<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('discount_price');
            }
            if (!Schema::hasColumn('products', 'track_stock')) {
                $table->tinyInteger('track_stock')->default(1)->after('has_variation');
            }
            if (!Schema::hasColumn('products', 'pickup_cost')) {
                $table->decimal('pickup_cost', 10, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('products', 'delivery_cost')) {
                $table->decimal('delivery_cost', 10, 2)->default(0)->after('pickup_cost');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = ['original_price', 'track_stock', 'pickup_cost', 'delivery_cost'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
