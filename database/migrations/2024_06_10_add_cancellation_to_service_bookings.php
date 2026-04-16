<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->enum('cancellation_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('payment_status');
            $table->text('cancellation_reason')->nullable()->after('cancellation_status');
            $table->enum('refund_status', ['none', 'pending', 'processed'])->default('none')->after('cancellation_reason');
            $table->timestamp('cancelled_at')->nullable()->after('refund_status');
        });
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn(['cancellation_status', 'cancellation_reason', 'refund_status', 'cancelled_at']);
        });
    }
};
