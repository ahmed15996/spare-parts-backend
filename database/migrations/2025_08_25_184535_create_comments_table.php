<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration {

	public function up()
	{
		Schema::create('comments', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->morphs('author');
			$table->unsignedInteger('post_id');
			$table->text('content');
			$table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('comments');
	}
}