<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->string('name');
            $table->string('value');
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
            
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_variations');
    }
};
