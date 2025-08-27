<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderRegistrationRequestResource\Pages;
use App\Filament\Resources\ProviderRegistrationRequestResource\RelationManagers;
use App\Models\ProviderRegistrationRequest;
use App\Models\City;
use App\Models\Category;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProviderRegistrationRequestResource extends Resource
{
    protected static ?string $model = ProviderRegistrationRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $modelLabel = 'Provider Registration Request';
    protected static ?string $pluralModelLabel = 'Provider Registration Requests';
    
    public static function getModelLabel(): string
    {
        return __('Provider Registration Request');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('Provider Registration Requests');
    }
    
    public static function getNavigationLabel(): string
    {
        return __('Provider Requests');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('User Information'))
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label(__('First Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label(__('Last Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
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
                        Forms\Components\Select::make('city_id')
                            ->label(__('City'))
                            ->options(City::pluck('name', 'id')->toArray())
                            ->searchable()
                            ->nullable()
                            ->formatStateUsing(function ($state) {
                                return $state ?? null;
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return $state == null ? null : $state;
                            }),
                        Forms\Components\Select::make('category_id')
                            ->label(__('Category'))
                            ->options(Category::pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('brands')
                            ->label(__('Brands'))
                            ->multiple()
                            ->options(Brand::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record || !$state) return [];
                                return is_string($state) ? json_decode($state, true) : $state;
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return is_array($state) ? array_map('intval', $state) : $state;
                            }),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('commercial_number')
                            ->label(__('Commercial Number'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label(__('Address'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make(__('Location'))
                    ->schema([
                        Forms\Components\TextInput::make('lat')
                            ->label(__('Latitude'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('long')
                            ->label(__('Longitude'))
                            ->required()
                            ->numeric(),
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
                Tables\Columns\TextColumn::make('first_name')
                    ->label(__('First Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label(__('Last Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('store_name.ar')
                    ->label(__('Store Name (AR)'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('City'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->city_id ? $record->city->name : __('All Cities');
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->colors([
                        'warning' => 0,
                        'success' => 1,
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '0' => __('Pending'),
                        '1' => __('Approved'),
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        0 => __('Pending'),
                        1 => __('Approved'),
                    ]),
                Tables\Filters\SelectFilter::make('city_id')
                    ->label(__('City'))
                    ->relationship('city', 'name'),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('Category'))
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProviderRegistrationRequests::route('/'),
            'create' => Pages\CreateProviderRegistrationRequest::route('/create'),
            'view' => Pages\ViewProviderRegistrationRequest::route('/{record}'),
            'edit' => Pages\EditProviderRegistrationRequest::route('/{record}/edit'),
        ];
    }
}
