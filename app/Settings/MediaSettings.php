<?php 

namespace App\Settings;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelSettings\Settings;

class MediaSettings extends Settings
{
    public string $linked_in;
    public string $facebook;
    public string $twitter;
    public string $tiktok;
    public string $instagram;
    public string $snapchat;
    public string $app_store;
    public string $google_play;

    public static function group(): string
    {
        return 'media';
    }

    //forget the cache after save 
    public function save(): self
    {
        parent::save();
        Cache::forget('all_settings');
        
        // Clear specific content settings cache
        Cache::forget('setting_media_linked_in');
        Cache::forget('setting_media_facebook');
        Cache::forget('setting_media_twitter');
        Cache::forget('setting_media_tiktok');
        Cache::forget('setting_media_instagram');
        Cache::forget('setting_media_snapchat');
        Cache::forget('setting_media_app_store');
        Cache::forget('setting_media_google_play'); 
        return $this;
    }
}
