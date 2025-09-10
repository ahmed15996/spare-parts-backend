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
        Schema::create('delete_account_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('set null');
            $table->unsignedBigInteger('reason_id');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delete_account_requests');
    }
};
