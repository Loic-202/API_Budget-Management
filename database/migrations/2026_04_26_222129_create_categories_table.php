<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nom', 100);
            $table->string('icone', 50)->nullable();
            $table->string('couleur', 7)->default('#6366f1');
            $table->enum('type', ['depense', 'revenu'])->default('depense');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['utilisateur_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
