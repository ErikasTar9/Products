<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property BelongsToMany $tags
 * @property int $id
 */
class Product extends Model
{
    use HasFactory;

    // Fillabale would be better practice here btw
    protected $guarded = [];

    protected $casts = [
        'source_updated_at' => 'date',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, ProductTag::class);
    }

    public function totalStock(): int
    {
        return $this->stocks()->sum('stock');
    }
}
