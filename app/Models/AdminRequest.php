<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRequest extends Model 
{

    protected $table = 'admin_requests';
    public $timestamps = true;
    protected $fillable = array('data', 'status', 'reason', 'type', 'requestable', 'original_data');

}