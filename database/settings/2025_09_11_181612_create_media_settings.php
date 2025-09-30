<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('media.linked_in','https://www.linkedin.com/');
        $this->migrator->add('media.facebook','https://www.facebook.com/');
        $this->migrator->add('media.twitter','https://www.twitter.com/');
        $this->migrator->add('media.tiktok','https://www.tiktok.com/');
        $this->migrator->add('media.instagram','https://www.instagram.com/');
        $this->migrator->add('media.snapchat','https://www.snapchat.com/');
        $this->migrator->add('media.app_store','https://www.apple.com/app-store/');
        $this->migrator->add('media.google_play','https://play.google.com/store/apps/details?id=com.google.android.apps.googlevoice');
        }
};
