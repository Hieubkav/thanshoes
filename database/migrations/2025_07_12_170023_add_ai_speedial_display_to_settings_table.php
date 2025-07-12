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
        Schema::table('settings', function (Blueprint $table) {
            $table->enum('ai_speedial_display', ['hidden', 'visible_manual', 'visible_auto'])
                  ->default('visible_auto')
                  ->after('seo_description')
                  ->comment('Cấu hình hiển thị AI Speedial Chatbot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('ai_speedial_display');
        });
    }
};
