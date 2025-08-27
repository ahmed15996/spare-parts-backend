<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Commands;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar']); // also accepts a closure
        });

        Commands\SetupCommand::prohibit($this->app->environment('production'));
        Commands\InstallCommand::prohibit($this->app->environment('production'));
        // Commands\GenerateCommand::prohibit($this->app->environment('production'));
        Commands\PublishCommand::prohibit($this->app->environment('production'));
        // FilamentShield::prohibitDestructiveCommands($this->app->environment('production'));


        
        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return str_replace('Models', 'Policies', $modelClass) . 'Policy';
        });
        DatabaseNotifications::trigger('vendor.filament.notifications.database-notifications-trigger');
        
        // Register Banner Observer

        // Clear custom settings cache when Spatie settings are updated
        $this->clearCustomSettingsCache();
    }

    /**
     * Clear custom settings cache when Spatie settings are updated
     */
    private function clearCustomSettingsCache(): void
    {
        // Listen for Spatie settings events
        \Event::listen('spatie.settings.saved', function ($settings) {
            // Clear all custom settings cache
            Cache::forget('all_settings');
            Cache::forget('settings');
            
            // Clear specific setting caches based on the settings class
            if ($settings instanceof \App\Settings\GeneralSettings) {
                Cache::forget('setting_general_name_ar');
                Cache::forget('setting_general_name_en');
                Cache::forget('setting_general_email');
                Cache::forget('setting_general_phone');
                Cache::forget('setting_general_logo_ar');
                Cache::forget('setting_general_logo_en');
            } elseif ($settings instanceof \App\Settings\SocialMediaSettings) {
                Cache::forget('setting_social_media_facebook');
                Cache::forget('setting_social_media_twitter');
                Cache::forget('setting_social_media_instagram');
                Cache::forget('setting_social_media_linkedin');
                Cache::forget('setting_social_media_youtube');
            }
        });
    }
}
