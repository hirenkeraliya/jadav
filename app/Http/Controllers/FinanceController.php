<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\FinanceEntry;
use App\Models\FinanceEntryType;
use App\Models\FinanceFile;
use App\Models\PaymentType;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    use ScopedToCompany;

    public function index(Project $project): View
    {
        $this->authorizeProject($project);
        $cid = $this->companyId();

        $entries = $project->financeEntries()->with(['entryType', 'paymentType', 'recorder', 'files'])->latest('date')->get();
        $paymentTypes = PaymentType::where('company_id', $cid)->where('is_active', true)->get();
        $entryTypes = FinanceEntryType::where('company_id', $cid)->where('is_active', true)->get();

        $totalCredit = $entries->where('type', 'credit')->sum('amount');
        $totalDebit  = $entries->where('type', 'debit')->sum('amount');
        $balance     = $totalCredit - $totalDebit;

        return view('finance.index', compact('project', 'entries', 'paymentTypes', 'entryTypes', 'totalCredit', 'totalDebit', 'balance'));
    }

    public function create(Project $project): View
    {
        $this->authorizeProject($project);
        $cid = $this->companyId();
        $paymentTypes = PaymentType::where('company_id', $cid)->where('is_active', true)->get();
        $entryTypes = FinanceEntryType::where('company_id', $cid)->where('is_active', true)->get();
        return view('finance.create', compact('project', 'paymentTypes', 'entryTypes'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);

        $data = $request->validate([
            'type'                  => ['required', 'in:debit,credit'],
            'finance_entry_type_id' => ['nullable', 'exists:finance_entry_types,id'],
            'payment_type_id'       => ['nullable', 'exists:payment_types,id'],
            'amount'                => ['required', 'numeric', 'min:0.01'],
            'date'                  => ['required', 'date'],
            'reference_number'      => ['nullable', 'string', 'max:100'],
            'remarks'               => ['nullable', 'string'],
            'attachments.*'         => ['nullable', 'file', 'max:10240'],
        ]);

        $entry = FinanceEntry::create(array_merge($data, [
            'company_id'  => $this->companyId(),
            'project_id'  => $project->id,
            'recorded_by' => auth()->id(),
        ]));

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store("finance/{$project->id}", 'public');
                FinanceFile::create([
                    'finance_entry_id' => $entry->id,
                    'original_name'    => $file->getClientOriginalName(),
                    'path'             => $path,
                    'mime_type'        => $file->getMimeType(),
                    'size'             => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('projects.show', $project)->with('success', ucfirst($data['type']) . ' entry added.');
    }

    public function edit(Project $project, FinanceEntry $entry): View
    {
        $this->authorizeProject($project);
        abort_if($entry->project_id !== $project->id, 403);
        $cid = $this->companyId();
        $paymentTypes = PaymentType::where('company_id', $cid)->where('is_active', true)->get();
        $entryTypes = FinanceEntryType::where('company_id', $cid)->where('is_active', true)->get();
        return view('finance.edit', compact('project', 'entry', 'paymentTypes', 'entryTypes'));
    }

    public function update(Request $request, Project $project, FinanceEntry $entry): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($entry->project_id !== $project->id, 403);

        $data = $request->validate([
            'type'                  => ['required', 'in:debit,credit'],
            'finance_entry_type_id' => ['nullable', 'exists:finance_entry_types,id'],
            'payment_type_id'       => ['nullable', 'exists:payment_types,id'],
            'amount'                => ['required', 'numeric', 'min:0.01'],
            'date'                  => ['required', 'date'],
            'reference_number'      => ['nullable', 'string', 'max:100'],
            'remarks'               => ['nullable', 'string'],
        ]);

        $entry->update($data);
        return redirect()->route('finance.index', $project)->with('success', 'Entry updated.');
    }

    public function destroy(Project $project, FinanceEntry $entry): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($entry->project_id !== $project->id, 403);
        $entry->files->each(fn($f) => \Storage::disk('public')->delete($f->path));
        $entry->delete();
        return back()->with('success', 'Entry deleted.');
    }

    private function authorizeProject(Project $project): void
    {
        abort_if($project->company_id !== $this->companyId(), 403);
    }
}
