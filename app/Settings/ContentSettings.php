<?php 

namespace App\Settings;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelSettings\Settings;

class ContentSettings extends Settings
{
    public string $about_us_ar;
    public string $about_us_en;
    public string $privacy_ar;
    public string $privacy_en;
    public string $terms_ar;
    public string $terms_en;

    public static function group(): string
    {
        return 'content';
    }

    //forget the cache after save 
    public function save(): self
    {
        parent::save();
        Cache::forget('all_settings');
        
        // Clear specific content settings cache
        Cache::forget('setting_content_about_us_ar');
        Cache::forget('setting_content_about_us_en');
        Cache::forget('setting_content_privacy_ar');
        Cache::forget('setting_content_privacy_en');
        Cache::forget('setting_content_terms_ar');
        Cache::forget('setting_content_terms_en');
        return $this;
    }
}
