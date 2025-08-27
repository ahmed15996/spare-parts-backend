<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration {

	public function up()
	{
		Schema::create('subscriptions', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('provider_id')->unsigned();
			$table->date('start_date');
			$table->string('end_date');
			$table->integer('package_id')->unsigned();
			$table->decimal('total', 8,2);
			$table->boolean('is_active');
		});
	}

	public function down()
	{
		Schema::drop('subscriptions');
	}
}