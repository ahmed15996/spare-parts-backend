<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandModelsTable extends Migration {

	public function up()
	{
		Schema::create('brand_models', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->json('name');
			$table->integer('brand_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('brand_models');
	}
}