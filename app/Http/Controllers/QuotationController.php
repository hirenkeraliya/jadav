<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\TermsTemplate;
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
        return view('quotations.index', compact('quotations'));
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
        return view('quotations.show', compact('quotation', 'company'));
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
        $revision->status = 'draft';
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

        $request->validate([
            'project_name'         => ['nullable', 'string', 'max:255'],
            'customer_organization' => ['nullable', 'string', 'max:255'],
            'customer_email'        => ['nullable', 'email', 'max:255'],
            'customer_mobile'       => ['nullable', 'string', 'max:20'],
            'customer_address'      => ['nullable', 'string'],
        ]);

        // Update customer details if provided
        $customerUpdates = array_filter([
            'organization' => $request->input('customer_organization'),
            'email'        => $request->input('customer_email'),
            'mobile'       => $request->input('customer_mobile'),
            'address'      => $request->input('customer_address'),
        ]);
        if ($customerUpdates) {
            $quotation->customer->update($customerUpdates);
        }

        $count = Project::where('company_id', $cid)->withTrashed()->count() + 1;
        $code  = 'PRJ-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $project = Project::create([
            'company_id'       => $cid,
            'project_code'     => $code,
            'name'             => $request->input('project_name') ?: ($quotation->customer->name . ' Project'),
            'customer_id'      => $quotation->customer_id,
            'estimated_amount' => $quotation->total,
            'status'           => 'pending',
            'priority'         => 'medium',
            'quotation_id'     => $quotation->id,
        ]);

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
        return $request->validate([
            'customer_id'       => ['required', 'exists:customers,id'],
            'date'              => ['required', 'date'],
            'valid_until'       => ['nullable', 'date'],
            'discount_type'     => ['required', 'in:percentage,fixed'],
            'discount_value'    => ['nullable', 'numeric', 'min:0'],
            'tax_label'         => ['nullable', 'string'],
            'tax_rate'          => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes'             => ['nullable', 'string'],
            'status'            => ['required', 'in:draft,sent,accepted,rejected,expired,converted'],
            'terms_template_id' => ['nullable', 'exists:terms_templates,id'],
        ]);
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
