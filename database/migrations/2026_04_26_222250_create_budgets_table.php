<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('mois');
            $table->unsignedSmallInteger('annee');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['utilisateur_id', 'mois', 'annee']);
            $table->index(['utilisateur_id', 'annee', 'mois']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
