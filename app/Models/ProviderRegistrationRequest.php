<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class ProviderRegistrationRequest extends Model implements HasMedia
{
    use InteractsWithMedia , HasTranslations;
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'city_id',
        'category_id',
        'brands',
        'lat',
        'long',
        'store_name',
        'description',
        'commercial_number',
        'address',
        'status',
    ];
    public $translatable = ['store_name'];
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brands()
    {
        return $this->belongsToMany(Brand::class);
    }
}
