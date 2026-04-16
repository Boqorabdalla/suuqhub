<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_order_items', function (Blueprint $table) {
            $table->string('item_type')->nullable()->after('order_id');
            $table->unsignedBigInteger('item_id')->nullable()->after('item_type');
        });
    }

    public function down(): void
    {
        Schema::table('shop_order_items', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'item_id']);
        });
    }
};
