<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;
use Illuminate\Support\Facades\Cache;

class CommissionSettings extends Settings
{
    public float $client_commission;
    public float $provider_commission;
    public string $client_commission_text_ar;
    public string $client_commission_text_en;
    public string $provider_commission_text_ar;
    public string $provider_commission_text_en;

    public static function group(): string
    {
        return 'commission';
    }
    //forget the cache after save 
    public function save(): self
    {
        parent::save();
        Cache::forget('all_settings');
        
        // Clear specific commission settings cache
        Cache::forget('setting_commission_client_commission');
        Cache::forget('setting_commission_provider_commission');
        Cache::forget('setting_commission_client_commission_text_ar');
        Cache::forget('setting_commission_client_commission_text_en');
        Cache::forget('setting_commission_provider_commission_text_ar');
        Cache::forget('setting_commission_provider_commission_text_en');
        
        return $this;
    }
}