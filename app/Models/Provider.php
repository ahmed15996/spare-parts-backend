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
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = array('user_id', 'store_name', 'description', 'commercial_number', 'location', 'category_id', 'city_id', 'slug');
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
        return $this->belongsToMany(Brand::class, 'brand_provider','provider_id','brand_id');
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
        return $this->hasMany('App\Models\Product','provider_id');
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
        return $this->hasMany('App\Models\DayProvider', 'provider_id','id');
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
        return $this->hasMany('App\Models\Subscription','provider_id');
    }

    public function banners()
    {
        return $this->hasMany('App\Models\Banner');
    }

    public function activeProfileBanners()
    {
        return $this->hasMany('App\Models\Banner')->where('status', 1)->where('type', 2);
    }

    public function hasActiveSubscription()
    {
        return $this->subscriptions->where('is_active', true)->first() ? true : false;
    }

}