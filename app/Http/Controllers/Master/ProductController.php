<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreProductRequest;
use App\Http\Requests\Master\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:product.view', only: ['index']),
            new Middleware('permission:product.create', only: ['create', 'store']),
            new Middleware('permission:product.edit', only: ['edit', 'update']),
            new Middleware('permission:product.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $products = Product::search($request->query('q'))
            ->orderBy('product_name')
            ->paginate(10)
            ->withQueryString();

        return view('master.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('master.products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return redirect()->route('admin.master.products.index')->with('success', __('Product saved.'));
    }

    public function edit(Product $product): View
    {
        return view('master.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('admin.master.products.index')->with('success', __('Product updated.'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->isUsedInTransactions()) {
            return back()->with('error', __('Product is used in transactions and cannot be deleted.'));
        }

        $product->delete();

        return redirect()->route('admin.master.products.index')->with('success', __('Product deleted.'));
    }
}
