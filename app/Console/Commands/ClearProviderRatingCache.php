<?php

namespace App\Console\Commands;

use App\Models\Provider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearProviderRatingCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-provider-ratings {--provider-id= : Clear cache for specific provider}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all provider rating caches or for a specific provider';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $providerId = $this->option('provider-id');

        if ($providerId) {
            // Clear cache for specific provider
            $cacheKey = "provider_rating_{$providerId}";
            Cache::forget($cacheKey);
            
            $this->info("Provider rating cache cleared for provider ID: {$providerId}");
        } else {
            // Clear all provider rating caches
            $providers = Provider::all();
            $clearedCount = 0;
            
            foreach ($providers as $provider) {
                $cacheKey = "provider_rating_{$provider->id}";
                Cache::forget($cacheKey);
                $clearedCount++;
            }
            
            $this->info("Provider rating cache cleared for {$clearedCount} providers");
        }

        return Command::SUCCESS;
    }
}
