<?php 

namespace App\Settings;

use Illuminate\Support\Facades\Storage;
use Spatie\LaravelSettings\Settings;
class ContentSettings extends Settings
{
    public string $about_us_ar;
    public string $about_us_en;
    public string $privacy_ar;
    public string $privacy_en;
    public static function group(): string
    {
                return 'content';
    }


}
