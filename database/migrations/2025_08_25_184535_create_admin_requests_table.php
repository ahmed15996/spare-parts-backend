<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminRequestsTable extends Migration {

	public function up()
	{
		Schema::create('admin_requests', function(Blueprint $table) {
			$table->increments('id');
			$table->json('data');
			$table->tinyInteger('status');
			$table->text('reason')->nullable();
			$table->timestamps();
			$table->tinyInteger('type');
			$table->morphs('requestable');
			$table->json('original_data')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('admin_requests');
	}
}