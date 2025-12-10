<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ========================
// AUTH ROUTES (Public)
// ========================
Route::prefix('auth')->group(function () {
    // Đăng ký & Đăng nhập thông thường
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Google OAuth
    Route::get('/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/google/token', [AuthController::class, 'loginWithGoogleToken']);
    Route::post('/google/code', [AuthController::class, 'loginWithGoogleCode']);
});

// ========================
// PROTECTED ROUTES (Cần đăng nhập)
// ========================
Route::middleware('auth:sanctum')->group(function () {
    // Thông tin người dùng
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
});
