<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Alimentation', 'icone' => '🍔'],
            ['nom' => 'Transport', 'icone' => '🚌'],
            ['nom' => 'Logement', 'icone' => '🏠'],
            ['nom' => 'Santé', 'icone' => '💊'],
            ['nom' => 'Loisirs', 'icone' => '🎉'],
            ['nom' => 'Éducation', 'icone' => '📚'],
            ['nom' => 'Vêtements', 'icone' => '👕'],
            ['nom' => 'Factures', 'icone' => '🧾'],
        ];

        foreach ($categories as $category) {
            Categorie::updateOrCreate(
                ['nom' => $category['nom']],
                [
                    'icone' => $category['icone'],
                    'is_default' => true,
                ]
            );
        }
    }
}
