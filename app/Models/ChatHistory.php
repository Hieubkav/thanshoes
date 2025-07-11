<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'message',
        'is_user',
    ];

    protected $casts = [
        'is_user' => 'boolean',
    ];

    /**
     * Scope để lấy tin nhắn theo session
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope để lấy tin nhắn của user
     */
    public function scopeUserMessages($query)
    {
        return $query->where('is_user', true);
    }

    /**
     * Scope để lấy tin nhắn của AI
     */
    public function scopeAiMessages($query)
    {
        return $query->where('is_user', false);
    }

    /**
     * Lấy tin nhắn mới nhất theo session
     */
    public static function getRecentBySession($sessionId, $limit = 10)
    {
        return static::bySession($sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }
}
