<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBlocksTable extends Migration {

	public function up()
	{
		Schema::create('user_blocks', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->unsignedInteger('blocker_id');
			$table->unsignedInteger('blocked_id');
			
			// Prevent duplicate blocks
			$table->unique(['blocker_id', 'blocked_id']);
		});

		// Add foreign key constraints
		Schema::table('user_blocks', function(Blueprint $table) {
			$table->foreign('blocker_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('blocked_id')->references('id')->on('users')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('user_blocks');
	}
}