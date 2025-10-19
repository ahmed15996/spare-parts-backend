<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class BrandModel extends Model 
{
    use HasTranslations;
    protected $table = 'brand_models';
    public $timestamps = true;
    protected $fillable = array('name', 'brand_id');
    public $translatable = ['name'];

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }

    public function cars()
    {
        return $this->hasMany('App\Models\Car', 'brand_model_id');
    }

}