<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class Day extends Model 
{
    use HasTranslations;
    protected $table = 'days';
    public $timestamps = true;
    protected $fillable = array('name');

    public $translatable = ['name'];

    public function providers()
    {
        return $this->belongsToMany('App\Models\Provider');
    }

}