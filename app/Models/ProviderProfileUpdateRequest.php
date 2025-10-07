<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProviderProfileUpdateRequest extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'provider_id',
        'store_name',
        'description',
        'city_id',
        'commercial_number',
        'lat',
        'long',
        'address',
        'location',
        'status',
        'brands',
        'category_id',
        'processed_at',
        'processed_by',
        'admin_notes'
    ];

    protected $casts = [
        'store_name' => 'array',
        'brands' => 'array',
        'processed_at' => 'datetime',
        'status' => 'integer'
    ];

    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    /**
     * Get the provider that owns the update request
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class,'provider_id');
    }

    /**
     * Get the city for this update request
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the category for this update request
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the admin who processed this request
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the brands for this update request
     */
    public function getBrandsAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true);
        }
        return $value;
    }

    /**
     * Set the brands attribute
     */
    public function setBrandsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['brands'] = json_encode($value);
        } else {
            $this->attributes['brands'] = $value;
        }
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Check if request is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request is rejected
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown'
        };
    }
}
