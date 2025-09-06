<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('commission.client_commission');
        $this->migrator->add('commission.client_commission_text_ar');
        $this->migrator->add('commission.client_commission_text_en');
        $this->migrator->add('commission.provider_commission');
        $this->migrator->add('commission.provider_commission_text_ar');
        $this->migrator->add('commission.provider_commission_text_en');


    }
};
