<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * Lấy slug làm khóa route thay vì id
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    protected $fillable = [
        'name',
        'brand',
        'type',
        'description',
        'sku',
        'slug',
        'og_image',
        'seo_description',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->ordered();
    }

    public function productViews(): HasMany
    {
        return $this->hasMany(ProductView::class);
    }

    // Quan hệ nhiều-nhiều với Tag
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
