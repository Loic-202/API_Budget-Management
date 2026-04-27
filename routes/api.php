<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\DepenseController;
use App\Http\Controllers\Api\RevenuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes pour l'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Routes protégées pour les dépenses
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('depenses', DepenseController::class);
    Route::apiResource('revenus', RevenuController::class);
    Route::apiResource('categories', CategorieController::class);
    Route::apiResource('budgets', BudgetController::class);

    // Route pour obtenir le solde
    Route::get('/solde', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'solde' => $user->solde,
            'total_revenus' => $user->revenus()->sum('montant'),
            'total_depenses' => $user->depenses()->sum('montant'),
        ]);
    });
});
