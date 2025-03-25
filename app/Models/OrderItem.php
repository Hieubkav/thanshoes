<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'variant_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function getProductLabel(): string
    {
        if (!$this->variant || !$this->variant->product) {
            return 'Sản phẩm không tồn tại';
        }

        return $this->variant->product->name . 
               ' - Size: ' . $this->variant->size . 
               ' - Màu: ' . $this->variant->color;
    }
}
