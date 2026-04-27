<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ── INSCRIPTION ────────────────────────────────────────
    public function register(Request $request): JsonResponse
    {
        // Validation des données envoyées
        $validated = $request->validate([
            'nom'            => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'mot_de_passe'   => 'required|string|min:6|confirmed',
        ]);

        // Création de l'utilisateur
        $user = User::create([
            'nom'          => $validated['nom'],
            'email'        => $validated['email'],
            'mot_de_passe' => $validated['mot_de_passe'],
        ]);

        // Associer automatiquement les catégories par défaut
        // à ce nouvel utilisateur
        $categoriesParDefaut = Categorie::where('is_default', true)->get();
        foreach ($categoriesParDefaut as $categorie) {
            $user->userCategories()->create([
                'categorie_id' => $categorie->id,
            ]);
        }

        // Générer le token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    // ── CONNEXION ──────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        // Validation des données envoyées
        $request->validate([
            'email'        => 'required|email',
            'mot_de_passe' => 'required|string',
        ]);

        // Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();

        // Vérifier le mot de passe
        if (!$user || !Hash::check($request->mot_de_passe, $user->mot_de_passe)) {
            throw ValidationException::withMessages([
                'email' => ['Email ou mot de passe incorrect.'],
            ]);
        }

        // Révoquer les anciens tokens et en créer un nouveau
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    // ── DÉCONNEXION ────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        // Révoquer le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }

    // ── PROFIL ─────────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}