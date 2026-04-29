<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetCategorie extends Model
{
    protected $table = 'budget_categories';

    protected $fillable = [
        'budget_id',
        'categorie_id',
        'montant_limite',
    ];

    protected function casts(): array
    {
        return [
            'montant_limite' => 'decimal:2',
        ];
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function getMontantDepenseAttribute(): float
    {
        return (float) Depense::where('budget_id', $this->budget_id)
            ->where('categorie_id', $this->categorie_id)
            ->sum('montant');
    }

    public function getResteAttribute(): float
    {
        return (float) $this->montant_limite - $this->montant_depense;
    }
}
