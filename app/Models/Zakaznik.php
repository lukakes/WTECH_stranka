<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zakaznik extends Model
{
    protected $table = 'zakaznici';

    public $timestamps = false;

    protected $fillable = [
        'meno',
        'email',
        'telefon',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function adresy(): HasMany
    {
        return $this->hasMany(Adresa::class, 'zakaznik_id');
    }

    public function objednavky(): HasMany
    {
        return $this->hasMany(Objednavka::class, 'zakaznik_id');
    }
}
