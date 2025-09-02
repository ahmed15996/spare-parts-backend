<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommentsAddPostIdAndContent extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'post_id')) {
                $table->unsignedInteger('post_id')->nullable()->after('author_type');
                $table->index('post_id');
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            }
            if (!Schema::hasColumn('comments', 'content')) {
                $table->text('content')->nullable()->after('post_id');
            }
        });

        // Try to backfill content from legacy column if it exists
        if (Schema::hasColumn('comments', 'cotent') && Schema::hasColumn('comments', 'content')) {
            \DB::statement('UPDATE comments SET content = COALESCE(content, cotent)');
        }
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'post_id')) {
                $table->dropForeign(['post_id']);
                $table->dropIndex(['post_id']);
                $table->dropColumn('post_id');
            }
            if (Schema::hasColumn('comments', 'content')) {
                $table->dropColumn('content');
            }
        });
    }
}


