<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.name_ar','الاسم بالعربية');
        $this->migrator->add('general.name_en','The Name In English');
        $this->migrator->add('general.email','The Email');
        $this->migrator->add('general.phone','The Phone');
        $this->migrator->add('general.logo_ar','/frontend/images/logo.jpg');
        $this->migrator->add('general.logo_en','/frontend/images/logo.jpg');


    }
};
