<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenu extends Model
{
    protected $table = 'revenus';

    protected $fillable = [
        'montant',
        'description',
        'date',
        'user_id',
        'categorie_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}