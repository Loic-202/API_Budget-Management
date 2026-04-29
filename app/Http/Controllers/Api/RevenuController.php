<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Revenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevenuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'budget_id'  => 'nullable|exists:budgets,id',
            'date_debut' => 'nullable|date',
            'date_fin'   => 'nullable|date|after_or_equal:date_debut',
        ]);

        $revenus = Revenu::whereHas('budget', fn($q) => $q->where('utilisateur_id', $request->user()->id))
            ->when($request->budget_id,  fn($q, $v) => $q->where('budget_id', $v))
            ->when($request->date_debut, fn($q, $v) => $q->where('date', '>=', $v))
            ->when($request->date_fin,   fn($q, $v) => $q->where('date', '<=', $v))
            ->orderByDesc('date')
            ->get();

        return response()->json($revenus);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'budget_id'   => 'required|exists:budgets,id',
            'montant'     => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'source'      => 'nullable|string|max:150',
            'description' => 'nullable|string|max:255',
        ]);

        $budget = Budget::findOrFail($validated['budget_id']);

        if ($budget->utilisateur_id !== $request->user()->id) {
            abort(403);
        }

        $revenu = Revenu::create($validated);

        return response()->json($revenu, 201);
    }

    public function show(Request $request, Revenu $revenu): JsonResponse
    {
        $this->authorise($request, $revenu);

        return response()->json($revenu->load('budget'));
    }

    public function update(Request $request, Revenu $revenu): JsonResponse
    {
        $this->authorise($request, $revenu);

        $validated = $request->validate([
            'montant'     => 'sometimes|numeric|min:0.01',
            'date'        => 'sometimes|date',
            'source'      => 'nullable|string|max:150',
            'description' => 'nullable|string|max:255',
        ]);

        $revenu->update($validated);

        return response()->json($revenu);
    }

    public function destroy(Request $request, Revenu $revenu): JsonResponse
    {
        $this->authorise($request, $revenu);

        $revenu->delete();

        return response()->json(null, 204);
    }

    private function authorise(Request $request, Revenu $revenu): void
    {
        if ($revenu->budget->utilisateur_id !== $request->user()->id) {
            abort(403);
        }
    }
}
