<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Variant extends Model
{
    use HasFactory;

    protected $fillable = [
        'color',
        'size',
        'sku',
        'price',
        'stock',
        'product_id',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    // Thay đổi thành quan hệ 1-1
    public function variantImage(): HasOne
    {
        return $this->hasOne(VariantImage::class);
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
