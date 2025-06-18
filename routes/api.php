<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\AvatarController;

// API Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', [AuthController::class, 'logout']);

    // Email verification routes
    Route::prefix('email')->group(function () {
        Route::post('/send-verification-code', [EmailVerificationController::class, 'sendVerificationCode'])
            ->middleware('throttle:3,1'); // Allow 3 attempts per minute

        Route::post('/verify-code', [EmailVerificationController::class, 'verifyCode'])
            ->middleware('throttle:5,1'); // Allow 5 verification attempts per minute

        Route::get('/verification-status', [EmailVerificationController::class, 'checkStatus']);
    });

    Route::post('/avatar/upload', [AvatarController::class, 'upload']);
    Route::delete('/avatar', [AvatarController::class, 'delete']);
    Route::get('/avatar', [AvatarController::class, 'show']);
    Route::get('/avatar/{userId}', [AvatarController::class, 'show']);
});
