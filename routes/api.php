<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PingController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UnitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;

Route::get('/ping', PingController::class);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// قراءة الكتالوج بدون توكن (للتطبيق على الشبكة المحلية، الإضافة تتطلب Sanctum)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/units', [UnitController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::post('/units', [UnitController::class, 'store']);
    Route::get('/units/{unit}', [UnitController::class, 'show']);
    Route::put('/units/{unit}', [UnitController::class, 'update']);
    Route::delete('/units/{unit}', [UnitController::class, 'destroy']);

    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy']);

    Route::get('/purchases', [PurchaseController::class, 'index']);
    Route::post('/purchases', [PurchaseController::class, 'store']);
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show']);
    Route::put('/purchases/{purchase}', [PurchaseController::class, 'update']);
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy']);

    Route::get('/returns', [ReturnController::class, 'index']);
    Route::post('/returns', [ReturnController::class, 'store']);
    Route::get('/returns/{warehouse_return}', [ReturnController::class, 'show']);
    Route::put('/returns/{warehouse_return}', [ReturnController::class, 'update']);
    Route::delete('/returns/{warehouse_return}', [ReturnController::class, 'destroy']);

    Route::post('/sync/push', [SyncController::class, 'push']);
    Route::post('/sync/pull', [SyncController::class, 'pull']);
});
