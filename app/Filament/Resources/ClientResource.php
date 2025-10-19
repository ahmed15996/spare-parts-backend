<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\User;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ClientResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 3;

    public static function getLabel(): ?string
    {
        return __('Client');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Clients');
    }

    public static function getNavigationLabel(): string
    {
        return __('Clients');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Client Information'))
                    ->schema([
                        Forms\Components\Hidden::make('type')
                            ->default(0), // Client type
                        Forms\Components\TextInput::make('first_name')
                            ->label(__('First Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label(__('Last Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->visibleOn('create'),
                        Forms\Components\Select::make('city_id')
                            ->label(__('City'))
                            ->options(City::pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true),
                        
                        Forms\Components\TextInput::make('lat')
                            ->label(__('Latitude'))
                            ->numeric(),
                        Forms\Components\TextInput::make('long')
                            ->label(__('Longitude'))
                            ->numeric(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label(__('First Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label(__('Last Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('City'))
                    ->searchable()
                    ->placeholder(__('Not specified')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
               
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active')),
               
                Tables\Filters\SelectFilter::make('city_id')
                    ->label(__('City'))
                    ->relationship('city', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(__('Delete Client and All Associated Data?'))
                    ->modalDescription(__('This will delete the client and all associated data. Are you sure you want to continue?'))
                    ->modalSubmitActionLabel(__('Yes, Delete'))
                    ->before(function (User $record) {
                        // Delete all associated data
                        $record->cars()->delete();
                        $record->requests()->delete();
                        $record->posts()->delete();
                        $record->reviews()->delete();
                        $record->favourites()->delete();
                        $record->reports()->delete();
                        $record->fcmTokens()->delete();
                        $record->blockedUsers()->delete(); // Blocks where this user is the blocker
                        $record->blockedByUsers()->delete(); // Blocks where this user is blocked
                        
                        // Delete comments manually to avoid relationship issues
                        DB::table('comments')->where('author_id', $record->id)->where('author_type', 'App\Models\User')->delete();
                        
                        \Filament\Notifications\Notification::make()
                            ->title(__('Client Deleted'))
                            ->body(__('Client and all associated data have been deleted.'))
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('Delete Clients and All Associated Data?'))
                        ->modalDescription(__('This will delete the selected clients and all associated data. Are you sure you want to continue?'))
                        ->modalSubmitActionLabel(__('Yes, Delete All'))
                        ->before(function ($records) {
                            $userIds = [];
                            
                            foreach ($records as $record) {
                                $userIds[] = $record->id;
                                
                                // Delete all associated data
                                $record->cars()->delete();
                                $record->requests()->delete();
                                $record->posts()->delete();
                                $record->reviews()->delete();
                                $record->favourites()->delete();
                                $record->reports()->delete();
                                $record->fcmTokens()->delete();
                                $record->blockedUsers()->delete();
                                $record->blockedByUsers()->delete();
                            }
                            
                            // Delete comments manually to avoid relationship issues
                            if (!empty($userIds)) {
                                DB::table('comments')->whereIn('author_id', $userIds)->where('author_type', 'App\Models\User')->delete();
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title(__('Clients Deleted'))
                                ->body(__('Selected clients and all associated data have been deleted.'))
                                ->warning()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Client Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('first_name')
                            ->label(__('First Name')),
                        Infolists\Components\TextEntry::make('last_name')
                            ->label(__('Last Name')),
                        Infolists\Components\TextEntry::make('email')
                            ->label(__('Email'))
                            ->icon('heroicon-m-envelope'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label(__('Phone'))
                            ->icon('heroicon-m-phone'),
                        Infolists\Components\TextEntry::make('city.name')
                            ->label(__('City'))
                            ->placeholder(__('Not specified')),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label(__('Active'))
                            ->boolean(),
                      
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Location Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('lat')
                            ->label(__('Latitude'))
                            ->numeric(decimalPlaces: 6)
                            ->placeholder(__('Not provided')),
                        Infolists\Components\TextEntry::make('long')
                            ->label(__('Longitude'))
                            ->numeric(decimalPlaces: 6)
                            ->placeholder(__('Not provided')),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Account Information'))
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', function ($query) {
                $query->
                where('guard_name', 'sanctum')
                 ->where('name', 'client');
            })
            ->with(['city'])
            ->orderBy('created_at', 'desc');
    }
}
