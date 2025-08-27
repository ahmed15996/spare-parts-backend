<?php 

namespace App\Settings;

use Spatie\LaravelSettings\Settings;
class SocialMediaSettings extends Settings
{
    public string $facebook;
    public string $twitter;
    public string $instagram;
    public string $linkedin;
    public string $youtube;



    public static function group(): string
    {
        return 'social_media';
    }


}
