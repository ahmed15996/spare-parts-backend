<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model 
{

    protected $table = 'requests';
    public $timestamps = true;
    protected $fillable = array('city_id', 'all_cities', 'description', 'user_id', 'status');

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function cities()
    {
        return $this->hasMany('App\Models\City');
    }

}