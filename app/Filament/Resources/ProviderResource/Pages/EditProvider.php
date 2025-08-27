<?php

namespace App\Filament\Resources\ProviderResource\Pages;

use App\Filament\Resources\ProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class EditProvider extends EditRecord
{
    protected static string $resource = ProviderResource::class;
    
    protected array $userData = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load user data into the form
        if ($this->record->user) {
            $data['user'] = [
                'first_name' => $this->record->user->first_name,
                'last_name' => $this->record->user->last_name,
                'email' => $this->record->user->email,
                'phone' => $this->record->user->phone,
                'is_active' => $this->record->user->is_active,
                'lat' => $this->record->user->lat,
                'long' => $this->record->user->long,
            ];
        }
        
        // Load store_name translation data into form
        if ($this->record->store_name) {
            $storeNameData = is_array($this->record->store_name) 
                ? $this->record->store_name 
                : json_decode($this->record->store_name, true) ?? [];
            
            $data['store_name.ar'] = $storeNameData['ar'] ?? '';
            $data['store_name.en'] = $storeNameData['en'] ?? '';
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract user data from the form
        $userData = $data['user'] ?? [];
        unset($data['user']);
        
        // Store user data for later use
        $this->userData = $userData;
        
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            // Handle store_name translation data properly
            if (isset($data['store_name.ar']) || isset($data['store_name.en'])) {
                $data['store_name'] = [
                    'ar' => $data['store_name.ar'] ?? '',
                    'en' => $data['store_name.en'] ?? '',
                ];
                unset($data['store_name.ar'], $data['store_name.en']);
            }
            
            // Update the provider record first
            $record->update($data);
            
            // Update the user record with the form data
            $userData = $this->userData ?? [];
            
            if ($record->user && !empty($userData)) {
                $record->user->update([
                    'first_name' => $userData['first_name'] ?? $record->user->first_name,
                    'last_name' => $userData['last_name'] ?? $record->user->last_name,
                    'email' => $userData['email'] ?? $record->user->email,
                    'phone' => $userData['phone'] ?? $record->user->phone,
                    'is_active' => $userData['is_active'] ?? $record->user->is_active,
                    'lat' => $userData['lat'] ?? $record->user->lat,
                    'long' => $userData['long'] ?? $record->user->long,
                    'city_id' => $data['city_id'] ?? $record->user->city_id,
                ]);
            }
            
            // Refresh the record to get updated relationships
            return $record->fresh(['user', 'category', 'city']);
        });
    }
}
