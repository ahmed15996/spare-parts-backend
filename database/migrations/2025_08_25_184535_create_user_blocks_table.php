<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBlocksTable extends Migration {

	public function up()
	{
		Schema::create('user_blocks', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('blocker_id')->unsigned();
			$table->integer('blocked_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('user_blocks');
	}
}