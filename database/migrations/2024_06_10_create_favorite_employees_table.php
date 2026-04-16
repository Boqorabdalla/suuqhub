<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_employees');
    }
};
