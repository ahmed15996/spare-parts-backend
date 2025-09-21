<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;
use Illuminate\Support\Facades\Cache;

class GeneralSettings extends Settings
{
    public int $packages_discount;

    public static function group(): string
    {
        return 'general';
    }

    //forget the cache after save 
    public function save(): self
    {
        parent::save();
        Cache::forget('all_settings');
        
        // Clear specific general settings cache
        Cache::forget('setting_general_packages_discount');
        
        return $this;
    }
}