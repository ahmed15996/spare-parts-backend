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
			$table->foreignId('blocker_id')->constrained('users')->onDelete('cascade');
			$table->foreignId('blocked_id')->constrained('users')->onDelete('cascade');
			
			// Prevent duplicate blocks
			$table->unique(['blocker_id', 'blocked_id']);
		});
	}

	public function down()
	{
		Schema::drop('user_blocks');
	}
}