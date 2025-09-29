<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderHiddenRequest extends Model
{
    protected $table = 'provider_hidden_requests';
    
    protected $fillable = [
        'provider_id',
        'request_id',
    ];

    /**
     * Get the provider that owns the hidden request.
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the request that is hidden.
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
