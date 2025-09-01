<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Models\Request;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Requests Management';

    public static function getNavigationLabel(): string
    {
        return __('Requests');
    }

    public static function getModelLabel(): string
    {
        return __('Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Requests');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label(__('User'))
                    ->relationship('user', 'first_name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('city_id')
                    ->label(__('City'))
                    ->relationship('city', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('number')
                    ->label(__('Request Number'))
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->label(__('Description'))
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        0 => __('Pending'),
                        1 => __('Approved'),
                        2 => __('Rejected'),
                    ])
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label(__('Request Number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.first_name')
                    ->label(__('User'))
                    ->formatStateUsing(fn ($record) => 
                        $record->user->first_name . ' ' . $record->user->last_name
                    )
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('City'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->formatStateUsing(fn ($state) => match($state) {
                        0 => __('Pending'),
                        1 => __('Approved'),
                        2 => __('Rejected'),
                        default => __('Unknown')
                    })
                    ->colors([
                        'warning' => 0,
                        'success' => 1,
                        'danger' => 2,
                    ]),

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
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        0 => __('Pending'),
                        1 => __('Approved'),
                        2 => __('Rejected'),
                    ]),

                Tables\Filters\SelectFilter::make('city_id')
                    ->label(__('City'))
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
             
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Request Information'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('number')
                                    ->label(__('Request Number')),

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
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('Created At'))
                                    ->dateTime(),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('Updated At'))
                                    ->dateTime(),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('User Information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.first_name')
                                    ->label(__('First Name')),

                                Infolists\Components\TextEntry::make('user.last_name')
                                    ->label(__('Last Name')),

                                Infolists\Components\TextEntry::make('user.email')
                                    ->label(__('Email')),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.phone')
                                    ->label(__('Phone')),

                                Infolists\Components\TextEntry::make('city.name')
                                    ->label(__('City')),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('Request Details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull()
                            ->html(),
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
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'view' => Pages\ViewRequest::route('/{record}'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): string{
        return __('Requests Management');
    }
}
