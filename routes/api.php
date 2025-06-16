<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AvatarController;

// API Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('/avatar/upload', [AvatarController::class, 'upload']);
    Route::delete('/avatar', [AvatarController::class, 'delete']);
    Route::get('/avatar', [AvatarController::class, 'show']);
    Route::get('/avatar/{userId}', [AvatarController::class, 'show']);
});
