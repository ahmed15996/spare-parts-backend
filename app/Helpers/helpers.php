<?php

namespace App\Helpers;

use App\Settings\GeneralSettings;
use App\Settings\SocialMediaSettings;
use App\Settings\CommissionSettings;
use App\Settings\ContentSettings;
use App\Settings\MediaSettings;
use Illuminate\Support\Facades\Cache;

if(!function_exists('settings')){
    function settings()
    {
        return Cache::rememberForever('all_settings', function () {
            return [
                app(CommissionSettings::class),
                app(ContentSettings::class),
                app(MediaSettings::class),
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
                case 'commission':
                    $settings = app(CommissionSettings::class);
                    break;
                case 'content':
                        $settings = app(ContentSettings::class);
                    break;
                case 'media':
                    $settings = app(MediaSettings::class);
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
        Cache::forget('setting_content_*');
        Cache::forget('setting_commission_*');
        Cache::forget('setting_media_*');
    }
}