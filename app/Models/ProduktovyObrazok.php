<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduktovyObrazok extends Model
{
    protected $table = 'ProduktovyObrazok';

    public $timestamps = false;

    protected $fillable = [
        'produktId',
        'url',
        'poradie',
    ];

    protected $casts = [
        'poradie' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Produkt::class, 'produktId');
    }
}
