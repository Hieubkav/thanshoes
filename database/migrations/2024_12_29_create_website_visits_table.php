<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45); // Hỗ trợ IPv6
            $table->text('user_agent')->nullable();
            $table->string('page_url');
            $table->string('referrer')->nullable();
            $table->date('visit_date');
            $table->unsignedInteger('unique_visitors_today')->default(0);
            $table->unsignedInteger('total_page_views_today')->default(0);
            $table->unsignedInteger('total_page_views_all_time')->default(0);
            $table->timestamps();
            
            // Index để tối ưu query
            $table->index(['ip_address', 'visit_date']);
            $table->index('visit_date');
            $table->index('page_url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_visits');
    }
};
