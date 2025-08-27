<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayProviderTable extends Migration {

	public function up()
	{
		Schema::create('day_provider', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('provider_id')->unsigned();
			$table->integer('day_id')->unsigned();
			$table->time('from');
			$table->time('to');
			$table->boolean('is_closed');
		});
	}

	public function down()
	{
		Schema::drop('day_provider');
	}
}