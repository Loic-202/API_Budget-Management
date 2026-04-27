<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImportSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // On récupère le contenu du fichier SQL
    $sql = file_get_contents('C:/Users/miranda/Downloads/ImportSqlSeeder.php');

    // On l'exécute directement dans la base de données
    \DB::unprepared($sql);
}