<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Provider extends Model implements HasMedia 
{
    use InteractsWithMedia, HasTranslations;

    protected $table = 'providers';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = true;
    protected $fillable = array('user_id', 'store_name', 'description', 'commercial_number', 'address', 'category_id', 'city_id', 'slug');
    public $translatable = ['store_name'];
    
    protected $casts = [
        'store_name' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_provider');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function posts()
    {
        return $this->morphMany('App\Models\Post', 'author');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function days()
    {
        return $this->hasMany('App\Models\Day');
    }

    public function adminRequests()
    {
        return $this->morphMany('App\Models\AdminRequest', 'requestable');
    }

    public function offers()
    {
        return $this->hasMany('App\Models\Offer');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Models\Subscription');
    }

    public function banners()
    {
        return $this->hasMany('App\Models\Banner');
    }

}