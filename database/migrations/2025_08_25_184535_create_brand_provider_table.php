<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandProviderTable extends Migration {

	public function up()
	{
		Schema::create('brand_provider', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('brand_id');
			$table->integer('provider_id');
		});
	}

	public function down()
	{
		Schema::drop('brand_provider');
	}
}