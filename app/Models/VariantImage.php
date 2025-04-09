<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VariantImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'variant_id',
    ];

    protected $appends = [
        'image_url',
    ];

    public function variant(){
        return $this->belongsTo(Variant::class);
    }
    
    public function productImage()
    {
        return $this->hasOne(ProductImage::class, 'variant_image_id');
    }
    
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        
        return url('storage/' . $this->image);
    }
}
