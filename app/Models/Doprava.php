<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Doprava extends Model
{
    protected $table = 'dopravy';

    public $timestamps = false;

    protected $fillable = [
        'nazov',
        'cena',
        'odhad_dni',
        'aktivna',
    ];

    protected $casts = [
        'cena' => 'decimal:2',
        'aktivna' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $activeQuery) {
            $activeQuery->whereNull('aktivna')->orWhere('aktivna', true);
        });
    }
}
