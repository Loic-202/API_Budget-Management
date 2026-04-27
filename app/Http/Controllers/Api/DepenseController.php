<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Depense;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    /**
     * Liste toutes les dépenses de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        return response()->json($request->user()->depenses);
    }

    /**
     * Enregistrer une nouvelle dépense
     */
    public function store(Request $request)
    {
        // 1. Validation des données
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            // On vérifie que la catégorie existe si elle est fournie
            'categorie_id' => 'nullable|exists:categories,id',
        ]);

        // 2. Création liée à l'utilisateur connecté
        $depense = $request->user()->depenses()->create($validated);

        return response()->json([
            'message' => 'Dépense enregistrée avec succès !',
            'data' => $depense
        ], 201);
    }

    /**
     * Supprimer une dépense
     */
    public function destroy(Depense $depense)
    {
        // On vérifie que la dépense appartient bien à l'utilisateur
        $depense->delete();

        return response()->json(['message' => 'Dépense supprimée']);
    }
}