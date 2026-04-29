<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetCategorie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $budgets = Budget::where('utilisateur_id', $request->user()->id)
            ->when($request->annee, fn($q, $a) => $q->where('annee', $a))
            ->orderByDesc('annee')
            ->orderByDesc('mois')
            ->withCount(['depenses', 'revenus'])
            ->get()
            ->map(fn($b) => array_merge($b->toArray(), [
                'total_depenses' => $b->total_depenses,
                'total_revenus'  => $b->total_revenus,
                'solde'          => $b->solde,
            ]));

        return response()->json($budgets);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mois'           => 'required|integer|between:1,12',
            'annee'          => 'required|integer|min:2000|max:2100',
            'montant_limite' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        $budget = Budget::create([
            ...$validated,
            'utilisateur_id' => $request->user()->id,
        ]);

        return response()->json($budget, 201);
    }

    public function show(Request $request, Budget $budget): JsonResponse
    {
        $this->authorise($request, $budget);

        $budget->load([
            'depenses.categorie',
            'revenus',
            'budgetCategories.categorie',
        ]);

        return response()->json(array_merge($budget->toArray(), [
            'total_depenses' => $budget->total_depenses,
            'total_revenus'  => $budget->total_revenus,
            'solde'          => $budget->solde,
        ]));
    }

    public function update(Request $request, Budget $budget): JsonResponse
    {
        $this->authorise($request, $budget);

        $validated = $request->validate([
            'mois'           => 'sometimes|integer|between:1,12',
            'annee'          => 'sometimes|integer|min:2000|max:2100',
            'montant_limite' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        $budget->update($validated);

        return response()->json($budget);
    }

    public function destroy(Request $request, Budget $budget): JsonResponse
    {
        $this->authorise($request, $budget);

        $budget->delete();

        return response()->json(null, 204);
    }

    public function stats(Request $request, Budget $budget): JsonResponse
    {
        $this->authorise($request, $budget);

        $depensesParCategorie = $budget->depenses()
            ->with('categorie:id,nom,icone,couleur')
            ->selectRaw('categorie_id, SUM(montant) as total')
            ->groupBy('categorie_id')
            ->get()
            ->map(function ($row) use ($budget) {
                $limite = $budget->budgetCategories()
                    ->where('categorie_id', $row->categorie_id)
                    ->value('montant_limite');

                return [
                    'categorie'     => $row->categorie,
                    'total_depense' => (float) $row->total,
                    'montant_limite'=> $limite ? (float) $limite : null,
                    'reste'         => $limite ? (float) $limite - (float) $row->total : null,
                ];
            });

        return response()->json([
            'mois'               => $budget->mois,
            'annee'              => $budget->annee,
            'total_revenus'      => $budget->total_revenus,
            'total_depenses'     => $budget->total_depenses,
            'solde'              => $budget->solde,
            'depenses_par_categorie' => $depensesParCategorie,
        ]);
    }

    public function storeLimite(Request $request, Budget $budget): JsonResponse
    {
        $this->authorise($request, $budget);

        $validated = $request->validate([
            'categorie_id'   => 'required|exists:categories,id',
            'montant_limite' => 'required|numeric|min:0',
        ]);

        $limite = BudgetCategorie::updateOrCreate(
            ['budget_id' => $budget->id, 'categorie_id' => $validated['categorie_id']],
            ['montant_limite' => $validated['montant_limite']]
        );

        return response()->json($limite->load('categorie'), 201);
    }

    private function authorise(Request $request, Budget $budget): void
    {
        if ($budget->utilisateur_id !== $request->user()->id) {
            abort(403);
        }
    }
}
