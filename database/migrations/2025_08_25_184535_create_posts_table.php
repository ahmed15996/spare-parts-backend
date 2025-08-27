<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration {

	public function up()
	{
		Schema::create('posts', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->morphs('author');
			$table->text('content');
			$table->integer('likes_count')->default(0);
			$table->tinyInteger('status');
		});
	}

	public function down()
	{
		Schema::drop('posts');
	}
}