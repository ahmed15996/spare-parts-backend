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
        Schema::create('provider_registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->unique();
            $table->string('email');
            $table->integer('city_id')->nullable();// if null mean all cities
            $table->integer('category_id');
            $table->json('brands');
            $table->decimal('lat', 10, 8);
            $table->decimal('long', 11, 8);
			$table->json('store_name');
			$table->text('description');
			$table->string('commercial_number');
			$table->text('address');
            $table->text('location');
			$table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_registration_requests');
    }
};
