<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'customer_id',
        'payment_method',
        'total',
        'original_total',
        'discount_amount',
        'discount_type',
        'discount_percentage',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->total ?? $this->items->sum(fn($item) => $item->price * $item->quantity);
    }
    
    public function getOriginalTotalPriceAttribute()
    {
        return $this->original_total ?? $this->total_price;
    }
    
    public function getDiscountAmountAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }
        
        return $this->original_total - $this->total;
    }
}
