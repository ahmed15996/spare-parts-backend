<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersTable extends Migration {

	public function up()
	{
		Schema::create('providers', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->unsignedInteger('category_id')->nullable();
		    $table->unsignedInteger('city_id')->nullable();
			$table->json('store_name');
			$table->text('description');
			$table->string('commercial_number');
			$table->text('location');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('providers');
	}
}