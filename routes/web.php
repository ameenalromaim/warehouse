<?php
use App\Http\Controllers\Dashboard\ImportController;
use App\Http\Controllers\Dashboard\ProductDashboardController;
use App\Http\Controllers\Dashboard\PurchaseDashboardController;
use App\Http\Controllers\Dashboard\SupplierDashboardController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard.purchases');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard/purchases', [PurchaseDashboardController::class, 'index'])->name('dashboard.purchases');
    Route::get('/dashboard/purchases/export', [PurchaseDashboardController::class, 'export'])->name('dashboard.purchases.export');
    Route::get('/dashboard/purchases/{purchase}/export', [PurchaseDashboardController::class, 'exportOne'])->name('dashboard.purchases.export-one');
    Route::get('/dashboard/purchases/items/{purchaseitem}/export', [PurchaseDashboardController::class, 'exportItem'])->name('dashboard.purchases.export-item');
    Route::get('/dashboard/suppliers', [SupplierDashboardController::class, 'index'])->name('dashboard.suppliers');
    Route::put('/dashboard/suppliers/{supplier}', [SupplierDashboardController::class, 'update'])->name('dashboard.suppliers.update');
    Route::delete('/dashboard/suppliers/{supplier}', [SupplierDashboardController::class, 'destroy'])->name('dashboard.suppliers.destroy');
    Route::post('/dashboard/suppliers/import', [ImportController::class, 'importSuppliers'])->name('dashboard.suppliers.import');
    Route::get('/dashboard/products', [ProductDashboardController::class, 'index'])->name('dashboard.products');
    Route::put('/dashboard/products/{product}', [ProductDashboardController::class, 'update'])->name('dashboard.products.update');
    Route::delete('/dashboard/products/{product}', [ProductDashboardController::class, 'destroy'])->name('dashboard.products.destroy');
    Route::post('/dashboard/products/import', [ImportController::class, 'importProducts'])->name('dashboard.products.import');
});