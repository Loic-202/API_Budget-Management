<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Revenu;
use Illuminate\Http\Request;

class RevenuController extends Controller
{
    /**
     * Enregistrer un nouveau revenu
     */
    public function store(Request $request)
    {
        // 1. On valide les données (ex: le montant doit être un nombre)
        $validated = $request->validate([
    'description' => 'required|string|max:255',
    'montant'     => 'required|numeric',
    'date'        => 'required|date',
]);

        // 2. On crée le revenu en le liant à l'utilisateur connecté
        // $request->user() récupère automatiquement l'utilisateur qui a envoyé le token
        $revenu = $request->user()->revenus()->create($validatedData);

        // 3. On retourne une réponse JSON
        return response()->json([
            'message' => 'Revenu ajouté avec succès !',
            'data' => $revenu
        ], 201);
    }
}