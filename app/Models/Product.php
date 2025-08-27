<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model 
{

    protected $table = 'products';
    public $timestamps = true;
    protected $fillable = array('provider_id', 'name', 'description', 'price', 'discount_price', 'stock', 'published');

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

}