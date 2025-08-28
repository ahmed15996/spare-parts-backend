<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model 
{

    protected $table = 'banners';
    public $timestamps = true;

    const TYPE_HOME = 1;
    const TYPE_PROFILE = 2;

    protected $fillable = array('title', 'description', 'type', 'original_price', 'discount_price', 'discount_percentage', 'provider_id', 'status', 'rejection_reason');

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeHome($query)
    {
        return $query->where('type', self::TYPE_HOME);
    }
    public function scopeProfile($query)
    {
        return $query->where('type', self::TYPE_PROFILE);
    }


}