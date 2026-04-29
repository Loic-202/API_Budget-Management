<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $fillable = [
        'utilisateur_id',
        'nom',
        'icone',
        'couleur',
        'type',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class, 'categorie_id');
    }

    public function budgetCategories(): HasMany
    {
        return $this->hasMany(BudgetCategorie::class, 'categorie_id');
    }
}
