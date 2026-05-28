<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\FinanceEntry;
use App\Models\FinanceEntryType;
use App\Models\PaymentType;
use App\Models\Project;
use App\Models\ProjectCompletion;
use App\Models\ProjectCompletionPayment;
use App\Models\TermsTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ProjectCompletionController extends Controller
{
    use ScopedToCompany;

    public function create(Project $project): View
    {
        $this->authorizeProject($project);
        abort_unless(auth()->user()->can('projects.change_status'), 403);
        abort_if($project->status !== 'running', 422, 'Project is not running.');
        abort_if($project->completion()->exists(), 422, 'Project already has a completion invoice.');

        $project->load(['customer', 'projectTypes']);
        $cid = $this->companyId();

        $financeEntries = $project->financeEntries()->with(['entryType', 'paymentType'])->latest('date')->get();
        $totalReceived  = $financeEntries->where('type', 'credit')->sum('amount');
        $totalExpense   = $financeEntries->where('type', 'debit')->sum('amount');

        $entryTypes   = FinanceEntryType::where('company_id', $cid)->where('is_active', true)->get();
        $paymentTypes = PaymentType::where('company_id', $cid)->where('is_active', true)->get();
        $suggestedInvoiceNumber = ProjectCompletion::generateNumber($cid);
        $termsTemplates  = TermsTemplate::where('company_id', $cid)->orderBy('name')->get();
        $defaultTermsId  = $termsTemplates->firstWhere('is_default_invoice', true)?->id;

        return view('projects.complete', compact(
            'project', 'financeEntries', 'totalReceived', 'totalExpense',
            'entryTypes', 'paymentTypes', 'suggestedInvoiceNumber',
            'termsTemplates', 'defaultTermsId'
        ));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_unless(auth()->user()->can('projects.change_status'), 403);
        abort_if($project->status !== 'running', 422, 'Project is not running.');
        abort_if($project->completion()->exists(), 422, 'Completion invoice already exists.');

        $cid = $this->companyId();

        $request->validate([
            'invoice_number'         => ['required', 'string', 'max:50', 'unique:project_completions,invoice_number'],
            'notes'                  => ['nullable', 'string'],
            'terms_template_id'      => ['nullable', 'exists:terms_templates,id'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.description'    => ['required', 'string', 'max:500'],
            'items.*.qty'            => ['required', 'numeric', 'min:0.01'],
            'items.*.rate'           => ['required', 'numeric', 'min:0'],
            // Final expenses (optional)
            'expenses.*.type_id'     => ['nullable', 'exists:finance_entry_types,id'],
            'expenses.*.payment_id'  => ['nullable', 'exists:payment_types,id'],
            'expenses.*.amount'      => ['nullable', 'numeric', 'min:0.01'],
            'expenses.*.date'        => ['nullable', 'date'],
            'expenses.*.remarks'     => ['nullable', 'string'],
        ]);

        // Save any final expense entries
        foreach ($request->input('expenses', []) as $exp) {
            if (empty($exp['amount']) || $exp['amount'] <= 0) continue;
            FinanceEntry::create([
                'company_id'            => $cid,
                'project_id'            => $project->id,
                'type'                  => 'debit',
                'finance_entry_type_id' => $exp['type_id'] ?? null,
                'payment_type_id'       => $exp['payment_id'] ?? null,
                'amount'                => $exp['amount'],
                'date'                  => $exp['date'] ?? now()->toDateString(),
                'remarks'               => $exp['remarks'] ?? null,
                'recorded_by'           => auth()->id(),
            ]);
        }

        // Create the completion record
        $completion = ProjectCompletion::create([
            'project_id'        => $project->id,
            'company_id'        => $cid,
            'invoice_number'    => $request->invoice_number,
            'notes'             => $request->notes,
            'terms_template_id' => $request->terms_template_id,
            'created_by'        => auth()->id(),
        ]);

        foreach ($request->input('items', []) as $i => $item) {
            if (empty($item['description'])) continue;
            $qty  = (float) $item['qty'];
            $rate = (float) $item['rate'];
            $completion->items()->create([
                'description' => $item['description'],
                'qty'         => $qty,
                'rate'        => $rate,
                'amount'      => $qty * $rate,
                'sort_order'  => $i,
            ]);
        }

        $completion->recalculate();
        $project->update(['status' => 'completed']);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project completed. Invoice ' . $completion->invoice_number . ' created.');
    }

    public function edit(Project $project): View
    {
        $this->authorizeProject($project);
        abort_unless($project->completion, 404);

        $completion = $project->completion->load('items');
        $termsTemplates = TermsTemplate::where('company_id', $this->companyId())->orderBy('name')->get();
        return view('projects.completion-edit', compact('project', 'completion', 'termsTemplates'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);
        $completion = $project->completion;
        abort_unless($completion, 404);

        $request->validate([
            'notes'               => ['nullable', 'string'],
            'terms_template_id'   => ['nullable', 'exists:terms_templates,id'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.qty'         => ['required', 'numeric', 'min:0.01'],
            'items.*.rate'        => ['required', 'numeric', 'min:0'],
        ]);

        $completion->update([
            'notes'             => $request->notes,
            'terms_template_id' => $request->terms_template_id,
        ]);
        $completion->items()->delete();

        foreach ($request->input('items', []) as $i => $item) {
            if (empty($item['description'])) continue;
            $qty  = (float) $item['qty'];
            $rate = (float) $item['rate'];
            $completion->items()->create([
                'description' => $item['description'],
                'qty'         => $qty,
                'rate'        => $rate,
                'amount'      => $qty * $rate,
                'sort_order'  => $i,
            ]);
        }

        $completion->recalculate();

        return redirect()->route('projects.show', $project)->with('success', 'Invoice updated.');
    }

    public function storePayment(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_unless(auth()->user()->can('finance.create'), 403);
        $completion = $project->completion;
        abort_unless($completion, 404);

        $request->validate([
            'amount'    => ['required', 'numeric', 'min:0.01'],
            'date'      => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes'     => ['nullable', 'string'],
        ]);

        ProjectCompletionPayment::create([
            'completion_id' => $completion->id,
            'amount'        => $request->amount,
            'date'          => $request->date,
            'reference'     => $request->reference,
            'notes'         => $request->notes,
            'recorded_by'   => auth()->id(),
        ]);

        // Also record as a credit finance entry for the project ledger
        FinanceEntry::create([
            'company_id'  => $this->companyId(),
            'project_id'  => $project->id,
            'type'        => 'credit',
            'amount'      => $request->amount,
            'date'        => $request->date,
            'remarks'     => 'Invoice payment: ' . ($request->reference ?: $completion->invoice_number),
            'recorded_by' => auth()->id(),
        ]);

        $completion->recalculate();

        return back()->with('success', 'Payment of ' . number_format($request->amount, 2) . ' recorded.');
    }

    public function destroyPayment(Project $project, ProjectCompletionPayment $payment): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($payment->completion->project_id !== $project->id, 403);

        $payment->delete();
        $project->completion->recalculate();

        return back()->with('success', 'Payment removed.');
    }

    public function pdf(Project $project): Response
    {
        $this->authorizeProject($project);
        $completion = $project->completion;
        abort_unless($completion, 404);

        $completion->load(['items', 'payments.recorder', 'termsTemplate']);
        $project->load(['customer', 'projectTypes']);
        $company = $this->company();

        $financeEntries = $project->financeEntries()->latest('date')->get();
        $totalReceived  = $financeEntries->where('type', 'credit')->sum('amount');
        $totalExpense   = $financeEntries->where('type', 'debit')->sum('amount');

        $pdf = Pdf::loadView('pdf.project-invoice', compact(
            'project', 'completion', 'company', 'totalReceived', 'totalExpense'
        ))->setPaper('a4');

        return $pdf->stream('invoice-' . $completion->invoice_number . '.pdf');
    }

    private function authorizeProject(Project $project): void
    {
        abort_if($project->company_id !== $this->companyId(), 403);
    }
}
