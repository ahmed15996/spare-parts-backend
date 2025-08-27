<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration {

	public function up()
	{
		Schema::create('banners', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('title');
			$table->text('description')->nullable();
			$table->tinyInteger('type');
			$table->decimal('original_price')->nullable();
			$table->decimal('discount_price')->nullable();
			$table->integer('discount_percentage');
			$table->integer('provider_id')->unsigned();
			$table->tinyInteger('status');
			$table->text('rejection_reason')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('banners');
	}
}