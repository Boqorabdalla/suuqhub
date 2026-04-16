<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('type'); // sale, commission, payout, refund
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, approved, paid
            $table->string('description')->nullable();
            $table->timestamps();
            
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('shop_orders')->onDelete('set null');
            $table->index(['agent_id', 'type']);
            $table->index(['agent_id', 'status']);
        });

        Schema::create('agent_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, rejected
            $table->timestamps();
            
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['agent_id', 'status']);
        });

        Schema::table('system_settings', function (Blueprint $table) {
            $table->decimal('default_commission_rate', 5, 2)->default(10.00)->after('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_payouts');
        Schema::dropIfExists('agent_earnings');
    }
};
