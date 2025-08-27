<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model 
{

    protected $table = 'banners';
    public $timestamps = true;
    protected $fillable = array('title', 'description', 'type', 'original_price', 'discount_price', 'discount_percentage', 'provider_id', 'status', 'rejection_reason');

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

}