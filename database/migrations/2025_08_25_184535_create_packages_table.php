<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration {

	public function up()
	{
		Schema::create('packages', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->json('name');
			$table->json('description');
			$table->decimal('price', 10, 2);
			$table->tinyInteger('banner_type');
			$table->integer('duration');
		});
	}

	public function down()
	{
		Schema::drop('packages');
	}
}