<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\API\OwnerApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/user/profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
        Route::get('/categories', [CategoryController::class, 'index']);
    });
});

// ROUTE NOTIFIKASI (di luar prefix 'auth' sehingga URL menjadi /api/notifications)
Route::middleware('auth:sanctum')->get('/notifications', [NotificationController::class, 'index']);

// ROUTE DASHBOARD (Sudah di luar grup prefix 'auth' sehingga URL menjadi /api/dashboard/summary)
Route::middleware('auth:sanctum')->get('/dashboard/summary', [DashboardController::class, 'summary']);

// Taruh di luar grup auth agar bisa diakses langsung ke /api/products
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::put('/products/{id}', [ProductController::class, 'update']);

// Route notifikasi — diakses langsung ke /api/notifications
Route::get('/notifications', [NotificationController::class, 'index']);

Route::prefix('owner')->group(function () {
    Route::get('/dashboard', [OwnerApiController::class, 'getDashboardData']);
    Route::get('/stock', [OwnerApiController::class, 'getStockData']);
    Route::get('/finance', [OwnerApiController::class, 'getFinancialData']);
});
