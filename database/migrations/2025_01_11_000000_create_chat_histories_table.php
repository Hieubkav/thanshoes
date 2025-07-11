<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_histories', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index(); // Session ID để nhóm các tin nhắn
            $table->text('message'); // Nội dung tin nhắn
            $table->boolean('is_user')->default(true); // true = tin nhắn từ user, false = từ AI
            $table->timestamps();
            
            // Index để tối ưu query
            $table->index(['session_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
    }
};
