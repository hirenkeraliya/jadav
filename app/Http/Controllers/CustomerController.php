<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    use ScopedToCompany;

    public function index(Request $request): View
    {
        $query = Customer::where('company_id', $this->companyId());

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $customers = $query->withCount('projects')->latest()->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_code' => ['nullable', 'string', 'max:50', Rule::unique('customers', 'customer_code')],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['nullable', 'email', 'max:255'],
            'mobile'       => ['nullable', 'string', 'max:20'],
            'address'      => ['nullable', 'string'],
            'organization' => ['nullable', 'string', 'max:255'],
            'source'       => ['nullable', 'string', 'max:100'],
            'notes'        => ['nullable', 'string'],
            'status'       => ['required', 'in:active,inactive'],
        ]);

        $customer = Customer::create(array_merge($data, ['company_id' => $this->companyId()]));

        return redirect()->route('customers.show', $customer)->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $this->authorizeCompany($customer);
        $customer->load(['projects.financeEntries', 'invoices']);

        $ledger = $customer->projects->map(function ($project) {
            return [
                'project'  => $project,
                'received' => $project->financeEntries->where('type', 'credit')->sum('amount'),
                'expense'  => $project->financeEntries->where('type', 'debit')->sum('amount'),
            ];
        });

        return view('customers.show', compact('customer', 'ledger'));
    }

    public function edit(Customer $customer): View
    {
        $this->authorizeCompany($customer);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorizeCompany($customer);

        $data = $request->validate([
            'customer_code' => ['nullable', 'string', 'max:50', Rule::unique('customers', 'customer_code')->ignore($customer->id)],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['nullable', 'email', 'max:255'],
            'mobile'       => ['nullable', 'string', 'max:20'],
            'address'      => ['nullable', 'string'],
            'organization' => ['nullable', 'string', 'max:255'],
            'source'       => ['nullable', 'string', 'max:100'],
            'notes'        => ['nullable', 'string'],
            'status'       => ['required', 'in:active,inactive'],
        ]);

        $customer->update($data);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorizeCompany($customer);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }

    private function authorizeCompany(Customer $customer): void
    {
        abort_if($customer->company_id !== $this->companyId(), 403);
    }
}
