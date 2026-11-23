<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes - Brewly Cafe Ordering & Management System
|--------------------------------------------------------------------------
*/

// Pelanggan (Customer) Routes
Route::get('/', [CustomerController::class, 'index'])->name('home');
Route::get('/cart', [CustomerController::class, 'cart'])->name('customer.cart');
Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('customer.cart.add');
Route::post('/cart/update/{id}', [CustomerController::class, 'updateCart'])->name('customer.cart.update');
Route::delete('/cart/remove/{id}', [CustomerController::class, 'removeFromCart'])->name('customer.cart.remove');
Route::get('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
Route::post('/order/place', [CustomerController::class, 'placeOrder'])->name('customer.order.place');
Route::get('/order/track/{id}', [CustomerController::class, 'trackOrder'])->name('customer.track');
Route::post('/order/cancel/{id}', [CustomerController::class, 'cancelOrder'])->name('customer.order.cancel');
Route::get('/order/receipt/{id}', [CustomerController::class, 'receipt'])->name('customer.receipt');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/login/quick/{role}', [AuthController::class, 'quickLogin'])->name('login.quick');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Kasir (Cashier) Routes
Route::middleware(['auth', 'role:CASHIER,ADMIN'])->prefix('cashier')->group(function () {
    Route::get('/orders', [CashierController::class, 'orders'])->name('cashier.orders');
    Route::get('/orders/{id}', [CashierController::class, 'orderDetail'])->name('cashier.orders.detail');
    Route::post('/orders/{id}/pay', [CashierController::class, 'processPayment'])->name('cashier.orders.pay');
    Route::post('/payments/{paymentId}/receipt', [CashierController::class, 'issueReceipt'])->name('cashier.receipt.issue');
    Route::get('/receipt/{receiptId}/print', [CashierController::class, 'printReceipt'])->name('cashier.receipt.print');
    Route::post('/orders/{id}/status', [CashierController::class, 'updateOrderStatus'])->name('cashier.orders.status');
    Route::get('/transactions', [CashierController::class, 'transactions'])->name('cashier.transactions');
});

// Dapur (Kitchen) Routes
Route::middleware(['auth', 'role:KITCHEN,ADMIN'])->prefix('kitchen')->group(function () {
    Route::get('/', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::post('/orders/{id}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.orders.status');
});

// Admin Routes
Route::middleware(['auth', 'role:ADMIN'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Kelola Menu & Kategori (DFD 1.0)
    Route::get('/menus', [MenuController::class, 'index'])->name('admin.menus');
    Route::post('/menus', [MenuController::class, 'storeMenu'])->name('admin.menus.store');
    Route::put('/menus/{id}', [MenuController::class, 'updateMenu'])->name('admin.menus.update');
    Route::post('/menus/{id}/toggle', [MenuController::class, 'toggleAvailability'])->name('admin.menus.toggle');
    Route::delete('/menus/{id}', [MenuController::class, 'destroyMenu'])->name('admin.menus.delete');

    Route::get('/categories', [MenuController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [MenuController::class, 'storeCategory'])->name('admin.categories.store');
    Route::put('/categories/{id}', [MenuController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [MenuController::class, 'destroyCategory'])->name('admin.categories.delete');

    // Kelola Meja Kafe (D7 Meja Kafe)
    Route::get('/tables', [TableController::class, 'index'])->name('admin.tables');
    Route::post('/tables', [TableController::class, 'store'])->name('admin.tables.store');
    Route::put('/tables/{id}', [TableController::class, 'update'])->name('admin.tables.update');
    Route::post('/tables/{id}/toggle', [TableController::class, 'toggleStatus'])->name('admin.tables.toggle');
    Route::delete('/tables/{id}', [TableController::class, 'destroy'])->name('admin.tables.delete');

    // Kelola Staf (DFD 5.0)
    Route::get('/staff', [StaffController::class, 'index'])->name('admin.staff');
    Route::post('/staff', [StaffController::class, 'store'])->name('admin.staff.store');
    Route::put('/staff/{id}', [StaffController::class, 'update'])->name('admin.staff.update');
    Route::post('/staff/{id}/toggle', [StaffController::class, 'toggleActive'])->name('admin.staff.toggle');
    Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('admin.staff.delete');

    // Buat Laporan Penjualan (DFD 6.0)
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('admin.reports.print');
});
