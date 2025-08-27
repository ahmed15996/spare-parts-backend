<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayProvider extends Model 
{

    protected $table = 'day_provider';
    public $timestamps = true;
    protected $fillable = array('provider_id', 'day_id', 'from', 'to', 'is_closed');

}