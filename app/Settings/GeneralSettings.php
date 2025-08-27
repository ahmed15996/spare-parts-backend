<?php 

namespace App\Settings;

use Illuminate\Support\Facades\Storage;
use Spatie\LaravelSettings\Settings;
class GeneralSettings extends Settings
{
    public string $name_ar;
    public string $name_en;
    public string $email;
    public string $phone;
    public ?string $logo_ar = null;
    public ?string $logo_en = null;


    public static function group(): string
    {
        return 'general';
    }

    public function getLogoArUrlAttribute()
    {
        return $this->logo_ar 
            ? Storage::disk('public')->url($this->logo_ar)
            : asset('frontend/images/logo.jpg');
    }

    public function getLogoEnUrlAttribute()
    {
        return $this->logo_en 
            ? Storage::disk('public')->url($this->logo_en)
            : asset('frontend/images/logo.jpg');
    }

}
