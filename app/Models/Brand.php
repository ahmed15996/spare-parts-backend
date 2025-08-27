<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;
    protected $table = 'brands';
    public $timestamps = true;
    protected $fillable = array('name');
    public $translatable = ['name'];

    public function providers()
    {
        return $this->belongsToMany('App\Models\Provider');
    }

    public function models()
    {
        return $this->hasMany('App\Models\BrandModel','brand_id');
    }

}