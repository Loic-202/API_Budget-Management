<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenu extends Model
{
    protected $fillable = [
        'budget_id',
        'montant',
        'date',
        'source',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'date'    => 'date',
        ];
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }
}
