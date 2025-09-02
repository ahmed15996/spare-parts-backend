<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model 
{

    protected $table = 'offers';
    public $timestamps = true;
    protected $fillable = array('provider_id', 'city_id', 'request_id', 'status', 'price', 'description', 'has_delivery');

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider','provider_id');
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