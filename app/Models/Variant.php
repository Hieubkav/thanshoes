<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;

    protected $fillable = [
        'color',
        'size',
        'price',
        'stock',
        'product_id',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    // có nhiều variant_images
    public function variant_images(){
        return $this->hasMany(VariantImage::class);
    }

    // có nhiều order_items
    public function order_items(){
        return $this->hasMany(OrderItem::class);
    }

    // có nhiều carts_items
    public function carts_items(){
        return $this->hasMany(CartItem::class);
    }
}
