<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantProduktu extends Model
{
    protected $table = 'VariantProduktu';

    public $timestamps = false;

    protected $fillable = [
        'produktId',
        'nazov',
        'cena',
        'skladom',
        'aktivny',
    ];

    protected $casts = [
        'cena' => 'decimal:2',
        'skladom' => 'integer',
        'aktivny' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $activeQuery) {
            $activeQuery->whereNull('aktivny')->orWhere('aktivny', true);
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Produkt::class, 'produktId');
    }
}
