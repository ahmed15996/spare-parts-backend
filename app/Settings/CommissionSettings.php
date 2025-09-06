<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

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
}