<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function categories(): HasMany { return $this->hasMany(Categorie::class); }
    public function budgets(): HasMany { return $this->hasMany(Budget::class); }
    public function depenses(): HasMany { return $this->hasMany(Depense::class); }
    public function revenus(): HasMany { return $this->hasMany(Revenu::class); }
    
    public function userCategories(): BelongsToMany
    {
        return $this->belongsToMany(Categorie::class, 'user_categories');
    }

    public function getSoldeAttribute()
    {
        $totalRevenus = $this->revenus()->sum('montant');
        $totalDepenses = $this->depenses()->sum('montant');
        return $totalRevenus - $totalDepenses;
    }
} 