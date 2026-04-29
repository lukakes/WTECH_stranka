<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adresa extends Model
{
    protected $table = 'adresy';

    public $timestamps = false;

    protected $fillable = [
        'zakaznik_id',
        'meno',
        'ulica',
        'mesto',
        'psc',
        'stat',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function zakaznik(): BelongsTo
    {
        return $this->belongsTo(Zakaznik::class, 'zakaznik_id');
    }
}
