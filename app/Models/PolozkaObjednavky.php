<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolozkaObjednavky extends Model
{
    protected $table = 'polozky_objednavky';

    public $timestamps = false;

    protected $fillable = [
        'objednavka_id',
        'variant_id',
        'mnozstvo',
        'jednotkova_cena',
        'celkova_cena',
    ];

    protected $casts = [
        'mnozstvo' => 'integer',
        'jednotkova_cena' => 'decimal:2',
        'celkova_cena' => 'decimal:2',
    ];

    public function objednavka(): BelongsTo
    {
        return $this->belongsTo(Objednavka::class, 'objednavka_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(VariantProduktu::class, 'variant_id');
    }
}
