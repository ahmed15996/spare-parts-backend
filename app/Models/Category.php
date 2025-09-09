<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia    
{
    use HasTranslations, InteractsWithMedia , HasFactory;
    protected $table = 'categories';
    public $timestamps = true;
    protected $fillable = array('name', 'slug');
    public $translatable = ['name'];
    public function providers()
    {
        return $this->belongsToMany('App\Models\Provider');
    }
    public function requests()
    {
        return $this->hasMany('App\Models\Request');
    }

}