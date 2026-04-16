<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->decimal('delivery_price', 10, 2)->default(0)->after('shipping_cost');
            $table->string('delivery_status')->default('pending')->after('delivery_price');
            $table->unsignedBigInteger('listing_id')->nullable()->after('delivery_status');
            $table->string('approval_status')->default('pending')->after('listing_id');
            $table->text('rejection_reason')->nullable()->after('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_price', 'delivery_status', 'listing_id', 'approval_status', 'rejection_reason']);
        });
    }
};
