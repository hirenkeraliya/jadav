<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\CustomField;
use App\Models\FinanceEntryType;
use App\Models\PaymentType;
use App\Models\ProjectType;
use App\Models\TermsTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    use ScopedToCompany;

    // ── Company ──────────────────────────────────────────────────────────────
    public function showCompany(): View
    {
        $company = $this->company();
        return view('settings.company', compact('company'));
    }

    public function updateCompany(Request $request): RedirectResponse
    {
        $company = $this->company();
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email'],
            'phone'            => ['nullable', 'string'],
            'address'          => ['nullable', 'string'],
            'logo'             => ['nullable', 'image', 'max:2048'],
            'qr_code'          => ['nullable', 'image', 'max:2048'],
            'remove_qr_code'   => ['nullable', 'boolean'],
            'primary_color'    => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary_color'  => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'currency'         => ['required', 'string', 'max:10'],
            'currency_symbol'  => ['required', 'string', 'max:5'],
            'tax_label'        => ['nullable', 'string'],
            'tax_number'       => ['nullable', 'string'],
            'website'          => ['nullable', 'url'],
            'invoice_prefix'   => ['required', 'string', 'max:20'],
            'quotation_prefix' => ['required', 'string', 'max:20'],
            'financial_year_start' => ['required', 'integer', 'between:1,12'],
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) Storage::disk('public')->delete($company->logo);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('qr_code')) {
            if ($company->qr_code) Storage::disk('public')->delete($company->qr_code);
            $data['qr_code'] = $request->file('qr_code')->store('qr-codes', 'public');
        } elseif ($request->boolean('remove_qr_code')) {
            if ($company->qr_code) Storage::disk('public')->delete($company->qr_code);
            $data['qr_code'] = null;
        }

        unset($data['remove_qr_code']);
        $company->update($data);
        return back()->with('success', 'Company settings updated.');
    }

    // ── Project Types ─────────────────────────────────────────────────────────
    public function projectTypes(): View
    {
        $types = ProjectType::where('company_id', $this->companyId())->get();
        return view('settings.project-types', compact('types'));
    }

    public function storeProjectType(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'color' => ['required', 'string']]);
        ProjectType::create(array_merge($data, ['company_id' => $this->companyId()]));
        return back()->with('success', 'Project type added.');
    }

    public function updateProjectType(Request $request, ProjectType $projectType): RedirectResponse
    {
        abort_if($projectType->company_id !== $this->companyId(), 403);
        $data = $request->validate(['name' => ['required', 'string'], 'color' => ['required', 'string'], 'is_active' => ['boolean']]);
        $projectType->update($data);
        return back()->with('success', 'Updated.');
    }

    public function destroyProjectType(ProjectType $projectType): RedirectResponse
    {
        abort_if($projectType->company_id !== $this->companyId(), 403);
        $projectType->delete();
        return back()->with('success', 'Deleted.');
    }

    // ── Payment Types ─────────────────────────────────────────────────────────
    public function paymentTypes(): View
    {
        $types = PaymentType::where('company_id', $this->companyId())->get();
        return view('settings.payment-types', compact('types'));
    }

    public function storePaymentType(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);
        PaymentType::create(array_merge($data, ['company_id' => $this->companyId()]));
        return back()->with('success', 'Payment type added.');
    }

    public function updatePaymentType(Request $request, PaymentType $paymentType): RedirectResponse
    {
        abort_if($paymentType->company_id !== $this->companyId(), 403);
        $paymentType->update($request->validate(['name' => ['required', 'string'], 'is_active' => ['boolean']]));
        return back()->with('success', 'Updated.');
    }

    public function destroyPaymentType(PaymentType $paymentType): RedirectResponse
    {
        abort_if($paymentType->company_id !== $this->companyId(), 403);
        $paymentType->delete();
        return back()->with('success', 'Deleted.');
    }

    // ── Finance Entry Types ───────────────────────────────────────────────────
    public function financeEntryTypes(): View
    {
        $types = FinanceEntryType::where('company_id', $this->companyId())->get();
        return view('settings.finance-entry-types', compact('types'));
    }

    public function storeFinanceEntryType(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'direction' => ['required', 'in:debit,credit,both']]);
        FinanceEntryType::create(array_merge($data, ['company_id' => $this->companyId()]));
        return back()->with('success', 'Entry type added.');
    }

    public function updateFinanceEntryType(Request $request, FinanceEntryType $financeEntryType): RedirectResponse
    {
        abort_if($financeEntryType->company_id !== $this->companyId(), 403);
        $financeEntryType->update($request->validate(['name' => ['required', 'string'], 'direction' => ['required', 'in:debit,credit,both'], 'is_active' => ['boolean']]));
        return back()->with('success', 'Updated.');
    }

    public function destroyFinanceEntryType(FinanceEntryType $financeEntryType): RedirectResponse
    {
        abort_if($financeEntryType->company_id !== $this->companyId(), 403);
        $financeEntryType->delete();
        return back()->with('success', 'Deleted.');
    }

    // ── Terms Templates ───────────────────────────────────────────────────────
    public function terms(): View
    {
        $terms = TermsTemplate::where('company_id', $this->companyId())->get();
        return view('settings.terms', compact('terms'));
    }

    public function storeTerms(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'content'              => ['required', 'string'],
            'is_default_quotation' => ['boolean'],
            'is_default_invoice'   => ['boolean'],
        ]);
        TermsTemplate::create(array_merge($data, ['company_id' => $this->companyId()]));
        return back()->with('success', 'Template created.');
    }

    public function updateTerms(Request $request, TermsTemplate $termsTemplate): RedirectResponse
    {
        abort_if($termsTemplate->company_id !== $this->companyId(), 403);
        $data = $request->validate([
            'name'                 => ['required', 'string'],
            'content'              => ['required', 'string'],
            'is_default_quotation' => ['boolean'],
            'is_default_invoice'   => ['boolean'],
        ]);
        $termsTemplate->update($data);
        return back()->with('success', 'Updated.');
    }

    public function destroyTerms(TermsTemplate $termsTemplate): RedirectResponse
    {
        abort_if($termsTemplate->company_id !== $this->companyId(), 403);
        $termsTemplate->delete();
        return back()->with('success', 'Deleted.');
    }

    // ── Custom Fields ─────────────────────────────────────────────────────────
    public function customFields(): View
    {
        $fields = CustomField::where('company_id', $this->companyId())->orderBy('module')->orderBy('sort_order')->get();
        return view('settings.custom-fields', compact('fields'));
    }

    public function storeCustomField(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'module'        => ['required', 'in:projects,customers,finance_entries'],
            'label'         => ['required', 'string', 'max:100'],
            'field_key'     => ['required', 'string', 'max:50', 'alpha_dash'],
            'type'          => ['required', 'in:text,textarea,number,date,toggle,select,multiselect,file,url,color'],
            'options'       => ['nullable', 'string'],
            'placeholder'   => ['nullable', 'string'],
            'default_value' => ['nullable', 'string'],
            'is_required'   => ['boolean'],
            'sort_order'    => ['integer'],
        ]);

        if (isset($data['options']) && is_string($data['options'])) {
            $data['options'] = array_filter(array_map('trim', explode("\n", $data['options'])));
        }

        CustomField::create(array_merge($data, ['company_id' => $this->companyId()]));
        return back()->with('success', 'Custom field added.');
    }

    public function updateCustomField(Request $request, CustomField $customField): RedirectResponse
    {
        abort_if($customField->company_id !== $this->companyId(), 403);
        $data = $request->validate([
            'label'       => ['required', 'string'],
            'placeholder' => ['nullable', 'string'],
            'is_required' => ['boolean'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['integer'],
        ]);
        $customField->update($data);
        return back()->with('success', 'Updated.');
    }

    public function destroyCustomField(CustomField $customField): RedirectResponse
    {
        abort_if($customField->company_id !== $this->companyId(), 403);
        $customField->delete();
        return back()->with('success', 'Deleted.');
    }
}
