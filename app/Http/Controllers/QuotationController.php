<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\TermsTemplate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class QuotationController extends Controller
{
    use ScopedToCompany;

    public function index(Request $request): View
    {
        $cid = $this->companyId();
        $query = Quotation::where('company_id', $cid)->whereNull('parent_id')->with('customer');

        if ($s = $request->input('search')) {
            $query->where(fn($q) => $q->where('quotation_number', 'like', "%{$s}%"));
        }
        if ($status = $request->input('status')) $query->where('status', $status);

        $quotations = $query->latest()->paginate(15)->withQueryString();

        // Stats across all root quotations for this company (unfiltered, so the cards stay stable).
        $statsBase = Quotation::where('company_id', $cid)->whereNull('parent_id');
        $totalQuotations   = (clone $statsBase)->count();
        $pendingQuotations = (clone $statsBase)->where('status', 'sent')->count();
        $convertedCount    = (clone $statsBase)->where('status', 'converted')->count();
        $totalAmount       = (clone $statsBase)->sum('total');
        $conversionRatio   = $totalQuotations > 0 ? round(($convertedCount / $totalQuotations) * 100, 1) : 0;

        return view('quotations.index', compact(
            'quotations', 'totalQuotations', 'pendingQuotations', 'totalAmount', 'conversionRatio'
        ));
    }

    public function create(): View
    {
        $cid = $this->companyId();
        $customers = Customer::where('company_id', $cid)->where('status', 'active')->get();
        $terms = TermsTemplate::where('company_id', $cid)->get();
        $company = $this->company();
        return view('quotations.create', compact('customers', 'terms', 'company'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cid = $this->companyId();
        $data = $this->validateQuotation($request);

        $quotation = Quotation::create(array_merge($data, [
            'company_id'       => $cid,
            'quotation_number' => Quotation::generateNumber($cid),
            'version'          => 1,
        ]));

        $this->syncItems($quotation, $request->input('items', []));
        $quotation->recalculate();

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation created.');
    }

    public function show(Quotation $quotation): View
    {
        $this->authorize($quotation);
        $quotation->load(['customer', 'items', 'termsTemplate', 'revisions.revisor', 'project', 'revisor']);
        $company = $this->company();
        $cid = $this->companyId();

        $projectTypes = ProjectType::where('company_id', $cid)->where('is_active', true)->get();
        $users        = User::whereHas('companies', fn($q) => $q->where('companies.id', $cid))->get();
        $count        = Project::where('company_id', $cid)->withTrashed()->count() + 1;
        $suggestedCode = 'PRJ-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('quotations.show', compact('quotation', 'company', 'projectTypes', 'users', 'suggestedCode'));
    }

    public function edit(Quotation $quotation): View
    {
        $this->authorize($quotation);
        $cid = $this->companyId();
        $customers = Customer::where('company_id', $cid)->where('status', 'active')->get();
        $terms = TermsTemplate::where('company_id', $cid)->get();
        $company = $this->company();
        $quotation->load('items');
        return view('quotations.edit', compact('quotation', 'customers', 'terms', 'company'));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize($quotation);
        $data = $this->validateQuotation($request);
        $quotation->update(array_merge($data, ['revised_by' => auth()->id()]));
        $this->syncItems($quotation, $request->input('items', []));
        $quotation->recalculate();
        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated.');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $this->authorize($quotation);
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    public function revise(Quotation $quotation): RedirectResponse
    {
        $this->authorize($quotation);
        $cid = $this->companyId();

        $revision = $quotation->replicate();
        $revision->parent_id = $quotation->parent_id ?? $quotation->id;
        $revision->version = Quotation::where('company_id', $cid)
            ->where(fn($q) => $q->where('id', $revision->parent_id)->orWhere('parent_id', $revision->parent_id))
            ->count() + 1;
        $revision->quotation_number = $quotation->quotation_number . '-R' . $revision->version;
        $revision->status = 'sent';
        $revision->revised_by = auth()->id();
        $revision->save();

        foreach ($quotation->items as $item) {
            $revision->items()->create($item->only(['name', 'description', 'qty', 'unit', 'unit_rate', 'amount', 'sort_order']));
        }

        return redirect()->route('quotations.edit', $revision)->with('success', 'Revision created. Edit and send when ready.');
    }

    public function convertToProject(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize($quotation);
        $cid = $this->companyId();

        $data = $request->validate([
            'project_code'          => ['required', 'string', 'max:50', 'unique:projects,project_code'],
            'project_name'          => ['required', 'string', 'max:255'],
            'project_type_ids'      => ['nullable', 'array'],
            'project_type_ids.*'    => ['exists:project_types,id'],
            'start_date'            => ['nullable', 'date'],
            'end_date'              => ['nullable', 'date', 'after_or_equal:start_date'],
            'lead_by'               => ['nullable', 'exists:users,id'],
            'priority'              => ['required', 'in:low,medium,high'],
            'status'                => ['nullable', 'in:quotation,running,on_hold,delayed,completed,cancelled'],
            'location'              => ['nullable', 'string', 'max:255'],
            'scope_of_work'         => ['nullable', 'string'],
            'internal_notes'        => ['nullable', 'string'],
            'customer_organization' => ['nullable', 'string', 'max:255'],
            'customer_email'        => ['nullable', 'email', 'max:255'],
            'customer_mobile'       => ['nullable', 'string', 'max:20'],
            'customer_address'      => ['nullable', 'string'],
        ]);

        // Update customer details if provided
        $customerUpdates = array_filter([
            'organization' => $data['customer_organization'] ?? null,
            'email'        => $data['customer_email'] ?? null,
            'mobile'       => $data['customer_mobile'] ?? null,
            'address'      => $data['customer_address'] ?? null,
        ]);
        if ($customerUpdates) {
            $quotation->customer->update($customerUpdates);
        }

        $typeIds = $data['project_type_ids'] ?? [];

        $project = Project::create([
            'company_id'       => $cid,
            'project_code'     => $data['project_code'],
            'name'             => $data['project_name'],
            'customer_id'      => $quotation->customer_id,
            'status'           => $data['status'] ?? 'running',
            'priority'         => $data['priority'],
            'start_date'       => $data['start_date'] ?? null,
            'end_date'         => $data['end_date'] ?? null,
            'lead_by'          => $data['lead_by'] ?? null,
            'location'         => $data['location'] ?? null,
            'scope_of_work'    => $data['scope_of_work'] ?? null,
            'internal_notes'   => $data['internal_notes'] ?? null,
            'quotation_id'     => $quotation->id,
        ]);

        $project->projectTypes()->sync($typeIds);
        $quotation->update(['status' => 'converted']);

        return redirect()->route('projects.show', $project)->with('success', 'Quotation converted to project successfully.');
    }

    public function pdf(Quotation $quotation): Response
    {
        $this->authorize($quotation);
        $quotation->load(['customer', 'items', 'termsTemplate', 'company']);
        $company = $this->company();
        $pdf = Pdf::loadView('pdf.quotation', compact('quotation', 'company'));
        return $pdf->stream('quotation-' . $quotation->quotation_number . '.pdf');
    }

    private function validateQuotation(Request $request): array
    {
        $data = $request->validate([
            'customer_id'       => ['required', 'exists:customers,id'],
            'date'              => ['required', 'date'],
            'valid_until'       => ['nullable', 'date'],
            'discount_type'     => ['required', 'in:percentage,fixed'],
            'discount_value'    => ['nullable', 'numeric', 'min:0'],
            'tax_label'         => ['nullable', 'string'],
            'tax_rate'          => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes'             => ['nullable', 'string'],
            'status'            => ['required', 'in:sent,rejected,converted'],
            'terms_template_id' => ['nullable', 'exists:terms_templates,id'],
        ]);

        // Columns are NOT NULL with default 0 — coerce blanks so saving without a discount/tax works.
        $data['discount_value'] = $data['discount_value'] ?? 0;
        $data['tax_rate']       = $data['tax_rate'] ?? 0;

        return $data;
    }

    private function syncItems(Quotation $quotation, array $items): void
    {
        $quotation->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['name'])) continue;
            $qty  = (float) ($item['qty'] ?? 1);
            $rate = (float) ($item['unit_rate'] ?? 0);
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'name'         => $item['name'],
                'description'  => $item['description'] ?? null,
                'qty'          => $qty,
                'unit'         => $item['unit'] ?? null,
                'unit_rate'    => $rate,
                'amount'       => $qty * $rate,
                'sort_order'   => $i,
            ]);
        }
    }

    private function authorize(Quotation $quotation): void
    {
        abort_if($quotation->company_id !== $this->companyId(), 403);
    }
}
