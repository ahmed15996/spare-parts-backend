<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('social_media.facebook', 'https://www.facebook.com/');
        $this->migrator->add('social_media.twitter', 'https://www.twitter.com/');
        $this->migrator->add('social_media.instagram', 'https://www.instagram.com/');
        $this->migrator->add('social_media.linkedin', 'https://www.linkedin.com/');
        $this->migrator->add('social_media.youtube', 'https://www.youtube.com/');
    }
};
