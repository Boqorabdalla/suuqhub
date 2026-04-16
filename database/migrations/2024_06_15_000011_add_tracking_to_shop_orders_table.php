<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('order_number');
            }
            if (!Schema::hasColumn('shop_orders', 'tracking_status')) {
                $table->string('tracking_status')->default('pending')->after('tracking_number');
            }
            if (!Schema::hasColumn('shop_orders', 'coupon_id')) {
                $table->unsignedBigInteger('coupon_id')->nullable()->after('shipping_cost');
            }
            if (!Schema::hasColumn('shop_orders', 'coupon_discount')) {
                $table->decimal('coupon_discount', 10, 2)->default(0)->after('coupon_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $columns = ['tracking_number', 'tracking_status', 'coupon_id', 'coupon_discount'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('shop_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
