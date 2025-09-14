<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $modelLabel = 'Product';
    protected static ?string $pluralModelLabel = 'Products';
    
    public static function getModelLabel(): string
    {
        return __('Product');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('Products');
    }
    
    public static function getNavigationLabel(): string
    {
        return __('Products');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('provider_id')
                    ->default(fn () => Auth::user()?->provider?->id)
                    ->dehydrated(true),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(__('Name'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->label(__('Description'))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->label(__('Price'))
                    ->prefix('$'),
                Forms\Components\TextInput::make('discount_percentage')
                    ->numeric()
                    ->label(__('Discount Percentage')),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->label(__('Stock')),
                Forms\Components\Toggle::make('published')
                    ->required()
                    ->label(__('Published')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
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
                Tables\Columns\TextColumn::make('provider.store_name')
                    ->label(__('Store Name'))
                    ->searchable()
                    ->sortable()
                    ->label(__('Store Name')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('Name')),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('SAR')
                    ->sortable()
                    ->label(__('Price')),
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->numeric()
                    ->label(__('Discount Percentage'))
                    ->sortable()
                    ->suffix('%')
                    ->label(__('Discount Percentage')),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->label(__('Stock'))
                        ->sortable()
                    ->label(__('Stock')),
                Tables\Columns\IconColumn::make('published')
                    ->label(__('Published'))
                    ->boolean()
                    ->label(__('Published')),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();
        if ($user && $user->hasRole('provider') && $user->provider) {
            $query->where('provider_id', $user->provider->id);
        }
        return $query;
    }
}
