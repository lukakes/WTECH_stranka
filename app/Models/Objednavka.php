<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Objednavka extends Model
{
    protected $table = 'objednavky';

    public $timestamps = false;

    protected $fillable = [
        'zakaznik_id',
        'adresa_id',
        'doprava_id',
        'platba_id',
        'stav',
        'subtotal',
        'doprava_cena',
        'platba_poplatok',
        'total',
        'created_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'doprava_cena' => 'decimal:2',
        'platba_poplatok' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function zakaznik(): BelongsTo
    {
        return $this->belongsTo(Zakaznik::class, 'zakaznik_id');
    }

    public function polozky(): HasMany
    {
        return $this->hasMany(PolozkaObjednavky::class, 'objednavka_id');
    }
}
