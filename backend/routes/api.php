<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Public routes (authentication)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Links
    Route::get('/links/trash', [LinkController::class, 'trash']);
    Route::post('/links/{link}/restore', [LinkController::class, 'restore']);
    Route::delete('/links/{link}/force', [LinkController::class, 'forceDelete']);
    Route::apiResource('/links', LinkController::class);

    // Stats
    Route::get('/stats', [StatsController::class, 'index']);
    Route::get('/stats/detailed', [StatsController::class, 'detailed']);
});
