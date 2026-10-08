<?php

namespace App\Http\Controllers\Purchasing;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Purchasing\StorePurchaseOrderRequest;
use App\Http\Requests\Purchasing\UpdatePurchaseOrderRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PurchaseOrderController extends Controller implements HasMiddleware
{
    public function __construct(private PurchaseOrderService $service) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:purchase-order.view', only: ['index', 'show']),
            new Middleware('permission:purchase-order.create', only: ['create', 'store']),
            new Middleware('permission:purchase-order.edit', only: ['edit', 'update', 'receive', 'cancel']),
            new Middleware('permission:purchase-order.approve', only: ['approve']),
            new Middleware('permission:purchase-order.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->search($request->query('q'))
            ->status($request->query('status'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('purchasing.purchase-orders.index', [
            'purchaseOrders' => $purchaseOrders,
            'statuses' => OrderStatus::options(),
        ]);
    }

    public function create(): View
    {
        return view('purchasing.purchase-orders.create', $this->formData());
    }

    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $order = $this->service->create($request->validated(), $request->user());

        return redirect()->route('admin.purchasing.purchase-orders.show', $order)
            ->with('success', __('Purchase order saved.'));
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'lines.product', 'creator', 'approver']);

        return view('purchasing.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder): View|RedirectResponse
    {
        if (! $purchaseOrder->status->isEditable()) {
            return redirect()->route('admin.purchasing.purchase-orders.show', $purchaseOrder)
                ->with('error', __('Only draft orders can be edited.'));
        }

        $purchaseOrder->load('lines');

        return view('purchasing.purchase-orders.edit', $this->formData() + compact('purchaseOrder'));
    }

    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->service->update($purchaseOrder, $request->validated());

        return redirect()->route('admin.purchasing.purchase-orders.show', $purchaseOrder)
            ->with('success', __('Purchase order updated.'));
    }

    public function approve(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->service->approve($purchaseOrder, $request->user());

        return back()->with('success', __('Purchase order approved.'));
    }

    public function receive(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->service->receive($purchaseOrder);

        return back()->with('success', __('Goods received and stock updated.'));
    }

    public function cancel(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->service->cancel($purchaseOrder);

        return back()->with('success', __('Purchase order cancelled.'));
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->service->delete($purchaseOrder);

        return redirect()->route('admin.purchasing.purchase-orders.index')
            ->with('success', __('Purchase order deleted.'));
    }

    private function formData(): array
    {
        return [
            'suppliers' => Supplier::orderBy('name')->pluck('name', 'id'),
            'products' => Product::orderBy('product_name')->get(['id', 'product_code', 'product_name', 'price']),
        ];
    }
}
