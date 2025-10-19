<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use App\Models\Provider;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?int $navigationSort = 5;

    public static function getLabel(): ?string
    {
        return __('Subscription');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Subscriptions');
    }

    public static function getNavigationLabel(): string
    {
        return __('Subscriptions');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Subscriptions & Packages');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Subscription Information'))
                    ->schema([
                        Forms\Components\Select::make('provider_id')
                            ->label(__('Provider'))
                            ->options(function () {
                                return Provider::with('user')->get()->mapWithKeys(function ($provider) {
                                    $storeName = is_array($provider->store_name) 
                                        ? ($provider->store_name['en'] ?? $provider->store_name['ar'] ?? 'N/A')
                                        : $provider->store_name;
                                    
                                    $userName = $provider->user 
                                        ? "{$provider->user->first_name} {$provider->user->last_name}"
                                        : 'N/A';
                                    
                                    return [$provider->id => "{$storeName} ({$userName})"];
                                });
                            })
                            ->searchable()
                            ->required()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Reset package when provider changes
                                $set('package_id', null);
                            })
                            ->columnSpanFull(),

                        Forms\Components\Select::make('package_id')
                            ->label(__('Package'))
                            ->options(function () {
                                return Package::all()->mapWithKeys(function ($package) {
                                    $name = is_array($package->name) 
                                        ? ($package->name['en'] ?? $package->name['ar'] ?? 'N/A')
                                        : $package->name;
                                    
                                    return [$package->id => "{$name} - " . number_format($package->final_price, 2) . " SAR ({$package->duration} days)"];
                                });
                            })
                            ->searchable()
                            ->required()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $package = Package::find($state);
                                    if ($package) {
                                        // Set total based on package final price
                                        $set('total', $package->final_price);
                                        
                                        // Calculate end date based on start date and package duration
                                        $startDate = $get('start_date');
                                        if ($startDate) {
                                            $endDate = Carbon::parse($startDate)->addDays($package->duration)->format('Y-m-d');
                                            $set('end_date', $endDate);
                                        }
                                    }
                                }
                            })
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('Subscription Details'))
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('Start Date'))
                            ->required()
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $packageId = $get('package_id');
                                if ($state && $packageId) {
                                    $package = Package::find($packageId);
                                    if ($package) {
                                        $endDate = Carbon::parse($state)->addDays($package->duration)->format('Y-m-d');
                                        $set('end_date', $endDate);
                                    }
                                }
                            }),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('End Date'))
                            ->required()
                            ->after('start_date')
                            ->disabled(fn (callable $get) => !$get('package_id'))
                            ->helperText(__('End date is automatically calculated based on package duration')),

                        Forms\Components\TextInput::make('total')
                            ->label(__('Total Amount'))
                            ->required()
                            ->numeric()
                            ->prefix('SAR')
                            ->minValue(0)
                            ->disabled(fn (callable $get) => !$get('package_id'))
                            ->helperText(__('Total is automatically set based on package price')),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Is Active'))
                            ->default(true)
                            ->required()
                            ->helperText(__('Set whether this subscription is currently active')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('provider.store_name')
                    ->label(__('Provider'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $storeName = is_array($record->provider->store_name) 
                            ? ($record->provider->store_name['en'] ?? $record->provider->store_name['ar'] ?? 'N/A')
                            : $record->provider->store_name;
                        return $storeName;
                    }),

                Tables\Columns\TextColumn::make('provider.user.first_name')
                    ->label(__('Provider Name'))
                    ->searchable(['users.first_name', 'users.last_name'])
                    ->formatStateUsing(function ($record) {
                        if ($record->provider && $record->provider->user) {
                            return "{$record->provider->user->first_name} {$record->provider->user->last_name}";
                        }
                        return 'N/A';
                    }),

                Tables\Columns\TextColumn::make('package.name')
                    ->label(__('Package'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        if ($record->package) {
                            $name = is_array($record->package->name) 
                                ? ($record->package->name['en'] ?? $record->package->name['ar'] ?? 'N/A')
                                : $record->package->name;
                            return $name;
                        }
                        return 'N/A';
                    }),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->date()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        // Handle both date and string formats
                        try {
                            return Carbon::parse($state)->format('Y-m-d');
                        } catch (\Exception $e) {
                            return $state;
                        }
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label(__('Total'))
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('provider_id')
                    ->label(__('Provider'))
                    ->options(function () {
                        return Provider::with('user')->get()->mapWithKeys(function ($provider) {
                            $storeName = is_array($provider->store_name) 
                                ? ($provider->store_name['en'] ?? $provider->store_name['ar'] ?? 'N/A')
                                : $provider->store_name;
                            return [$provider->id => $storeName];
                        });
                    })
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('package_id')
                    ->label(__('Package'))
                    ->options(function () {
                        return Package::all()->mapWithKeys(function ($package) {
                            $name = is_array($package->name) 
                                ? ($package->name['en'] ?? $package->name['ar'] ?? 'N/A')
                                : $package->name;
                            return [$package->id => $name];
                        });
                    })
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active Status'))
                    ->boolean()
                    ->trueLabel(__('Active Only'))
                    ->falseLabel(__('Inactive Only'))
                    ->native(false),

                Tables\Filters\Filter::make('active_subscriptions')
                    ->label(__('Currently Active'))
                    ->query(fn (Builder $query): Builder => 
                        $query->where('is_active', true)
                              ->where('end_date', '>=', now())
                    ),

                Tables\Filters\Filter::make('expired_subscriptions')
                    ->label(__('Expired'))
                    ->query(fn (Builder $query): Builder => 
                        $query->where('end_date', '<', now())
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label(__('Activate Selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        }),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label(__('Deactivate Selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        }),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Provider Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('provider.store_name')
                            ->label(__('Store Name'))
                            ->formatStateUsing(function ($record) {
                                if ($record->provider) {
                                    $storeName = is_array($record->provider->store_name) 
                                        ? ($record->provider->store_name['en'] ?? $record->provider->store_name['ar'] ?? 'N/A')
                                        : $record->provider->store_name;
                                    return $storeName;
                                }
                                return 'N/A';
                            }),

                        Infolists\Components\TextEntry::make('provider.user.first_name')
                            ->label(__('Provider Name'))
                            ->formatStateUsing(function ($record) {
                                if ($record->provider && $record->provider->user) {
                                    return "{$record->provider->user->first_name} {$record->provider->user->last_name}";
                                }
                                return 'N/A';
                            }),

                        Infolists\Components\TextEntry::make('provider.user.email')
                            ->label(__('Email'))
                            ->formatStateUsing(function ($record) {
                                return $record->provider->user->email ?? 'N/A';
                            }),

                        Infolists\Components\TextEntry::make('provider.user.phone')
                            ->label(__('Phone'))
                            ->formatStateUsing(function ($record) {
                                return $record->provider->user->phone ?? 'N/A';
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make(__('Package Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('package.name')
                            ->label(__('Package Name'))
                            ->formatStateUsing(function ($record) {
                                if ($record->package) {
                                    $name = is_array($record->package->name) 
                                        ? ($record->package->name['en'] ?? $record->package->name['ar'] ?? 'N/A')
                                        : $record->package->name;
                                    return $name;
                                }
                                return 'N/A';
                            }),

                        Infolists\Components\TextEntry::make('package.duration')
                            ->label(__('Duration'))
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record || !$record->package) {
                                    return 'N/A';
                                }
                                return $record->package->duration . ' ' . __('days');
                            }),

                        Infolists\Components\TextEntry::make('package.price')
                            ->label(__('Original Price'))
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record || !$record->package) {
                                    return 'N/A';
                                }
                                return number_format($record->package->price, 2) . ' SAR';
                            }),

                        Infolists\Components\TextEntry::make('package.final_price')
                            ->label(__('Final Price'))
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record || !$record->package) {
                                    return 'N/A';
                                }
                                return number_format($record->package->final_price, 2) . ' SAR';
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make(__('Subscription Details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('start_date')
                            ->label(__('Start Date'))
                            ->date(),

                        Infolists\Components\TextEntry::make('end_date')
                            ->label(__('End Date'))
                            ->date()
                            ->formatStateUsing(function ($state) {
                                try {
                                    return Carbon::parse($state)->format('Y-m-d');
                                } catch (\Exception $e) {
                                    return $state;
                                }
                            }),

                        Infolists\Components\TextEntry::make('total')
                            ->label(__('Total Amount Paid'))
                            ->money('SAR'),

                        Infolists\Components\IconEntry::make('is_active')
                            ->label(__('Status'))
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),



                        
                    ])->columns(3),

                Infolists\Components\Section::make(__('Timestamps'))
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label(__('Updated At'))
                            ->dateTime(),
                    ])->columns(2),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)
            ->where('end_date', '>=', now())
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

