<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Customer\StoreCustomerRequest;
use App\Http\Requests\Admin\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\CustomerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = CustomerProfile::with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt('password'),
        ]);

        $customerProfile = CustomerProfile::create([
            'customer_id' => $customer->id,
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'marketing_opt_in' => $validated['marketing_opt_in'] ?? false,
            'status' => $validated['status'] ?? 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer profile created successfully.');
    }

    public function show(CustomerProfile $customer): View
    {
        $customer->load(['customer.wallet', 'customer.addresses']);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(CustomerProfile $customer): View
    {
        $customer->load('customer');

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, CustomerProfile $customer): RedirectResponse
    {
        $validated = $request->validated();

        // Update the Customer record (name, email, phone)
        if ($customer->customer) {
            $customer->customer->update([
                'name' => $validated['name'] ?? $customer->customer->name,
                'email' => $validated['email'] ?? $customer->customer->email,
                'phone' => $validated['phone'] ?? $customer->customer->phone,
            ]);
        }

        // Update the CustomerProfile record
        $customer->update([
            'gender' => $validated['gender'] ?? $customer->gender,
            'date_of_birth' => $validated['date_of_birth'] ?? $customer->date_of_birth,
            'marketing_opt_in' => $validated['marketing_opt_in'] ?? $customer->marketing_opt_in,
            'status' => $validated['status'] ?? $customer->status,
            'notes' => $validated['notes'] ?? $customer->notes,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer profile updated successfully.');
    }

    public function destroy(CustomerProfile $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer profile deleted successfully.');
    }
}
