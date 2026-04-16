<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('name'); // e.g., "Size", "Color"
            $table->string('value'); // e.g., "Large", "Red"
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->tinyInteger('is_default')->default(0);
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
