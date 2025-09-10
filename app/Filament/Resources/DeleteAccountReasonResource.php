<?php

namespace App\Filament\Resources;

use App\Enums\DeleteAccountReasonType;
use App\Filament\Resources\DeleteAccountReasonResource\Pages;
use App\Models\DeleteAccountReason;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeleteAccountReasonResource extends Resource
{
    protected static ?string $model = DeleteAccountReason::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function getNavigationLabel(): string
    {
        return __('Delete Account Reasons');
    }

    public static function getModelLabel(): string
    {
        return __('Delete Account Reason');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Delete Account Reasons');
    }

    public static function getNavigationGroup(): ?string
    {
        return __("Informative Content");
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reason')
                    ->label(__('Reason'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        DeleteAccountReasonType::Client->value => DeleteAccountReasonType::Client->label(),
                        DeleteAccountReasonType::Provider->value => DeleteAccountReasonType::Provider->label(),
                    ])
                    ->required()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label(__('Reason'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->formatStateUsing(fn (DeleteAccountReasonType $state): string => $state->label())
                    ->badge()
                    ->color(fn (DeleteAccountReasonType $state): string => match ($state) {
                        DeleteAccountReasonType::Client => 'info',
                        DeleteAccountReasonType::Provider => 'warning',
                    })
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
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        DeleteAccountReasonType::Client->value => DeleteAccountReasonType::Client->label(),
                        DeleteAccountReasonType::Provider->value => DeleteAccountReasonType::Provider->label(),
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeleteAccountReasons::route('/'),
        ];
    }
}
