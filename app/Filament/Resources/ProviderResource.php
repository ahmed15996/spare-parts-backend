<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Models\User;
use App\Models\City;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?int $navigationSort = 2;

    public static function getLabel(): ?string
    {
        return __('Provider');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Providers');
    }

    public static function getNavigationLabel(): string
    {
        return __('Providers');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('User Information'))
                    ->schema([

                        Forms\Components\Toggle::make('user.is_active')
                            ->label(__('Active'))
                            ->default(true)
                            ->columnSpanFull()
                            ,
                        Forms\Components\TextInput::make('user.first_name')
                            ->label(__('First Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('user.last_name')
                            ->label(__('Last Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('user.email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('user.phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('user.password')
                            ->label(__('Password'))
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? \Illuminate\Support\Facades\Hash::make($state) : null)
                            ->dehydrated(fn ($state) => !empty($state))
                            ->helperText(__('Leave blank to generate a random password'))
                            ->maxLength(255)
                            ->revealable()
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                        
                        Forms\Components\TextInput::make('user.lat')
                            ->label(__('Latitude'))
                            ->numeric()
                            ->step(0.000001)
                            ->placeholder('e.g. 24.7136'),
                        Forms\Components\TextInput::make('user.long')
                            ->label(__('Longitude'))
                            ->numeric()
                            ->step(0.000001)
                            ->placeholder('e.g. 46.6753'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Provider Information'))
                    ->schema([
                        Forms\Components\TextInput::make('store_name.ar')
                            ->label(__('Store Name (Arabic)'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('store_name.en')
                            ->label(__('Store Name (English)'))
                            ->maxLength(255),
                        Forms\Components\Select::make('category_id')
                            ->label(__('Category'))
                            ->options(Category::pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                            Forms\Components\Select::make('city_id')
                            ->label(__('City'))
                            ->options(City::pluck('name', 'id')->toArray())
                            ->searchable()
                            ->nullable()
                            ->formatStateUsing(function ($state) {
                                return $state ?? 0;
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return $state == 0 ? null : $state;
                            }),
                        Forms\Components\Select::make('brands')
                            ->label(__('Brands'))
                            ->multiple()
                            ->relationship('brands', 'name')
                            ->options(function () {
                                return Brand::all()->mapWithKeys(function ($brand) {
                                    $name = is_array($brand->name) 
                                        ? ($brand->name[app()->getLocale()] ?? $brand->name['ar'] ?? $brand->name['en'] ?? '')
                                        : $brand->name;
                                    return [$brand->id => $name];
                                });
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(__('Select the brands this provider works with'))
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('commercial_number')
                            ->label(__('Commercial Number'))
                            ->required()
                            ->maxLength(255),
                           
                        Forms\Components\Textarea::make('location')
                            ->label(__('Address'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Documents'))
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                            ->label(__('Logo'))
                            ->collection('logo')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg']),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('commercial_number_image')
                            ->label(__('Commercial Number Image'))
                            ->collection('commercial_number_image')
                            ->image()
                            ->imageEditor()
                            ->required()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg']),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\TextColumn::make('store_name')
                        ->label(__('Store Name'))
                        ->formatStateUsing(function ($state, $record) {
                            return is_array($state) ? ($state['ar'] ?? '') : ($record->getTranslation('store_name', 'ar') ?? '');
                        })
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('Email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label(__('Phone'))
                    ->searchable(),
               
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->searchable(),
              
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('user.is_active')
                    ->label(__('Active'))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] !== null) {
                            $query->whereHas('user', function (Builder $query) use ($data) {
                                $query->where('is_active', $data['value']);
                            });
                        }
                    }),
                Tables\Filters\TernaryFilter::make('user.is_verified')
                    ->label(__('Verified'))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] !== null) {
                            $query->whereHas('user', function (Builder $query) use ($data) {
                                $query->where('is_verified', $data['value']);
                            });
                        }
                    }),
                Tables\Filters\SelectFilter::make('city_id')
                    ->label(__('City'))
                    ->options(City::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('Category'))
                    ->options(Category::pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(__('Delete Provider and All Associated Data?'))
                    ->modalDescription(function (Provider $record) {
                        $counts = [
                            'products' => $record->products()->count(),
                            'offers' => $record->offers()->count(),
                            'posts' => $record->posts()->count(),
                            'reviews' => $record->reviews()->count(),
                            'subscriptions' => $record->subscriptions()->count(),
                            'banners' => $record->banners()->count(),
                        ];
                        
                        $total = array_sum($counts);
                        
                        if ($total > 0) {
                            $details = [];
                            if ($counts['products'] > 0) $details[] = __(':count product(s)', ['count' => $counts['products']]);
                            if ($counts['offers'] > 0) $details[] = __(':count offer(s)', ['count' => $counts['offers']]);
                            if ($counts['posts'] > 0) $details[] = __(':count post(s)', ['count' => $counts['posts']]);
                            if ($counts['reviews'] > 0) $details[] = __(':count review(s)', ['count' => $counts['reviews']]);
                            if ($counts['subscriptions'] > 0) $details[] = __(':count subscription(s)', ['count' => $counts['subscriptions']]);
                            if ($counts['banners'] > 0) $details[] = __(':count banner(s)', ['count' => $counts['banners']]);
                            
                            return __('This provider has associated data: :details. All this data will be permanently deleted. Do you want to continue?', [
                                'details' => implode(', ', $details)
                            ]);
                        }
                        
                        return __('Are you sure you want to delete this provider? This will also delete the associated user account.');
                    })
                    ->modalSubmitActionLabel(__('Yes, Delete'))
                    ->before(function (Provider $record) {
                        $counts = [
                            'products' => $record->products()->count(),
                            'offers' => $record->offers()->count(),
                            'posts' => $record->posts()->count(),
                            'comments' => $record->comments()->count(),
                            'reviews' => $record->reviews()->count(),
                            'subscriptions' => $record->subscriptions()->count(),
                            'banners' => $record->banners()->count(),
                            'favourites' => $record->favourites()->count(),
                            'reports' => $record->reports()->count(),
                        ];
                        
                        // Store the total count for the notification
                        session()->put('provider_deleted_count_' . $record->id, array_sum($counts));
                        
                        // Delete all associated data (but NOT the user yet)
                        $record->products()->delete();
                        $record->offers()->delete();
                        $record->posts()->delete();
                        $record->comments()->delete();
                        $record->reviews()->delete();
                        $record->subscriptions()->delete();
                        $record->banners()->delete();
                        $record->favourites()->delete();
                        $record->reports()->delete();
                        $record->hiddenRequests()->delete();
                        $record->days()->delete(); // DayProvider pivot records
                        
                        // Detach many-to-many relationships
                        $record->brands()->detach();
                        
                        // Store user ID to delete after provider is deleted
                        session()->put('provider_user_to_delete', $record->user_id);
                    })
                    ->after(function (Provider $record) {
                        // Delete the user after the provider has been deleted
                        $userId = session()->pull('provider_user_to_delete');
                        if ($userId) {
                            User::find($userId)?->delete();
                        }
                        
                        // Show notification
                        $total = session()->pull('provider_deleted_count_' . $record->id, 0);
                        if ($total > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('Provider Data Deleted'))
                                ->body(__('Provider and :count associated record(s) have been deleted.', ['count' => $total]))
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('Delete Providers and All Associated Data?'))
                        ->modalDescription(function ($records) {
                            $totalCounts = [
                                'products' => 0,
                                'offers' => 0,
                                'posts' => 0,
                                'reviews' => 0,
                                'subscriptions' => 0,
                                'banners' => 0,
                            ];
                            
                            foreach ($records as $record) {
                                $totalCounts['products'] += $record->products()->count();
                                $totalCounts['offers'] += $record->offers()->count();
                                $totalCounts['posts'] += $record->posts()->count();
                                $totalCounts['reviews'] += $record->reviews()->count();
                                $totalCounts['subscriptions'] += $record->subscriptions()->count();
                                $totalCounts['banners'] += $record->banners()->count();
                            }
                            
                            $total = array_sum($totalCounts);
                            
                            if ($total > 0) {
                                return __('The selected providers have :count associated record(s) in total. All this data will be permanently deleted. Do you want to continue?', ['count' => $total]);
                            }
                            
                            return __('Are you sure you want to delete the selected providers? This will also delete their user accounts.');
                        })
                        ->modalSubmitActionLabel(__('Yes, Delete All'))
                        ->before(function ($records) {
                            $totalDeleted = 0;
                            $userIdsToDelete = [];
                            
                            foreach ($records as $record) {
                                $counts = [
                                    'products' => $record->products()->count(),
                                    'offers' => $record->offers()->count(),
                                    'posts' => $record->posts()->count(),
                                    'comments' => $record->comments()->count(),
                                    'reviews' => $record->reviews()->count(),
                                    'subscriptions' => $record->subscriptions()->count(),
                                    'banners' => $record->banners()->count(),
                                    'favourites' => $record->favourites()->count(),
                                    'reports' => $record->reports()->count(),
                                ];
                                
                                $totalDeleted += array_sum($counts);
                                
                                // Delete all associated data
                                $record->products()->delete();
                                $record->offers()->delete();
                                $record->posts()->delete();
                                $record->comments()->delete();
                                $record->reviews()->delete();
                                $record->subscriptions()->delete();
                                $record->banners()->delete();
                                $record->favourites()->delete();
                                $record->reports()->delete();
                                $record->hiddenRequests()->delete();
                                $record->days()->delete();
                                $record->brands()->detach();
                                
                                // Store user ID to delete after providers are deleted
                                if ($record->user_id) {
                                    $userIdsToDelete[] = $record->user_id;
                                }
                            }
                            
                            // Store for after hook
                            session()->put('bulk_providers_deleted_count', $totalDeleted);
                            session()->put('bulk_providers_users_to_delete', $userIdsToDelete);
                        })
                        ->after(function () {
                            // Delete all users after providers have been deleted
                            $userIdsToDelete = session()->pull('bulk_providers_users_to_delete', []);
                            if (!empty($userIdsToDelete)) {
                                User::whereIn('id', $userIdsToDelete)->delete();
                            }
                            
                            // Show notification
                            $totalDeleted = session()->pull('bulk_providers_deleted_count', 0);
                            if ($totalDeleted > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->title(__('Providers Data Deleted'))
                                    ->body(__('Providers and :count associated record(s) have been deleted.', ['count' => $totalDeleted]))
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('User Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('user.first_name')
                            ->label(__('First Name')),
                        Infolists\Components\TextEntry::make('user.last_name')
                            ->label(__('Last Name')),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label(__('Email'))
                            ->icon('heroicon-m-envelope'),
                        Infolists\Components\TextEntry::make('user.phone')
                            ->label(__('Phone'))
                            ->icon('heroicon-m-phone'),
                            Infolists\Components\TextEntry::make('city_display')
                            ->label(__('City'))
                            ->state(function ($record) {
                                return $record->city?->name ?? __('All Cities');
                            }),
                        Infolists\Components\IconEntry::make('user.is_active')
                            ->label(__('Active'))
                            ->boolean(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Location Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('user.address')
                            ->label(__('Address'))
                            ->placeholder(__('Not provided')),
                        Infolists\Components\TextEntry::make('location')
                            ->label(__('Location'))
                            ->placeholder(__('Not provided')),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Provider Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('store_name')
                            ->label(__('Store Name (Arabic)'))
                            ->formatStateUsing(function ($state, $record) {
                                return is_array($state) ? ($state['ar'] ?? '') : ($record->getTranslation('store_name', 'ar') ?? '');
                            }),
                        Infolists\Components\TextEntry::make('store_name')
                            ->label(__('Store Name (English)'))
                            ->formatStateUsing(function ($state, $record) {
                                return is_array($state) ? ($state['en'] ?? '') : ($record->getTranslation('store_name', 'en') ?? '');
                            })
                            ->placeholder(__('Not provided')),
                        Infolists\Components\TextEntry::make('category.name')
                            ->label(__('Category')),
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('commercial_number')
                            ->label(__('Commercial Number')),
                        Infolists\Components\TextEntry::make('location')
                            ->label(__('Location'))
                            ->columnSpanFull(),

                            // Brands
                            Infolists\Components\TextEntry::make('brands')
                            ->label(__('Brands'))
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record || $record->brands->isEmpty()) return __('No brands');
                                
                                return $record->brands->pluck('name')->implode(', ');
                            })
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Documents'))
                    ->schema([
                        Infolists\Components\SpatieMediaLibraryImageEntry::make('logo')
                            ->label(__('Logo'))
                            ->collection('logo')
                            ->size(200)
                            ->placeholder(__('No logo uploaded'))
                            ->extraAttributes(['target' => '_blank'])
                            ->url(fn ($record) => $record->getFirstMediaUrl('logo') ?: null)
                            ->openUrlInNewTab(),
                            

                        Infolists\Components\SpatieMediaLibraryImageEntry::make('commercial_number_image')
                            ->label(__('Commercial Number Image'))
                            ->collection('commercial_number_image')
                            ->size(200)
                            ->placeholder(__('No document uploaded'))
                            ->extraAttributes(['target' => '_blank'])
                            ->url(fn ($record) => $record->getFirstMediaUrl('commercial_number_image') ?: null)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Product Statistics'))
                    ->schema([
                        Infolists\Components\TextEntry::make('total_products')
                            ->label(__('Total Products'))
                            ->state(function ($record) {
                                return $record->products()->count();
                            })
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('total_stock')
                            ->label(__('Total Stock (Pieces)'))
                            ->state(function ($record) {
                                return $record->products()->sum('stock');
                            })
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('published_products')
                            ->label(__('Published Products'))
                            ->state(function ($record) {
                                return $record->products()->where('published', true)->count();
                            })
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('average_rating')
                            ->label(__('Average Rating'))
                            ->state(function ($record) {
                                $rating = $record->getAverageRating();
                                return $rating > 0 ? number_format($rating, 1) . '/5' : __('No ratings yet');
                            })
                            ->badge()
                            ->color(fn ($state) => str_contains($state, 'No ratings') ? 'gray' : 'warning'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Client Reviews'))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('reviews')
                            ->label('')
                            ->state(function ($record) {
                                return $record->reviews()
                                    ->with('user')
                                    ->latest()
                                    ->limit(10)
                                    ->get()
                                    ->map(function ($review) {
                                        return [
                                            'user_name' => $review->user->first_name . ' ' . $review->user->last_name,
                                            'rating' => $review->rating,
                                            'comment' => $review->comment,
                                            'created_at' => $review->created_at->format('M d, Y'),
                                        ];
                                    });
                            })
                            ->schema([
                                Infolists\Components\TextEntry::make('user_name')
                                    ->label(__('Client'))
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('rating')
                                    ->label(__('Rating'))
                                    ->formatStateUsing(fn ($state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                                    ->color('warning'),
                                Infolists\Components\TextEntry::make('comment')
                                    ->label(__('Comment'))
                                    ->columnSpanFull()
                                    ->placeholder(__('No comment')),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('Date'))
                                    ->color('gray'),
                            ])
                            ->columns(2)
                            ->placeholder(__('No reviews yet')),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make(__('Account Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label(__('Updated At'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('user.roles.name')
                            ->label(__('Roles'))
                            ->badge()
                            ->separator(','),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'view' => Pages\ViewProvider::route('/{record}'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'category', 'city', 'brands']);
    }
}

