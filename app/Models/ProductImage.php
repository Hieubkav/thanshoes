<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'type',
        'variant_image_id',
        'source',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variantImage(): BelongsTo
    {
        return $this->belongsTo(VariantImage::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getImageUrlAttribute()
    {
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        return $this->image ? url('storage/' . $this->image) : null;
    }

    public static function updateOrder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            static::where('id', $id)->update(['order' => $order]);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            if (!$image->order) {
                $image->order = static::where('product_id', $image->product_id)->max('order') + 1;
            }
        });

        static::deleting(function ($image) {
            if ($image->type === 'upload' && $image->image 
                && !Str::startsWith($image->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($image->image);
            }
        });
    }
}
