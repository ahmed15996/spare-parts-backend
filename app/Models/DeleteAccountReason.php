<?php

namespace App\Models;

use App\Enums\DeleteAccountReasonType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteAccountReason extends Model
{
    use HasFactory;

    protected $fillable = [
        'reason',
        'type',
    ];

    protected $casts = [
        'type' => DeleteAccountReasonType::class,
    ];

    public function scopeByType($query, DeleteAccountReasonType $type)
    {
        return $query->where('type', $type->value);
    }

    public function scopeForClients($query)
    {
        return $query->where('type', DeleteAccountReasonType::Client->value);
    }

    public function scopeForProviders($query)
    {
        return $query->where('type', DeleteAccountReasonType::Provider->value);
    }
}
