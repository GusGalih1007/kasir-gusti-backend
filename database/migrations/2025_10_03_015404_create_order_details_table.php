<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('order_detail_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('quantity');
            $table->decimal('price_at_purchase', 15, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
