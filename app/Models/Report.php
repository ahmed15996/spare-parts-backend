<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model 
{

    protected $table = 'reports';
    public $timestamps = true;
    protected $fillable = array('reporter_id', 'reason');

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportable()
    {
        return $this->morphTo();
    }

}