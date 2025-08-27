<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model 
{

    protected $table = 'user_blocks';
    public $timestamps = true;
    protected $fillable = array('blocker_id', 'blocked_id');

    public function blocker()
    {
        return $this->belongsTo('App\Models\User', 'blocker_id');
    }

    public function blocked()
    {
        return $this->belongsTo('App\Models\User', 'blocked_id');
    }

}