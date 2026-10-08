<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Purchasing\PurchaseOrderController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('products', ProductController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
        Route::resource('customers', CustomerController::class)->except('show');
    });

    Route::prefix('purchasing')->name('purchasing.')->group(function () {
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::patch('purchase-orders/{purchase_order}/approve', [PurchaseOrderController::class, 'approve'])
            ->name('purchase-orders.approve');
        Route::patch('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])
            ->name('purchase-orders.receive');
        Route::patch('purchase-orders/{purchase_order}/cancel', [PurchaseOrderController::class, 'cancel'])
            ->name('purchase-orders.cancel');
    });

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::resource('sales-orders', SalesOrderController::class);
        Route::patch('sales-orders/{sales_order}/approve', [SalesOrderController::class, 'approve'])
            ->name('sales-orders.approve');
        Route::patch('sales-orders/{sales_order}/deliver', [SalesOrderController::class, 'deliver'])
            ->name('sales-orders.deliver');
        Route::patch('sales-orders/{sales_order}/cancel', [SalesOrderController::class, 'cancel'])
            ->name('sales-orders.cancel');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('roles', RoleController::class)->except('show');
    });
});
