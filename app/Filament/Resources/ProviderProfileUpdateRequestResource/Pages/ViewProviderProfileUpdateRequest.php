<?php

namespace App\Filament\Resources\ProviderProfileUpdateRequestResource\Pages;

use App\Filament\Resources\ProviderProfileUpdateRequestResource;
use App\Services\ProviderProfileUpdateService;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ViewProviderProfileUpdateRequest extends ViewRecord
{
    protected static string $resource = ProviderProfileUpdateRequestResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Only show actions if the request is pending
        if ($this->record->isPending()) {
            $actions[] = Actions\Action::make('approve')
                ->label(__('Approve Request'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('Approve Profile Update Request'))
                ->modalDescription(__('Are you sure you want to approve this profile update request? This will update the provider\'s profile with the requested changes.'))
                ->modalSubmitActionLabel(__('Yes, Approve'))
                ->action(function () {
                    $this->approveRequest();
                });

            $actions[] = Actions\Action::make('reject')
                ->label(__('Reject Request'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label(__('Rejection Reason'))
                        ->placeholder(__('Please provide a reason for rejection (optional)'))
                        ->rows(3),
                ])
                ->modalHeading(__('Reject Profile Update Request'))
                ->modalDescription(__('Are you sure you want to reject this profile update request?'))
                ->modalSubmitActionLabel(__('Yes, Reject'))
                ->action(function (array $data) {
                    $this->rejectRequest($data['reason'] ?? null);
                });
        }

        return $actions;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $provider = $this->record->provider->load('user');
        
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Request Information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label(__('Request ID')),
                                    
                                Infolists\Components\TextEntry::make('status')
                                    ->label(__('Status'))
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => match($state) {
                                        0 => __('Pending'),
                                        1 => __('Approved'),
                                        2 => __('Rejected'),
                                        default => __('Unknown')
                                    })
                                    ->color(fn ($state) => match($state) {
                                        0 => 'warning',
                                        1 => 'success',
                                        2 => 'danger',
                                        default => 'gray'
                                    }),
                                    
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('Requested At'))
                                    ->dateTime(),
                            ]),
                            
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('processed_at')
                                    ->label(__('Processed At'))
                                    ->dateTime()
                                    ->placeholder(__('Not processed yet')),
                                    
                                Infolists\Components\TextEntry::make('processedBy.name')
                                    ->label(__('Processed By'))
                                    ->placeholder(__('Not processed yet')),
                                    
                                Infolists\Components\TextEntry::make('admin_notes')
                                    ->label(__('Admin Notes'))
                                    ->placeholder(__('No notes'))
                                    ->columnSpan(1),
                            ])
                            ->visible(fn () => !$this->record->isPending()),
                    ]),

                Infolists\Components\Section::make(__('Provider Information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.user.first_name')
                                    ->label(__('Provider Name'))
                                    ->formatStateUsing(fn () => 
                                        $provider->user->first_name . ' ' . $provider->user->last_name
                                    ),
                                    
                                Infolists\Components\TextEntry::make('provider.user.phone')
                                    ->label(__('Phone')),
                                    
                                Infolists\Components\TextEntry::make('provider.user.email')
                                    ->label(__('Email')),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('Comparison: Current vs Requested Data'))
                    ->schema([
                        // Store Name Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.store_name')
                                    ->label(__('Current Store Name'))
                                    ->formatStateUsing(function () use ($provider) {
                                        $ar = $provider->getTranslation('store_name', 'ar');
                                        $en = $provider->getTranslation('store_name', 'en');
                                        
                                        $parts = [];
                                        if ($ar) {
                                            $parts[] = "AR " . $ar;
                                        }
                                        if ($en) {
                                            $parts[] = "EN " . $en;
                                        }
                                        
                                        return !empty($parts) ? implode('<br>', $parts) : '-';
                                    })
                                    ->html(),
                                    
                                Infolists\Components\TextEntry::make('store_name')
                                    ->label(__('Requested Store Name'))
                                    ->formatStateUsing(function () {
                                        $ar = $this->record['store_name']['ar'];
                                        $en = $this->record['store_name']['en'];
                                        
                                        $parts = [];
                                        if ($ar) {
                                            $parts[] = "AR " . $ar;
                                        }
                                        if ($en) {
                                            $parts[] = "EN " . $en;
                                        }
                                        
                                        return !empty($parts) ? implode('<br>', $parts) : __('No change requested');
                                    })
                                    ->html()
                                    ->color(fn () => 
                                        $this->record->store_name && $this->isFieldChanged('store_name') ? 'warning' : null
                                    ),
                                ]),
                            // ->visible(fn () => $this->record->getRawOriginal('store_name') !== null),
                            
                        // Description Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.description')
                                    ->label(__('Current Description'))
                                    ->formatStateUsing(fn ($state) => $state ?? '-')
                                    ->columnSpanFull()
                                    ->html(),
                                    
                                Infolists\Components\TextEntry::make('description')
                                    ->label(__('Requested Description'))
                                    ->formatStateUsing(fn ($state) => $state ?? __('No change requested'))
                                    ->columnSpanFull()
                                    ->html()
                                    ->color(fn () => 
                                        $this->record->description && $this->isFieldChanged('description') ? 'warning' : null
                                    ),
                            ])
                            ->visible(fn () => $this->record->description !== null && $this->isFieldChanged('description')),
                            
                        // Category Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.category.name')
                                    ->label(__('Current Category'))
                                    ->formatStateUsing(fn ($state) => $state ?? '-'),
                                    
                                Infolists\Components\TextEntry::make('category.name')
                                    ->label(__('Requested Category'))
                                    ->default(__('No change requested'))
                                    ->color(fn () => 
                                        $this->record->category_id && $this->isFieldChanged('category_id') ? 'warning' : null
                                    ),
                            ])
                            ->visible(fn () => $this->record->category_id !== null && $this->isFieldChanged('category_id')),
                            
                        // City Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.city.name')
                                    ->label(__('Current City'))
                                    ->formatStateUsing(fn ($state) => $state ?? __('All Cities')),
                                    
                                Infolists\Components\TextEntry::make('city.name')
                                    ->label(__('Requested City'))
                                    ->formatStateUsing(fn ($state) => $state ?? __('All Cities'))
                                    ->color(fn () => 
                                        $this->isFieldChanged('city_id') ? 'warning' : null
                                    ),
                            ])
                            ->visible(fn () => $this->record->city_id !== null && $this->isFieldChanged('city_id')),
                            
                        // Commercial Number Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.commercial_number')
                                    ->label(__('Current Commercial Number'))
                                    ->formatStateUsing(fn ($state) => $state ?? '-'),
                                    
                                Infolists\Components\TextEntry::make('commercial_number')
                                    ->label(__('Requested Commercial Number'))
                                    ->default(__('No change requested'))
                                    ->color(fn () => 
                                        $this->record->commercial_number && $this->isFieldChanged('commercial_number') ? 'warning' : null
                                    ),
                            ])
                            ->visible(fn () => $this->record->commercial_number !== null && $this->isFieldChanged('commercial_number')),
                            
                        // Location Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.location')
                                    ->label(__('Current Location'))
                                    ->formatStateUsing(fn ($state) => $state ?? '-')
                                    ->columnSpanFull(),
                                    
                                Infolists\Components\TextEntry::make('location')
                                    ->label(__('Requested Location'))
                                    ->formatStateUsing(fn ($state) => $state ?? __('No change requested'))
                                    ->columnSpanFull()
                                    ->color(fn () => 
                                        $this->record->location && $this->isFieldChanged('location') ? 'warning' : null
                                    ),
                            ])
                            ->visible(fn () => $this->record->location !== null && $this->isFieldChanged('location')),
                            
                        // Address Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.user.address')
                                    ->label(__('Current address'))
                                    ->formatStateUsing(function ($state) use ($provider) {
                                        // Try multiple ways to get the address
                                        $address = $state ?? $provider->user?->address ?? null;
                                        
                                        if ($address) {
                                            return "Address: {$address}";
                                        }
                                       
                                        return __('No address set');
                                    }),
                                    
                                Infolists\Components\TextEntry::make('address')
                                    ->label(__('Requested address'))
                                    ->formatStateUsing(function () {
                                        $address = $this->record->address;
                                        
                                        if ($address) {
                                                return "Address: {$address}";
                                        }
                                       
                                        return __('No change requested');
                                    })
                                    ->color(fn () => 
                                        ($this->record->address) && 
                                        ($this->isFieldChanged('address')) ? 'warning' : null
                                    ),
                            ])
                            ->visible(fn () => $this->record->address !== null || $provider->user?->address !== null),
                        // Brands Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.brands')
                                    ->label(__('Current Brands'))
                                    ->formatStateUsing(fn ($state) => 
                                        $provider->brands->pluck('name')->implode(', ') ?: __('No brands')
                                    ),
                                    
                                Infolists\Components\TextEntry::make('brands')
                                    ->label(__('Requested Brands'))
                                    ->formatStateUsing(function ($state) {
                                        if (empty($state)) return __('No change requested');
                                        
                                        $brandIds = is_array($state) ? $state : json_decode($state, true);
                                        
                                        // Flatten the array in case it's nested
                                        $brandIds = collect($brandIds)->flatten()->filter()->toArray();
                                        
                                        if (empty($brandIds)) return __('No change requested');
                                        
                                        return \App\Models\Brand::whereIn('id', $brandIds)
                                            ->pluck('name')
                                            ->implode(', ');
                                    })
                                    ->color(fn () => 
                                        $this->record->brands && $this->isFieldChanged('brands') ? 'warning' : null
                                    ),
                            ])
                            ->visible(fn () => $this->record->brands !== null && $this->isFieldChanged('brands')),
                            
                        // Logo Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.id')
                                    ->label(__('Current Logo'))
                                    ->formatStateUsing(fn ($state) => 
                                        $provider->getFirstMediaUrl('logo') 
                                            ? new HtmlString('<a href="' . $provider->getFirstMediaUrl('logo') . '" target="_blank" class="text-primary-600 hover:underline">' . __('View Current Logo') . '</a>')
                                            : __('No logo')
                                    ),
                                    
                                Infolists\Components\TextEntry::make('id')
                                    ->label(__('Requested Logo'))
                                    ->formatStateUsing(fn ($state) => 
                                        $this->record->getFirstMediaUrl('logo') 
                                            ? new HtmlString('<a href="' . $this->record->getFirstMediaUrl('logo') . '" target="_blank" class="text-primary-600 hover:underline">' . __('View Requested Logo') . '</a>')
                                            : __('No change requested')
                                    )
                                    ->color(fn () => 
                                        $this->record->getFirstMediaUrl('logo') ? 'warning' : null
                                    ),
                            ])
                            ->visible(fn () => $this->record->getFirstMediaUrl('logo') !== ''),
                            
                        // Commercial Number Image Comparison
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('provider.id')
                                    ->label(__('Current Commercial Number Image'))
                                    ->formatStateUsing(fn ($state) => 
                                        $provider->getFirstMediaUrl('commercial_number_image') 
                                            ? new HtmlString('<a href="' . $provider->getFirstMediaUrl('commercial_number_image') . '" target="_blank" class="text-primary-600 hover:underline">' . __('View Current Image') . '</a>')
                                            : __('No image')
                                    ),
                                    
                                Infolists\Components\TextEntry::make('id')
                                    ->label(__('Requested Commercial Number Image'))
                                    ->formatStateUsing(fn ($state) => 
                                        $this->record->getFirstMediaUrl('commercial_number_image') 
                                            ? new HtmlString('<a href="' . $this->record->getFirstMediaUrl('commercial_number_image') . '" target="_blank" class="text-primary-600 hover:underline">' . __('View Requested Image') . '</a>')
                                            : __('No change requested')
                                    )
                                    ->color(fn () => 
                                        $this->record->getFirstMediaUrl('commercial_number_image') ? 'warning' : null
                                    ),
                            ])
                            ->visible(fn () => $this->record->getFirstMediaUrl('commercial_number_image') !== ''),
                    ])
                    ->visible(fn () => $this->hasAnyChanges())
                    ->collapsible(),
            ]);
    }

    protected function approveRequest(): void
    {
        try {
            $service = app(ProviderProfileUpdateService::class);
            $service->approveUpdateRequest($this->record, Auth::user());

            Notification::make()
                ->title(__('Request Approved'))
                ->body(__('The profile update request has been approved successfully.'))
                ->success()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('Error'))
                ->body(__('Failed to approve request: :message', ['message' => $e->getMessage()]))
                ->danger()
                ->send();
        }
    }

    protected function rejectRequest(?string $reason): void
    {
        try {
            $service = app(ProviderProfileUpdateService::class);
            $service->rejectUpdateRequest($this->record, Auth::user(), $reason);

            Notification::make()
                ->title(__('Request Rejected'))
                ->body(__('The profile update request has been rejected.'))
                ->warning()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('Error'))
                ->body(__('Failed to reject request: :message', ['message' => $e->getMessage()]))
                ->danger()
                ->send();
        }
    }

    protected function isFieldChanged(string $field): bool
    {
        $provider = $this->record->provider->load('user');
        
        return match($field) {
            'store_name' => $this->checkStoreNameChanged($provider),
            'description' => $provider->description !== $this->record->description,
            'category_id' => $provider->category_id !== $this->record->category_id,
            'city_id' => $provider->city_id !== $this->record->city_id,
            'commercial_number' => $provider->commercial_number !== $this->record->commercial_number,
            'location' => $provider->location !== $this->record->location,
            'address' => $provider->user->address !== $this->record->address,
            'lat' => $provider->user->lat !== $this->record->lat,
            'long' => $provider->user->long !== $this->record->long,
            'brands' => !$this->areBrandsEqual($provider),
            default => false
        };
    }

    protected function checkStoreNameChanged($provider): bool
    {
        // Provider store_name is translatable (array), request store_name is simple string
        $currentStoreNameAr = $provider->getTranslation('store_name', 'ar');
        $requestedStoreName = $this->record->store_name;
        
        // Compare the Arabic version with the requested store name
        return $currentStoreNameAr !== $requestedStoreName;
    }

    protected function areBrandsEqual($provider): bool
    {
        $currentBrands = $provider->brands->pluck('id')->sort()->values()->toArray();
        $requestedBrands = is_array($this->record->brands) 
            ? collect($this->record->brands)->sort()->values()->toArray()
            : collect(json_decode($this->record->brands, true))->sort()->values()->toArray();
            
        return $currentBrands === $requestedBrands;
    }

    protected function hasAnyChanges(): bool
    {
        // Check if any field has actually been submitted with changes
        return ($this->record->store_name !== null && $this->isFieldChanged('store_name')) ||
               ($this->record->description !== null && $this->isFieldChanged('description')) ||
               ($this->record->category_id !== null && $this->isFieldChanged('category_id')) ||
               ($this->record->city_id !== null && $this->isFieldChanged('city_id')) ||
               ($this->record->commercial_number !== null && $this->isFieldChanged('commercial_number')) ||
               ($this->record->location !== null && $this->isFieldChanged('location')) ||
               ($this->record->address !== null && $this->isFieldChanged('address')) ||
               ($this->record->lat !== null && $this->isFieldChanged('lat')) ||
               ($this->record->long !== null && $this->isFieldChanged('long')) ||
               ($this->record->brands !== null && $this->isFieldChanged('brands')) ||
               $this->record->getFirstMediaUrl('logo') !== '' ||
               $this->record->getFirstMediaUrl('commercial_number_image') !== '';
    }
}
