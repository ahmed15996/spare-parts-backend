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
			$table->integer('category_id')->unsigned()->nullable();
			$table->integer('car_id')->unsigned()->nullable();
			$table->integer('number')->unique();
			$table->text('description');
			$table->integer('user_id')->unsigned();
			$table->tinyInteger('status');
			$table->index('category_id');
			$table->index('number');
		});
	}

	public function down()
	{
		Schema::drop('requests');
	}
}