<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Catégories par défaut — associées automatiquement
        // à chaque nouvel utilisateur lors de l'inscription
        $categories = [
            'Alimentation',
            'Transport',
            'Logement',
            'Santé',
            'Loisirs',
            'Éducation',
            'Vêtements',
            'Factures',
        ];

        foreach ($categories as $nom) {
            Categorie::firstOrCreate(
                ['nom' => $nom],
                ['is_default' => true]
            );
        }
    }
}