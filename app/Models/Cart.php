<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper method để lấy hoặc tạo giỏ hàng
    public static function getCart($userId = null, $sessionId = null)
    {
        if ($userId) {
            return self::firstOrCreate(['user_id' => $userId]);
        }
        return self::firstOrCreate(['session_id' => $sessionId]);
    }

    // Tính tổng tiền giỏ hàng
    public function updateTotal()
    {
        $this->total_amount = $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        $this->save();
    }
}
