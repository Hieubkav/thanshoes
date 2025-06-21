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
    {        Schema::table('orders', function (Blueprint $table) {
            // Thêm trường total nếu chưa tồn tại
            if (!Schema::hasColumn('orders', 'total')) {
                $table->decimal('total', 12, 2)->nullable()->after('payment_method');
            }
            
            // Thêm các trường liên quan đến giảm giá
            $table->decimal('original_total', 12, 2)->nullable()->after('total');
            $table->decimal('discount_amount', 12, 2)->nullable()->after('original_total');
            $table->string('discount_type')->nullable()->after('discount_amount'); // 'percent' hoặc 'price'
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'original_total',
                'discount_amount',
                'discount_type',
                'discount_percentage',
            ]);
        });
    }
};
