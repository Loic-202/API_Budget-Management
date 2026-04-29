<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['nom' => 'Alimentation',    'icone' => '🛒', 'couleur' => '#ef4444', 'type' => 'depense'],
            ['nom' => 'Transport',       'icone' => '🚗', 'couleur' => '#f97316', 'type' => 'depense'],
            ['nom' => 'Logement',        'icone' => '🏠', 'couleur' => '#eab308', 'type' => 'depense'],
            ['nom' => 'Santé',           'icone' => '💊', 'couleur' => '#22c55e', 'type' => 'depense'],
            ['nom' => 'Loisirs',         'icone' => '🎮', 'couleur' => '#3b82f6', 'type' => 'depense'],
            ['nom' => 'Vêtements',       'icone' => '👗', 'couleur' => '#8b5cf6', 'type' => 'depense'],
            ['nom' => 'Éducation',       'icone' => '📚', 'couleur' => '#06b6d4', 'type' => 'depense'],
            ['nom' => 'Autres dépenses', 'icone' => '💳', 'couleur' => '#6b7280', 'type' => 'depense'],
            ['nom' => 'Salaire',         'icone' => '💼', 'couleur' => '#10b981', 'type' => 'revenu'],
            ['nom' => 'Freelance',       'icone' => '💻', 'couleur' => '#14b8a6', 'type' => 'revenu'],
            ['nom' => 'Investissements', 'icone' => '📈', 'couleur' => '#6366f1', 'type' => 'revenu'],
            ['nom' => 'Autres revenus',  'icone' => '💰', 'couleur' => '#84cc16', 'type' => 'revenu'],
        ];

        foreach ($defaults as $data) {
            Categorie::firstOrCreate(
                ['nom' => $data['nom'], 'is_default' => true],
                array_merge($data, ['utilisateur_id' => null, 'is_default' => true])
            );
        }
    }
}
