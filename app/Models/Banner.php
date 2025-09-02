<?php

namespace App\Models;

use App\Enums\BannerType;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Model;
use App\Enums\BannerStatus;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = 'banners';
    public $timestamps = true;

    const TYPE_HOME = 1;
    const TYPE_PROFILE = 2;

    protected $fillable = array('title', 'description', 'type', 'original_price', 'discount_price', 
    'discount_percentage', 'provider_id', 'status', 'rejection_reason', 'number', 'accepted_at');

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

    protected $casts = [
        'status' => BannerStatus::class,
    ];

    public function scopeActive($query)
    {
        return $query->where('status', BannerStatus::Approved);
    }

    public function scopeHome($query)
    {
        return $query->where('type', BannerType::Home) ->orWhere('type', BannerType::Both);
    }
    public function scopeProfile($query)
    {
        return $query->where('type', BannerType::Profile) ->orWhere('type', BannerType::Both);
    }


}