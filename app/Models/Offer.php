<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model 
{

    protected $table = 'offers';
    public $timestamps = true;
    protected $fillable = array('provider_id', 'city_id', 'request_id', 'status');

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

    public function request()
    {
        return $this->belongsTo('App\Models\Request');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

}