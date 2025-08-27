<?php

namespace App\Filament\Resources\ProviderResource\Pages;

use App\Filament\Resources\ProviderResource;
use App\Models\Provider;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CreateProvider extends CreateRecord
{
    protected static string $resource = ProviderResource::class;
    
    protected array $userData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract user data from the form
        $userData = $data['user'] ?? [];
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
            
            $user = User::create([
                'first_name' => $userData['first_name'] ?? '',
                'last_name' => $userData['last_name'] ?? '',
                'email' => $userData['email'] ?? '',
                'phone' => $userData['phone'] ?? '',
                'is_active' => $userData['is_active'] ?? true,
                'lat' => $userData['lat'] ?? null,
                'long' => $userData['long'] ?? null,
                'city_id' => $data['city_id'] ?? null,
            ]);

            // Assign provider role to the user
            $user->assignRole('provider');
            
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
            return Provider::create($data);
        });
    }
}
