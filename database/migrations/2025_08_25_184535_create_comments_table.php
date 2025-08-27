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
			$table->morphs('commentable');
			$table->text('cotent');
			$table->integer('parent_id')->unsigned()->nullable();
		});
	}

	public function down()
	{
		Schema::drop('comments');
	}
}