<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreCustomerRequest;
use App\Http\Requests\Master\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:customer.view', only: ['index']),
            new Middleware('permission:customer.create', only: ['create', 'store']),
            new Middleware('permission:customer.edit', only: ['edit', 'update']),
            new Middleware('permission:customer.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $customers = Customer::search($request->query('q'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('master.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('master.customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated());

        return redirect()->route('admin.master.customers.index')->with('success', __('Customer saved.'));
    }

    public function edit(Customer $customer): View
    {
        return view('master.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('admin.master.customers.index')->with('success', __('Customer updated.'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->salesOrders()->exists()) {
            return back()->with('error', __('Customer has sales orders and cannot be deleted.'));
        }

        $customer->delete();

        return redirect()->route('admin.master.customers.index')->with('success', __('Customer deleted.'));
    }
}
