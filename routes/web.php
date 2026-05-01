<?php
use App\Http\Controllers\Dashboard\BranchDashboardController;
use App\Http\Controllers\Dashboard\ImportController;
use App\Http\Controllers\Dashboard\ProductDashboardController;
use App\Http\Controllers\Dashboard\PurchaseDashboardController;
use App\Http\Controllers\Dashboard\ReturnReportDashboardController;
use App\Http\Controllers\Dashboard\SupplierDashboardController;
use App\Http\Controllers\Dashboard\UserDashboardController;
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

    Route::get('/dashboard/reports/returns-normal', [ReturnReportDashboardController::class, 'normal'])->name('dashboard.reports.returns.normal');
    Route::get('/dashboard/reports/returns-damage', [ReturnReportDashboardController::class, 'damage'])->name('dashboard.reports.returns.damage');
    Route::get('/dashboard/reports/returns/export', [ReturnReportDashboardController::class, 'export'])->name('dashboard.reports.returns.export');
  
    Route::get('/dashboard/suppliers', [SupplierDashboardController::class, 'index'])->name('dashboard.suppliers');
    Route::put('/dashboard/suppliers/{supplier}', [SupplierDashboardController::class, 'update'])->name('dashboard.suppliers.update');
    Route::delete('/dashboard/suppliers/{supplier}', [SupplierDashboardController::class, 'destroy'])->name('dashboard.suppliers.destroy');
    Route::post('/dashboard/suppliers/import', [ImportController::class, 'importSuppliers'])->name('dashboard.suppliers.import');

    Route::get('/dashboard/products', [ProductDashboardController::class, 'index'])->name('dashboard.products');
    Route::put('/dashboard/products/{product}', [ProductDashboardController::class, 'update'])->name('dashboard.products.update');
    Route::delete('/dashboard/products/{product}', [ProductDashboardController::class, 'destroy'])->name('dashboard.products.destroy');
    Route::post('/dashboard/products/import', [ImportController::class, 'importProducts'])->name('dashboard.products.import');

    Route::get('/dashboard/users', [UserDashboardController::class, 'index'])->name('dashboard.users');
    Route::get('/dashboard/users/create', [UserDashboardController::class, 'create'])->name('dashboard.users.create');
    Route::post('/dashboard/users', [UserDashboardController::class, 'store'])->name('dashboard.users.store');
    Route::put('/dashboard/users/{user}', [UserDashboardController::class, 'update'])->name('dashboard.users.update');
    Route::delete('/dashboard/users/{user}', [UserDashboardController::class, 'destroy'])->name('dashboard.users.destroy');

    Route::get('/dashboard/branches', [BranchDashboardController::class, 'index'])->name('dashboard.branches');
    Route::get('/dashboard/branches/create', [BranchDashboardController::class, 'create'])->name('dashboard.branches.create');
    Route::post('/dashboard/branches', [BranchDashboardController::class, 'store'])->name('dashboard.branches.store');
    Route::put('/dashboard/branches/{user}', [BranchDashboardController::class, 'update'])->name('dashboard.branches.update');
    Route::delete('/dashboard/branches/{user}', [BranchDashboardController::class, 'destroy'])->name('dashboard.branches.destroy');
});