<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'type',
        'description',
        'sku',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->ordered();
    }

    // Quan hệ nhiều-nhiều với Tag
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
