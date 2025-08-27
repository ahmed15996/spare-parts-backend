<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryProvider extends Model 
{

    protected $table = 'category_provider';
    public $timestamps = true;
    protected $fillable = array('category_id', 'provider_id');

}