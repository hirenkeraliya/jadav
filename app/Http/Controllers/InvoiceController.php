<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\PaymentType;
use App\Models\Project;
use App\Models\TermsTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    use ScopedToCompany;

    public function index(Request $request): View
    {
        $cid = $this->companyId();
        $query = Invoice::where('company_id', $cid)->with(['customer', 'project']);

        if ($s = $request->input('search')) {
            $query->where(fn($q) => $q->where('invoice_number', 'like', "%{$s}%"));
        }
        if ($status = $request->input('status')) $query->where('status', $status);

        $invoices = $query->latest()->paginate(15)->withQueryString();
        return view('invoices.index', compact('invoices'));
    }

    public function create(Request $request): View
    {
        $cid = $this->companyId();
        $customers = Customer::where('company_id', $cid)->where('status', 'active')->get();
        $projects  = Project::where('company_id', $cid)->get();
        $terms     = TermsTemplate::where('company_id', $cid)->get();
        $company   = $this->company();
        $selectedProject = $request->input('project_id') ? Project::find($request->input('project_id')) : null;
        return view('invoices.create', compact('customers', 'projects', 'terms', 'company', 'selectedProject'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cid = $this->companyId();
        $data = $this->validateInvoice($request);

        $invoice = Invoice::create(array_merge($data, [
            'company_id'     => $cid,
            'invoice_number' => Invoice::generateNumber($cid),
            'status'         => 'draft',
        ]));

        $this->syncItems($invoice, $request->input('items', []));
        $invoice->recalculate();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice): View
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['customer', 'project', 'items', 'payments.paymentType', 'termsTemplate']);
        $company = $this->company();
        return view('invoices.show', compact('invoice', 'company'));
    }

    public function edit(Invoice $invoice): View
    {
        $this->authorizeInvoice($invoice);
        $cid = $this->companyId();
        $customers = Customer::where('company_id', $cid)->where('status', 'active')->get();
        $projects  = Project::where('company_id', $cid)->get();
        $terms     = TermsTemplate::where('company_id', $cid)->get();
        $company   = $this->company();
        $invoice->load('items');
        return view('invoices.edit', compact('invoice', 'customers', 'projects', 'terms', 'company'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);
        $data = $this->validateInvoice($request);
        $invoice->update($data);
        $this->syncItems($invoice, $request->input('items', []));
        $invoice->recalculate();
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function addPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        $data = $request->validate([
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'payment_date'    => ['required', 'date'],
            'payment_type_id' => ['nullable', 'exists:payment_types,id'],
            'reference_number'=> ['nullable', 'string'],
            'notes'           => ['nullable', 'string'],
        ]);

        InvoicePayment::create(array_merge($data, [
            'invoice_id'  => $invoice->id,
            'recorded_by' => auth()->id(),
        ]));

        $invoice->recalculate();

        return back()->with('success', 'Payment recorded.');
    }

    public function deletePayment(Invoice $invoice, InvoicePayment $payment): RedirectResponse
    {
        $this->authorizeInvoice($invoice);
        abort_if($payment->invoice_id !== $invoice->id, 403);
        $payment->delete();
        $invoice->recalculate();
        return back()->with('success', 'Payment removed.');
    }

    public function pdf(Invoice $invoice): Response
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['customer', 'items', 'payments', 'termsTemplate']);
        $company = $this->company();
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice', 'company'));
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }

    private function validateInvoice(Request $request): array
    {
        return $request->validate([
            'customer_id'       => ['required', 'exists:customers,id'],
            'project_id'        => ['nullable', 'exists:projects,id'],
            'invoice_date'      => ['required', 'date'],
            'due_date'          => ['nullable', 'date'],
            'discount_type'     => ['required', 'in:percentage,fixed'],
            'discount_value'    => ['nullable', 'numeric', 'min:0'],
            'tax_label'         => ['nullable', 'string'],
            'tax_rate'          => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes'             => ['nullable', 'string'],
            'terms_template_id' => ['nullable', 'exists:terms_templates,id'],
            'template'          => ['nullable', 'string'],
        ]);
    }

    private function syncItems(Invoice $invoice, array $items): void
    {
        $invoice->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['name'])) continue;
            $qty      = (float) ($item['qty'] ?? 1);
            $rate     = (float) ($item['unit_rate'] ?? 0);
            $taxRate  = (float) ($item['tax_rate'] ?? 0);
            $amount   = $qty * $rate;
            $taxAmt   = $amount * ($taxRate / 100);
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'name'        => $item['name'],
                'description' => $item['description'] ?? null,
                'qty'         => $qty,
                'unit'        => $item['unit'] ?? null,
                'unit_rate'   => $rate,
                'tax_rate'    => $taxRate,
                'tax_amount'  => $taxAmt,
                'amount'      => $amount,
                'sort_order'  => $i,
            ]);
        }
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        abort_if($invoice->company_id !== $this->companyId(), 403);
    }
}
