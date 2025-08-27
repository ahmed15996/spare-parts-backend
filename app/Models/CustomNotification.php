<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomNotification extends Model 
{

    protected $table = 'custom_notifications';
    public $timestamps = true;
    protected $fillable = array('title', 'body', 'notifiable');

}