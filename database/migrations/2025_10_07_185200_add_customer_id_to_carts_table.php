<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'customer_id')) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->unique('customer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'customer_id')) {
                $table->dropUnique(['customer_id']);
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }
};
