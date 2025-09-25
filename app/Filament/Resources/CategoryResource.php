<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;
use Spatie\Image\Image;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class CategoryResource extends Resource
{
    use Translatable;
    protected static ?string $model = Category::class;
    
    protected static ?string $navigationIcon = 'fas-tags';

    public static function shouldRegisterNavigation(): bool
    {
        // Example: Only show this navigation item to admins
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')->translateLabel()
                    ->required(),
                SpatieMediaLibraryFileUpload::make('icon')
                ->label('Icon')->translateLabel()
                ->collection('icon')
                ->hint(__('The size of icons should be (40x40)px'))


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('icon')->label('Icon')
                ->translateLabel()
                ->collection('icon')
                ->conversion('thumb'),
                TextColumn::make('name')->translateLabel()->label('Name')->translateLabel(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getPluralLabel(): ?string
    {
        return __('Categories');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('Selection Lists');
    } 
}
