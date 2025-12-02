<?php

use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\SystemController;
use Illuminate\Support\Facades\Route;

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {

    // Video Sessions
    Route::prefix('sessions')->group(function () {
        Route::post('/', [SessionController::class, 'store']);
        Route::get('/', [SessionController::class, 'index']);
        Route::get('/{id}', [SessionController::class, 'show']);
        Route::delete('/{id}', [SessionController::class, 'destroy']);

        // Status
        Route::get('/{id}/status', [SessionController::class, 'status']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::post('/generate', [ReportController::class, 'store']);

        Route::prefix('sessions/{sessionId}')->group(function () {
            Route::get('/analytics', [ReportController::class, 'analytics']);
            Route::get('/heatmap', [ReportController::class, 'heatmap']);
            Route::get('/detection-stats', [ReportController::class, 'detectionStats']);
            Route::get('/summary', [ReportController::class, 'summary']);
        });
    });

    // Default
    Route::prefix('system')->group(function () {
        // System endpoints - проксируют запросы к FastAPI микросервису
        Route::get('/', [SystemController::class, 'root']);
        Route::get('/health', [SystemController::class, 'health']);
        Route::get('/info', [SystemController::class, 'info']);
        Route::get('/metrics', [SystemController::class, 'metrics']);
    });

});

