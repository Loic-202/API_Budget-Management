<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');
            $table->decimal('montant_limite', 10, 2);
            $table->timestamps();

            $table->unique(['budget_id', 'categorie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_categories');
    }
};
