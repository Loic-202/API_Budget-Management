<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    protected $fillable = [
        'utilisateur_id',
        'mois',
        'annee',
        'montant_limite',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'mois'           => 'integer',
            'annee'          => 'integer',
            'montant_limite' => 'decimal:2',
        ];
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class, 'budget_id');
    }

    public function revenus(): HasMany
    {
        return $this->hasMany(Revenu::class, 'budget_id');
    }

    public function budgetCategories(): HasMany
    {
        return $this->hasMany(BudgetCategorie::class, 'budget_id');
    }

    public function getTotalDepensesAttribute(): float
    {
        return (float) $this->depenses()->sum('montant');
    }

    public function getTotalRevenusAttribute(): float
    {
        return (float) $this->revenus()->sum('montant');
    }

    public function getSoldeAttribute(): float
    {
        return $this->total_revenus - $this->total_depenses;
    }
}
