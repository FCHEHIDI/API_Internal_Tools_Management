<?php

use App\Http\Controllers\Api\ToolController;
use App\Http\Controllers\Api\AnalyticsController;
use Illuminate\Support\Facades\Route;

// Tools CRUD endpoints
Route::apiResource('tools', ToolController::class);

// Analytics endpoints
Route::prefix('analytics')->group(function () {
    Route::get('/department-costs', [AnalyticsController::class, 'departmentCosts']);
    Route::get('/expensive-tools', [AnalyticsController::class, 'expensiveTools']);
    Route::get('/tools-by-category', [AnalyticsController::class, 'toolsByCategory']);
    Route::get('/low-usage-tools', [AnalyticsController::class, 'lowUsageTools']);
    Route::get('/vendor-summary', [AnalyticsController::class, 'vendorSummary']);
});
