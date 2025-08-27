<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryProviderTable extends Migration {

	public function up()
	{
		Schema::create('category_provider', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('category_id');
			$table->integer('provider_id');
		});
	}

	public function down()
	{
		Schema::drop('category_provider');
	}
}