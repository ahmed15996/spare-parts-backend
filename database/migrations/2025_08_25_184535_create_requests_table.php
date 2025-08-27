<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration {

	public function up()
	{
		Schema::create('requests', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('city_id')->unsigned()->nullable();
			$table->boolean('all_cities');
			$table->text('description');
			$table->integer('user_id')->unsigned();
			$table->tinyInteger('status');
		});
	}

	public function down()
	{
		Schema::drop('requests');
	}
}