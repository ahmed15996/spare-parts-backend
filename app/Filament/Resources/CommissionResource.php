<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommissionResource\Pages;
use App\Models\Commission;
use App\Enums\CommissionType;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CommissionResource extends Resource
{
    protected static ?string $model = Commission::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 5;

    public static function getLabel(): ?string
    {
        return __('Commission');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Commissions');
    }

    public static function getNavigationLabel(): string
    {
        return __('Commissions');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Financial');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        CommissionType::Client => 'primary',
                        CommissionType::Provider => 'success',
                    }),

                Tables\Columns\TextColumn::make('user_display')
                    ->label(__('User/Provider'))
                    ->state(function ($record) {
                        $user = $record->user;
                        if (!$user) return __('Unknown');
                        
                        // Use the name attribute from User model which handles provider/client logic
                        return $user->name;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('EGP', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label(__('Value'))
                    ->money('EGP', true)
                    ->sortable(),

                Tables\Columns\IconColumn::make('payed')
                    ->label(__('Paid'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        CommissionType::Client->value => CommissionType::Client->label(),
                        CommissionType::Provider->value => CommissionType::Provider->label(),
                    ]),
                
                Tables\Filters\TernaryFilter::make('payed')
                    ->label(__('Paid')),

                Tables\Filters\Filter::make('search')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('query')
                            ->label(__('Search'))
                            ->placeholder(__('Search by name, email, or phone...'))
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['query'],
                            fn (Builder $query, $search): Builder => $query->whereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where(function ($q) use ($search) {
                                    // Search in basic user fields
                                    $q->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%")
                                      ->orWhere('phone', 'like', "%{$search}%")
                                      // Search in provider store names
                                      ->orWhereHas('provider', function ($providerQuery) use ($search) {
                                          $providerQuery->where('store_name->ar', 'like', "%{$search}%")
                                                      ->orWhere('store_name->en', 'like', "%{$search}%");
                                      });
                                });
                            })
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['query']) {
                            return null;
                        }
                        return __('Search') . ': ' . $data['query'];
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for read-only resource
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Commission Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label(__('ID')),
                        
                        Infolists\Components\TextEntry::make('type')
                            ->label(__('Type'))
                            ->formatStateUsing(fn ($state) => $state->label())
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                CommissionType::Client => 'primary',
                                CommissionType::Provider => 'success',
                            }),
                        
                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('Amount'))
                            ->money('EGP', true),
                        
                        Infolists\Components\TextEntry::make('value')
                            ->label(__('Value'))
                            ->money('EGP', true),
                        
                        Infolists\Components\IconEntry::make('payed')
                            ->label(__('Paid'))
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('User/Provider Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('user_display')
                            ->label(__('Name'))
                            ->state(function ($record) {
                                $user = $record->user;
                                if (!$user) return __('Unknown');
                                
                                // Use the name attribute from User model
                                return $user->name;
                            }),
                        
                        Infolists\Components\TextEntry::make('user.email')
                            ->label(__('Email'))
                            ->icon('heroicon-m-envelope'),
                        
                        Infolists\Components\TextEntry::make('user.phone')
                            ->label(__('Phone'))
                            ->icon('heroicon-m-phone'),
                        
                        Infolists\Components\TextEntry::make('user_type')
                            ->label(__('User Type'))
                            ->state(function ($record) {
                                $user = $record->user;
                                if (!$user) return __('Unknown');
                                
                                if ($user->hasRole('provider')) {
                                    return __('Provider');
                                } elseif ($user->hasRole('client')) {
                                    return __('Client');
                                }
                                
                                return __('User');
                            })
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                __('Provider') => 'success',
                                __('Client') => 'primary',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                // Show commission products only for provider role
                Infolists\Components\Section::make(__('Commission Products Details'))
                    ->schema(function ($record) {
                        $user = $record->user;
                        if (!$user || !$user->hasRole('provider')) {
                            return [
                                Infolists\Components\TextEntry::make('no_products')
                                    ->label('')
                                    ->state(__('No commission products available'))
                                    ->columnSpanFull(),
                            ];
                        }
                        
                        $items = $record->items()->with('product')->get();
                        
                        if ($items->isEmpty()) {
                            return [
                                Infolists\Components\TextEntry::make('no_products')
                                    ->label('')
                                    ->state(__('No commission products available'))
                                    ->columnSpanFull(),
                            ];
                        }
                        
                        $schema = [];
                        foreach ($items as $index => $item) {
                            $product = $item->product;
                            $totalSold = $item->pieces;
                            $totalValue = $item->value;
                            $unitPrice = $product ? $product->price : 0;
                            $availableStock = $product ? $product->stock : 0;
                            
                            // Product Header
                            $schema[] = Infolists\Components\TextEntry::make("product_{$index}_header")
                                ->label('')
                                ->state("📦 " . ($product->name ?? __('Unknown Product')))
                                ->weight('bold')
                                ->color('primary')
                                ->columnSpanFull();
                            
                            // Product Description
                            if ($product && $product->description) {
                                $schema[] = Infolists\Components\TextEntry::make("product_{$index}_description")
                                    ->label(__('Description'))
                                    ->state($product->description)
                                    ->columnSpanFull()
                                    ->color('gray');
                            }
                            
                            // Available Quantity
                            $schema[] = Infolists\Components\TextEntry::make("product_{$index}_available")
                                ->label(__('Available Quantity'))
                                ->state($availableStock)
                                ->badge()
                                ->color($availableStock > 0 ? 'success' : 'danger')
                                ->icon($availableStock > 0 ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle');
                            
                            // Sold Quantity
                            $schema[] = Infolists\Components\TextEntry::make("product_{$index}_sold")
                                ->label(__('Sold Quantity'))
                                ->state($totalSold)
                                ->badge()
                                ->color('info')
                                ->icon('heroicon-o-shopping-cart');
                            
                            // Unit Price
                            $schema[] = Infolists\Components\TextEntry::make("product_{$index}_unit_price")
                                ->label(__('Unit Price'))
                                ->state($unitPrice)
                                ->money('EGP', true)
                                ->color('warning');
                            
                            // Total Sold Value
                            $totalSoldValue = $totalSold * $unitPrice;
                            $schema[] = Infolists\Components\TextEntry::make("product_{$index}_total_sold")
                                ->label(__('Total Sold Value'))
                                ->state($totalSoldValue)
                                ->money('EGP', true)
                                ->color('success')
                                ->weight('bold');
                            
                            // Commission Value
                            $schema[] = Infolists\Components\TextEntry::make("product_{$index}_commission")
                                ->label(__('Commission Value'))
                                ->state($totalValue)
                                ->money('EGP', true)
                                ->color('primary')
                                ->weight('bold')
                                ->icon('heroicon-o-currency-dollar');
                            
                            // Commission Percentage
                            $commissionPercentage = $totalSoldValue > 0 ? ($totalValue / $totalSoldValue) * 100 : 0;
                            $schema[] = Infolists\Components\TextEntry::make("product_{$index}_commission_percentage")
                                ->label(__('Commission %'))
                                ->state(number_format($commissionPercentage, 2) . '%')
                                ->badge()
                                ->color($commissionPercentage > 10 ? 'success' : ($commissionPercentage > 5 ? 'warning' : 'danger'));
                            
                            // Separator line between products
                            if ($index < count($items) - 1) {
                                $schema[] = Infolists\Components\TextEntry::make("product_{$index}_separator")
                                    ->label('')
                                    ->state('')
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'border-b border-gray-200 dark:border-gray-700 my-4']);
                            }
                        }
                        
                        return $schema;
                    })
                    ->columns(4)
                    ->collapsible()
                    ->visible(function ($record) {
                        $user = $record->user;
                        return $user && $user->hasRole('provider');
                    }),

                Infolists\Components\Section::make(__('Timestamps'))
                    ->schema([
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommissions::route('/'),
            'view' => Pages\ViewCommission::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user.provider', 'items.product']);
    }
}
