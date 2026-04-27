<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\DepenseController;
use App\Http\Controllers\Api\RevenuController;

// ── ROUTES PUBLIQUES (sans token) ──────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ── ROUTES PROTÉGÉES (token Sanctum requis) ────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Budgets
    Route::apiResource('budgets', BudgetController::class);

    // Catégories
    Route::apiResource('categories', CategorieController::class);

    // Dépenses
    Route::apiResource('depenses', DepenseController::class);

    // Revenus
    Route::apiResource('revenus', RevenuController::class);
});