<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Admin, Manager, Staff can view dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('role:Admin,Manager,Staff');
    
    // Only Admin and Staff can manage products and borrowings
    Route::middleware('role:Admin,Staff')->group(function () {
        Route::apiResource('products', ProductController::class);
        Route::apiResource('categories', CategoryController::class);
        
        Route::get('/borrowings', [BorrowingController::class, 'index']);
        Route::post('/borrowings', [BorrowingController::class, 'store']);
        Route::post('/borrowings/{borrowing}/return', [BorrowingController::class, 'return']);
    });
    
    // Only Admin can manage users
    Route::apiResource('users', UserController::class)->middleware('role:Admin');
});
