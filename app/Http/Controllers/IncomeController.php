<?php

namespace App\Http\Controllers;

use App\Models\Income; // Importe ton modèle Income
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Afficher la liste de tous les revenus
     */
    public function index()
    {
        $incomes = Income::all();
        return response()->json($incomes);
    }

    /**
     * Enregistrer un nouveau revenu
     */
    public function store(Request $request)
    {
        // 1. On vérifie que les données envoyées sont correctes
        $validatedData = $request->validate([
            'title'      => 'required|string|max:255',
            'amount'     => 'required|numeric|min:0',
            'entry_date' => 'required|date',
            'user_id'    => 'required|exists:users,id',
        ]);

        // 2. On crée le revenu dans la base de données
        $income = Income::create($validatedData);

        // 3. On répond au frontend que ça a fonctionné
        return response()->json([
            'message' => 'Revenu ajouté avec succès !',
            'income'  => $income
        ], 201);
    }

    /**
     * Supprimer un revenu
     */
    public function destroy($id)
    {
        $income = Income::findOrFail($id);
        $income->delete();

        return response()->json(['message' => 'Revenu supprimé']);
    }
}