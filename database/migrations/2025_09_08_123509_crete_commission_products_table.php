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
        Schema::create('commission_products',function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('commission_id');
            $table->unsignedInteger('product_id');
            // Details per product for provider submissions
            $table->integer('pieces')->default(0);
            // Commission value for this product in this submission
            $table->decimal('value', 12, 2)->default(0);
            // FKs
            $table->foreign('commission_id')->references('id')->on('commissions')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            // One row per commission per product
            $table->unique(['commission_id','product_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_products');
    }
};
