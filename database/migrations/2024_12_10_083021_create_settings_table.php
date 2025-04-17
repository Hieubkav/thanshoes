<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->text('slogan')->nullable();
            $table->string('facebook')->nullable();
            $table->string('zalo')->nullable();
            $table->string('logo')->nullable();
            $table->string('app_name')->nullable();

            $table->string('ban_name_product_one')->nullable();
            $table->string('ban_name_product_two')->nullable();
            $table->string('ban_name_product_three')->nullable();
            $table->string('ban_name_product_four')->nullable();
            $table->string('ban_name_product_five')->nullable();

            $table->text('size_shoes_image')->nullable();

            $table->integer('dec_product_price')->default(0);
            $table->enum('round_price',['up','down','balance'])->default('down');
            $table->enum('apply_price',['apply','not_apply'])->default('not_apply');
            // chọn giữa giảm tiền theo % hay giá tiền
            $table->enum('dec_product_price_type',['percent','price'])->default('percent');
 
            $table->string('messenger')->nullable();
            $table->string('link_tiktok')->nullable();
            $table->string('bank_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_name')->nullable();
            

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
