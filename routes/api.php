<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

// ✅ Public
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// ✅ Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'WhatsApp Clone API is running',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// ✅ Protected
Route::middleware('auth:sanctum')->group(function () {
    
    // 🔐 Auth Info
    Route::prefix('auth')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    });

    // 👤 Messages (Protected)
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']); // Get all conversations
        Route::post('/', [MessageController::class, 'store']); // Send message
        Route::get('/conversation/{userId}', [MessageController::class, 'getConversation']); // Get messages with user
        Route::put('/{id}/read', [MessageController::class, 'markAsRead']); // Mark as read
        Route::delete('/{id}', [MessageController::class, 'destroy']); // Delete
    });
        Route::get('/contacts', [\App\Http\Controllers\ContactController::class, 'index']);

});

// 🧱 Fallback
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'error' => 'The requested API endpoint does not exist'
    ], 404);
});
