<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model 
{

    protected $table = 'posts';
    public $timestamps = true;
    protected $fillable = array('author', 'content', 'status');

    public function author()
    {
        return $this->morphTo();
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment');
    }

}