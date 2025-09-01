<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model 
{

    protected $table = 'cars';
    public $timestamps = true;
    protected $fillable = array('brand_model_id', 'manufacture_year', 'number', 'user_id');

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function brandModel()
    {
        return $this->belongsTo('App\Models\BrandModel');
    }
    public function requests()
    {
        return $this->hasMany('App\Models\Request');
    }

    public function brand(){
        return $this->hasOneThrough('App\Models\Brand', 'App\Models\BrandModel', 'id', 'id', 'brand_model_id', 'brand_id');
    }

}