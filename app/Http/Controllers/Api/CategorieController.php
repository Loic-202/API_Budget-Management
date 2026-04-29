<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Categorie::where(function ($q) use ($request) {
            $q->where('utilisateur_id', $request->user()->id)
              ->orWhere('is_default', true);
        })
        ->when($request->type, fn($q, $type) => $q->where('type', $type))
        ->orderBy('is_default', 'desc')
        ->orderBy('nom')
        ->get();

        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom'     => 'required|string|max:100',
            'icone'   => 'nullable|string|max:50',
            'couleur' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'type'    => 'required|in:depense,revenu',
        ]);

        $categorie = Categorie::create([
            ...$validated,
            'utilisateur_id' => $request->user()->id,
            'is_default'     => false,
        ]);

        return response()->json($categorie, 201);
    }

    public function show(Request $request, Categorie $categorie): JsonResponse
    {
        $this->authorise($request, $categorie);

        return response()->json($categorie);
    }

    public function update(Request $request, Categorie $categorie): JsonResponse
    {
        $this->authorise($request, $categorie);

        $validated = $request->validate([
            'nom'     => 'sometimes|string|max:100',
            'icone'   => 'nullable|string|max:50',
            'couleur' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'type'    => 'sometimes|in:depense,revenu',
        ]);

        $categorie->update($validated);

        return response()->json($categorie);
    }

    public function destroy(Request $request, Categorie $categorie): JsonResponse
    {
        $this->authorise($request, $categorie);

        if ($categorie->depenses()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer : cette catégorie est utilisée par des dépenses.',
            ], 422);
        }

        $categorie->delete();

        return response()->json(null, 204);
    }

    private function authorise(Request $request, Categorie $categorie): void
    {
        if ($categorie->is_default) {
            abort(403, 'Les catégories par défaut ne peuvent pas être modifiées.');
        }

        if ($categorie->utilisateur_id !== $request->user()->id) {
            abort(403);
        }
    }
}
