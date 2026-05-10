<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Platba extends Model
{
    protected $table = 'platby';

    public $timestamps = false;

    protected $fillable = [
        'sposob_platby',
        'poplatok',
        'aktivna',
    ];

    protected $casts = [
        'poplatok' => 'decimal:2',
        'aktivna' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $activeQuery) {
            $activeQuery->whereNull('aktivna')->orWhere('aktivna', true);
        });
    }
}
