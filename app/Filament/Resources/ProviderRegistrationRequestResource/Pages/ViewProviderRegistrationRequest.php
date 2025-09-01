<?php

namespace App\Filament\Resources\ProviderRegistrationRequestResource\Pages;

use App\Filament\Resources\ProviderRegistrationRequestResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Actions;
use App\Models\City;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\Provider;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ViewProviderRegistrationRequest extends ViewRecord
{
    protected static string $resource = ProviderRegistrationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('accept')
                ->label(__('Accept'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status == 0)
                ->requiresConfirmation()
                ->modalHeading(__('Accept Provider Registration'))
                ->modalDescription(__('Are you sure you want to accept this provider registration? This will create a user account and provider profile.'))
                ->action(function () {
                    $this->acceptRegistration();
                })
        ];
    }

    protected function acceptRegistration(): void
    {
        try {
            DB::transaction(function () {
                // Create User
                $user = User::create([
                    'first_name' => $this->record->first_name,
                    'last_name' => $this->record->last_name,
                    'email' => $this->record->email,
                    'phone' => $this->record->phone,
                    'city_id' => $this->record->city_id == 0 ? null : $this->record->city_id,
                    'lat' => $this->record->lat,
                    'long' => $this->record->long,
                    'address' => $this->record->address,
                    'is_active' => true,
                    'is_verified' => true,
                ]);

                // Assign provider role
                $user->assignRole('provider');

                // Create Provider
                $provider = Provider::create([
                    'user_id' => $user->id,
                    'store_name' => [
                        'ar' => $this->record->getTranslation('store_name', 'ar'),
                        'en' => $this->record->getTranslation('store_name', 'en'),
                    ],
                    'description' => $this->record->description,
                    'commercial_number' => $this->record->commercial_number,
                    'location' => $this->record->location,
                    'category_id' => $this->record->category_id,
                    'city_id' => $this->record->city_id == 0 ? null : $this->record->city_id,
                ]);

                // Ensure provider was created successfully with an ID
                if (!$provider || !$provider->id) {
                    throw new \Exception(__('Failed to create provider record'));
                }

                // Handle brands relationship
                if ($this->record->brands) {
                    $brandIds = is_string($this->record->brands) 
                        ? json_decode($this->record->brands, true) 
                        : $this->record->brands;
                    
                    if (is_array($brandIds) && !empty($brandIds)) {
                        // Filter out any invalid brand IDs
                        $validBrandIds = array_filter(array_map('intval', $brandIds), function($id) {
                            return $id > 0;
                        });
                        
                        if (!empty($validBrandIds)) {
                            $provider->brands()->sync($validBrandIds);
                        }
                    }
                }

                // Copy media files from registration request to provider
                foreach ($this->record->getMedia('logo') as $media) {
                    $media->copy($provider, 'logo');
                }
                
                foreach ($this->record->getMedia('commercial_number_image') as $media) {
                    $media->copy($provider, 'commercial_number_image');
                }

                // Update registration request status
                $this->record->update(['status' => 1]);
            });

            Notification::make()
                ->title(__('Registration Accepted'))
                ->body(__('Provider registration has been accepted successfully. User account and provider profile have been created.'))
                ->success()
                ->send();

            $this->redirect(ProviderRegistrationRequestResource::getUrl('index'));

        } catch (\Exception $e) {
            Notification::make()
                ->title(__('Error'))
                ->body(__('Failed to accept registration: ') . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('User Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('first_name')
                            ->label(__('First Name')),
                        Infolists\Components\TextEntry::make('last_name')
                            ->label(__('Last Name')),
                        Infolists\Components\TextEntry::make('phone')
                            ->label(__('Phone'))
                            ->icon('heroicon-m-phone'),
                        Infolists\Components\TextEntry::make('email')
                            ->label(__('Email'))
                            ->icon('heroicon-m-envelope'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Provider Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('store_name')
                            ->label(__('Store Name (Arabic)'))
                            ->formatStateUsing(function ($state, $record) {
                                return $record->getTranslation('store_name', 'ar');
                            }),
                        Infolists\Components\TextEntry::make('store_name')
                            ->label(__('Store Name (English)'))
                            ->formatStateUsing(function ($state, $record) {
                                return $record->getTranslation('store_name', 'en');
                            }),
                        Infolists\Components\TextEntry::make('city.name')
                            ->label(__('City'))
                            ->formatStateUsing(function ($state, $record) {
                                return $record->city_id ? $record->city->name : __('All Cities');
                            }),
                        Infolists\Components\TextEntry::make('category.name')
                            ->label(__('Category')),
                        Infolists\Components\TextEntry::make('brands')
                            ->label(__('Brands'))
                            ->formatStateUsing(function ($state, $record) {
                                if (!$state) return __('Not selected');
                                $brandIds = is_string($state) ? json_decode($state, true) : $state;
                                if (!is_array($brandIds)) return __('Not selected');
                                $brands = Brand::whereIn('id', array_map('intval', $brandIds))->get();
                                return $brands->map(function ($brand) {
                                    return $brand->getTranslation('name', app()->getLocale());
                                })->implode(', ');
                            }),
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('commercial_number')
                            ->label(__('Commercial Number')),
                        Infolists\Components\TextEntry::make('address')
                            ->label(__('Address'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Location Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('location')
                            ->label(__('Location'))
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('address')
                            ->label(__('Address'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Documents'))
                    ->schema([
                        Infolists\Components\SpatieMediaLibraryImageEntry::make('logo')
                            ->label(__('Logo'))
                            ->collection('logo')
                            ->size(200)
                            ->placeholder(__('No logo uploaded')),
                        Infolists\Components\SpatieMediaLibraryImageEntry::make('commercial_number_image')
                            ->label(__('Commercial Number Image'))
                            ->collection('commerciacommercial_documentsl_number_image')
                            ->size(200)
                            ->placeholder(__('No document uploaded')),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Status Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '0' => 'warning',
                                '1' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                '0' => __('Pending'),
                                '1' => __('Approved'),
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label(__('Updated At'))
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
