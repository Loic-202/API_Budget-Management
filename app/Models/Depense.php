<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 
        'montant', 
        'date_depense', 
        'user_id', 
        'categorie_id'
    ];

    // Relation : Une dépense appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}