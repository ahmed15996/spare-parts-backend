<?php

namespace App\Filament\Resources\ProviderResource\Pages;

use App\Filament\Resources\ProviderResource;
use App\Models\Provider;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Filament\Notifications\Notification;

class CreateProvider extends CreateRecord
{
    protected static string $resource = ProviderResource::class;
    
    protected array $userData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate unique email and phone
        $userData = $data['user'] ?? [];
        
        if (!empty($userData['email'])) {
            $existingEmail = User::where('email', $userData['email'])->exists();
            if ($existingEmail) {
                Notification::make()
                    ->title(__('Error'))
                    ->body(__('Email already exists'))
                    ->danger()
                    ->send();
                    
                $this->halt();
            }
        }
        
        if (!empty($userData['phone'])) {
            $existingPhone = User::where('phone', $userData['phone'])->exists();
            if ($existingPhone) {
                Notification::make()
                    ->title(__('Error'))
                    ->body(__('Phone already exists'))
                    ->danger()
                    ->send();
                    
                $this->halt();
            }
        }
        
        // Extract user data from the form
        unset($data['user']);
        
        // Store user data for later use
        $this->userData = $userData;
        
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // First, create the user record
            $userData = $this->userData ?? [];
            
            // Check if password was provided in the form
            $passwordProvided = !empty($userData['password']);
            $password = $passwordProvided ? $userData['password'] : null;
            
            // Generate a random password if not provided
            $generatedPassword = null;
            if (!$passwordProvided) {
                $generatedPassword = 'Provider@' . rand(1000, 9999);
                $password = Hash::make($generatedPassword);
            }
            
            $user = User::create([
                'first_name' => $userData['first_name'] ?? '',
                'last_name' => $userData['last_name'] ?? '',
                'email' => $userData['email'] ?? null,
                'phone' => $userData['phone'] ?? '',
                'password' => $password,
                'is_active' => $userData['is_active'] ?? true,
                'is_verified' => true, // Admin-created providers are auto-verified
                'lat' => $userData['lat'] ?? null,
                'long' => $userData['long'] ?? null,
                'city_id' => $data['city_id'] ?? null,
            ]);

            // Assign provider role to the user
            // Try different guard names as the exact guard may vary
            $role = Role::where('name', 'provider')
                ->whereIn('guard_name', ['sanctum', 'web', 'api'])
                ->first();
                
            if ($role) {
                $user->assignRole($role);
            }
            
            // Now create the provider record linked to the user
            $data['user_id'] = $user->id;
            
            // Handle store_name translation data properly
            if (isset($data['store_name.ar']) || isset($data['store_name.en'])) {
                $data['store_name'] = [
                    'ar' => $data['store_name.ar'] ?? '',
                    'en' => $data['store_name.en'] ?? '',
                ];
                unset($data['store_name.ar'], $data['store_name.en']);
            }
            
            // Create and return the provider record
            $provider = Provider::create($data);
            
            // Send notification based on whether password was generated or set by admin
            if ($generatedPassword) {
                Notification::make()
                    ->title(__('Provider Created Successfully'))
                    ->body(__('Generated password: ') . $generatedPassword . ' ' . __('(Please save this and share with the provider)'))
                    ->success()
                    ->persistent()
                    ->send();
            } else {
                Notification::make()
                    ->title(__('Provider Created Successfully'))
                    ->body(__('The provider can now login with the password you set.'))
                    ->success()
                    ->send();
            }
            
            return $provider;
        });
    }
}
