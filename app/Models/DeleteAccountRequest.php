<?php

namespace App\Models;

use App\Enums\DeleteAccountRequestStatus;
use Illuminate\Database\Eloquent\Model;

class DeleteAccountRequest extends Model
{
    protected $guarded = [];
    protected $casts = [
        'status' => DeleteAccountRequestStatus::class,
    ];
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    public function reason()
    {
        return $this->belongsTo(DeleteAccountReason::class);
    }
}
