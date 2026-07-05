<?php

namespace App\Services;

use App\Models\AdminBanner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class AdminBannerService extends BaseService
{
    public function __construct(AdminBanner $adminBanner)
    {
        parent::__construct($adminBanner);
    }

    public function getActiveBanners(): Collection
    {
        return Cache::rememberForever('admin_banners', function () {
            return AdminBanner::query()
                ->active()
                ->latest()
                ->get();
        });
    }
}
