<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategoria extends Model
{
    protected $table = 'Kategoria';

    public $timestamps = false;

    protected $fillable = [
        'nazov',
        'parentId',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parentId');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parentId');
    }

    public function produkty(): HasMany
    {
        return $this->hasMany(Produkt::class, 'kategoriaId');
    }
}
