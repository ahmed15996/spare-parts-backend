<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model 
{

    protected $table = 'likes';
    public $timestamps = true;
    protected $fillable = array('user_id', 'user_type', 'likeable_id', 'likeable_type', 'value');

    public function user()
    {
        return $this->morphTo();
    }

    public function likeable()
    {
        return $this->morphTo();
    }
}


