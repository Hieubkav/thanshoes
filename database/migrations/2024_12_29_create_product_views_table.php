<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->date('view_date');
            $table->unsignedInteger('unique_viewers_today')->default(0);
            $table->unsignedInteger('total_views_today')->default(0);
            $table->unsignedInteger('total_views_all_time')->default(0);
            $table->timestamps();
            
            // Index để tối ưu query
            $table->index(['product_id', 'ip_address', 'view_date']);
            $table->index(['product_id', 'view_date']);
            $table->index('view_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_views');
    }
};
