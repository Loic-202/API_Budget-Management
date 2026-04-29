<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Categorie;
use App\Models\Depense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'budget_id'    => 'nullable|exists:budgets,id',
            'categorie_id' => 'nullable|exists:categories,id',
            'date_debut'   => 'nullable|date',
            'date_fin'     => 'nullable|date|after_or_equal:date_debut',
        ]);

        $depenses = Depense::whereHas('budget', fn($q) => $q->where('utilisateur_id', $request->user()->id))
            ->with('categorie:id,nom,icone,couleur')
            ->when($request->budget_id,    fn($q, $v) => $q->where('budget_id', $v))
            ->when($request->categorie_id, fn($q, $v) => $q->where('categorie_id', $v))
            ->when($request->date_debut,   fn($q, $v) => $q->where('date', '>=', $v))
            ->when($request->date_fin,     fn($q, $v) => $q->where('date', '<=', $v))
            ->orderByDesc('date')
            ->get();

        return response()->json($depenses);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'budget_id'    => 'required|exists:budgets,id',
            'categorie_id' => 'required|exists:categories,id',
            'montant'      => 'required|numeric|min:0.01',
            'date'         => 'required|date',
            'description'  => 'nullable|string|max:255',
            'justificatif' => 'nullable|string|max:255',
        ]);

        $budget = Budget::findOrFail($validated['budget_id']);

        if ($budget->utilisateur_id !== $request->user()->id) {
            abort(403);
        }

        $this->authoriseCategorie($request, $validated['categorie_id']);

        $depense = Depense::create($validated);

        return response()->json($depense->load('categorie'), 201);
    }

    public function show(Request $request, Depense $depense): JsonResponse
    {
        $this->authorise($request, $depense);

        return response()->json($depense->load('categorie', 'budget'));
    }

    public function update(Request $request, Depense $depense): JsonResponse
    {
        $this->authorise($request, $depense);

        $validated = $request->validate([
            'categorie_id' => 'sometimes|exists:categories,id',
            'montant'      => 'sometimes|numeric|min:0.01',
            'date'         => 'sometimes|date',
            'description'  => 'nullable|string|max:255',
            'justificatif' => 'nullable|string|max:255',
        ]);

        if (isset($validated['categorie_id'])) {
            $this->authoriseCategorie($request, $validated['categorie_id']);
        }

        $depense->update($validated);

        return response()->json($depense->load('categorie'));
    }

    public function destroy(Request $request, Depense $depense): JsonResponse
    {
        $this->authorise($request, $depense);

        $depense->delete();

        return response()->json(null, 204);
    }

    private function authorise(Request $request, Depense $depense): void
    {
        if ($depense->budget->utilisateur_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function authoriseCategorie(Request $request, int $categorieId): void
    {
        $categorie = Categorie::findOrFail($categorieId);

        if ($categorie->type !== 'depense') {
            abort(422, 'Cette catégorie n\'est pas de type dépense.');
        }

        if (!$categorie->is_default && $categorie->utilisateur_id !== $request->user()->id) {
            abort(403, 'Cette catégorie ne vous appartient pas.');
        }
    }
}
