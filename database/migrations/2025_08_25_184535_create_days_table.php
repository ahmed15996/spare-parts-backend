<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaysTable extends Migration {

	public function up()
	{
		Schema::create('days', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->json('name');
		});
	}

	public function down()
	{
		Schema::drop('days');
	}
}