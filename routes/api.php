<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return clone $request->user()->load('role');
    });

    // Master Data Barang
    Route::apiResource('products', ProductController::class);

    // Peminjaman
    Route::get('/borrowings', [BorrowingController::class, 'index']);
    Route::post('/borrowings', [BorrowingController::class, 'store']);
    Route::post('/borrowings/{borrowing}/return', [BorrowingController::class, 'return']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
