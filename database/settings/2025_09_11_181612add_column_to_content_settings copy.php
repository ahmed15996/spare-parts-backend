<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('content.terms_ar','arabic terms');
        $this->migrator->add('content.terms_en','english terms');
        }
};
