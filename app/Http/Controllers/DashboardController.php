<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'counts' => [
                'products' => Product::count(),
                'suppliers' => Supplier::count(),
                'customers' => Customer::count(),
                'draftPurchaseOrders' => PurchaseOrder::where('status', OrderStatus::Draft)->count(),
                'draftSalesOrders' => SalesOrder::where('status', OrderStatus::Draft)->count(),
            ],
            'lowStockProducts' => Product::lowStock()->orderBy('stock')->limit(8)->get(),
            'recentPurchaseOrders' => PurchaseOrder::with('supplier')->latest()->limit(5)->get(),
            'recentSalesOrders' => SalesOrder::with('customer')->latest()->limit(5)->get(),
        ]);
    }
}
