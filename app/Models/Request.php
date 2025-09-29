<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model 
{

    protected $table = 'requests';
    public $timestamps = true;
    protected $fillable = array('city_id','description', 'user_id', 'status', 'number', 'car_id', 'category_id');

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function car()
    {
        return $this->belongsTo('App\Models\Car');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function offers()
    {
        return $this->hasMany('App\Models\Offer', 'request_id');
    }

    /**
     * Get the providers that have hidden this request.
     */
    public function hiddenByProviders()
    {
        return $this->belongsToMany(Provider::class, 'provider_hidden_requests');
    }
}