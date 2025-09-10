<?php

namespace App\Jobs;

use App\Enums\BannerStatus;
use App\Models\Banner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteExpiredBanners implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // delete banners after 24 hours after accepted
        $banners = Banner::where('status',BannerStatus::Approved)
        ->where('accepted_at','<',now()->subHours(24))
        ->get();
        foreach($banners as $banner){
            $banner->delete();
        }
        
    }
}
