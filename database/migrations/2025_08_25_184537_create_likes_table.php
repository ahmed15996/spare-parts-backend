<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration {

    public function up()
    {
        Schema::create('likes', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->morphs('user');
            $table->morphs('likeable');
            $table->tinyInteger('value')->default(1); // 1 like, -1 dislike
            $table->unique(['user_id', 'user_type', 'likeable_id', 'likeable_type'], 'likes_unique_user_likeable');
        });
    }

    public function down()
    {
        Schema::drop('likes');
    }
}


