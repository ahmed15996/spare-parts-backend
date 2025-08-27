<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {

	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('email')->unique()->nullable();
			$table->string('phone')->unique();
			$table->string('password')->nullable();
			$table->unsignedInteger('city_id')->nullable();
			$table->boolean('is_active')->default(true);
			$table->boolean('is_verified')->default(false);
			$table->decimal('lat', 10, 8)->nullable();
			$table->decimal('long', 11, 8)->nullable();
			$table->string('address')->nullable();
			$table->integer('active_code')->nullable();
			$table->rememberToken();

		});
	}

	public function down()
	{
		Schema::drop('users');
	}
}