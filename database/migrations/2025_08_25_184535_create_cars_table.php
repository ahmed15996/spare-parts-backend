<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration {

	public function up()
	{
		Schema::create('cars', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('brand_model_id')->unsigned();
			$table->date('manufacture_year');
			$table->string('number')->nullable();
			$table->integer('user_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('cars');
	}
}