<?php

namespace App\Helpers;

use App\Settings\GeneralSettings;
use App\Settings\SocialMediaSettings;
use Illuminate\Support\Facades\Cache;

if(!function_exists('settings')){
    function settings()
    {
        return Cache::rememberForever('all_settings', function () {
            return [
                app(GeneralSettings::class),
                app(SocialMediaSettings::class),
            ];
        });
    }
}

if(!function_exists('setting')){
    function setting($group, $name)
    {
        return Cache::rememberForever("setting_{$group}_{$name}", function () use ($group, $name) {
            switch ($group) {
                case 'general':
                    $settings = app(GeneralSettings::class);
                    break;
                case 'social_media':
                    $settings = app(SocialMediaSettings::class);
                    break;
                default:
                    return null;
            }
            
            return $settings->{$name} ?? null;
        });
    }
}

if(!function_exists('clear_settings_cache')){
    function clear_settings_cache()
    {
        Cache::forget('all_settings');
        Cache::forget('setting_general_*');
        Cache::forget('setting_social_media_*');
    }
}