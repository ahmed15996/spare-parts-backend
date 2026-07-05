<?php

namespace App\Models;

use App\Enums\AdminBannerType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AdminBanner extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'admin_banners';

    protected $fillable = [
        'title',
        'type',
        'is_active',
        'link',
    ];

    protected $casts = [
        'type' => AdminBannerType::class,
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (AdminBanner $banner) {
            if (! isset($banner->type)) {
                $banner->type = AdminBannerType::Admin;
            }
        });

        static::saved(fn () => Cache::forget('admin_banners'));
        static::deleted(fn () => Cache::forget('admin_banners'));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }
}
