<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration {

	public function up()
	{
		Schema::create('offers', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('provider_id')->unsigned();
			$table->integer('city_id')->unsigned();
			$table->integer('request_id')->unsigned();
			$table->tinyInteger('status');
		});
	}

	public function down()
	{
		Schema::drop('offers');
	}
}