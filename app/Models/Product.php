<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;   
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model  implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'products';
    public $timestamps = true;
    protected $fillable = array('provider_id', 'name', 'description', 'price', 'discount_price', 'stock', 'published');

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider','provider_id');
    }


    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

}