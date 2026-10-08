<?php

namespace App\Http\Controllers\Sales;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreSalesOrderRequest;
use App\Http\Requests\Sales\UpdateSalesOrderRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Services\SalesOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SalesOrderController extends Controller implements HasMiddleware
{
    public function __construct(private SalesOrderService $service) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:sales-order.view', only: ['index', 'show']),
            new Middleware('permission:sales-order.create', only: ['create', 'store']),
            new Middleware('permission:sales-order.edit', only: ['edit', 'update', 'deliver', 'cancel']),
            new Middleware('permission:sales-order.approve', only: ['approve']),
            new Middleware('permission:sales-order.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $salesOrders = SalesOrder::with('customer')
            ->search($request->query('q'))
            ->status($request->query('status'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.sales-orders.index', [
            'salesOrders' => $salesOrders,
            'statuses' => OrderStatus::options(),
        ]);
    }

    public function create(): View
    {
        return view('sales.sales-orders.create', $this->formData());
    }

    public function store(StoreSalesOrderRequest $request): RedirectResponse
    {
        $order = $this->service->create($request->validated(), $request->user());

        return redirect()->route('admin.sales.sales-orders.show', $order)
            ->with('success', __('Sales order saved.'));
    }

    public function show(SalesOrder $salesOrder): View
    {
        $salesOrder->load(['customer', 'lines.product', 'creator', 'approver']);

        return view('sales.sales-orders.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder): View|RedirectResponse
    {
        if (! $salesOrder->status->isEditable()) {
            return redirect()->route('admin.sales.sales-orders.show', $salesOrder)
                ->with('error', __('Only draft orders can be edited.'));
        }

        $salesOrder->load('lines');

        return view('sales.sales-orders.edit', $this->formData() + compact('salesOrder'));
    }

    public function update(UpdateSalesOrderRequest $request, SalesOrder $salesOrder): RedirectResponse
    {
        $this->service->update($salesOrder, $request->validated());

        return redirect()->route('admin.sales.sales-orders.show', $salesOrder)
            ->with('success', __('Sales order updated.'));
    }

    public function approve(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $this->service->approve($salesOrder, $request->user());

        return back()->with('success', __('Sales order approved.'));
    }

    public function deliver(SalesOrder $salesOrder): RedirectResponse
    {
        $this->service->deliver($salesOrder);

        return back()->with('success', __('Goods delivered and stock updated.'));
    }

    public function cancel(SalesOrder $salesOrder): RedirectResponse
    {
        $this->service->cancel($salesOrder);

        return back()->with('success', __('Sales order cancelled.'));
    }

    public function destroy(SalesOrder $salesOrder): RedirectResponse
    {
        $this->service->delete($salesOrder);

        return redirect()->route('admin.sales.sales-orders.index')
            ->with('success', __('Sales order deleted.'));
    }

    private function formData(): array
    {
        return [
            'customers' => Customer::orderBy('name')->pluck('name', 'id'),
            'products' => Product::orderBy('product_name')->get(['id', 'product_code', 'product_name', 'price', 'stock']),
        ];
    }
}
