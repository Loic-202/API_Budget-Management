<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('restrict');
            $table->decimal('montant', 10, 2);
            $table->date('date');
            $table->string('description', 255)->nullable();
            $table->string('justificatif', 255)->nullable();
            $table->timestamps();

            $table->index(['budget_id', 'date']);
            $table->index('categorie_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
