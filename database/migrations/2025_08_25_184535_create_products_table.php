<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration {

	public function up()
	{
		Schema::create('products', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('provider_id')->unsigned();
			$table->string('name');
			$table->text('description');
			$table->decimal('price');
			$table->decimal('discount_price')->nullable();
			$table->integer('stock');
			$table->boolean('published');
		});
	}

	public function down()
	{
		Schema::drop('products');
	}
}