<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Produkt extends Model
{
    protected $table = 'Produkt';

    public $timestamps = false;

    protected $fillable = [
        'nazov',
        'popis',
        'zakladna_cena',
        'kategoriaId',
        'aktivny',
        'created_at',
    ];

    protected $casts = [
        'zakladna_cena' => 'decimal:2',
        'aktivny' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $activeQuery) {
            $activeQuery->whereNull('aktivny')->orWhere('aktivny', true);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Kategoria::class, 'kategoriaId');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(VariantProduktu::class, 'produktId');
    }

    public function firstActiveVariant(): HasOne
    {
        return $this->hasOne(VariantProduktu::class, 'produktId')
            ->active()
            ->orderBy('id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProduktovyObrazok::class, 'produktId');
    }
}
