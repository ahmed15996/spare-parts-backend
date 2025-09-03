<?php

namespace App\Jobs;

use App\Models\Provider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CalculateProviderRating implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $providerId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $providerId = null)
    {
        $this->providerId = $providerId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->providerId) {
            // Calculate rating for specific provider
            $this->calculateRating($this->providerId);
        } else {
            // Calculate ratings for all providers
            $providers = Provider::all();
            
            foreach ($providers as $provider) {
                $this->calculateRating($provider->id);
            }
        }
    }

    /**
     * Calculate and cache rating for a specific provider
     */
    protected function calculateRating(int $providerId): void
    {
        $cacheKey = "provider_rating_{$providerId}";
        
        // Clear existing cache first
        Cache::forget($cacheKey);
        
        // Calculate new rating (this will cache it automatically)
        \App\Models\Review::calculateProviderRating($providerId);
    }
}
