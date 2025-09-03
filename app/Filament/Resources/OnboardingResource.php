<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OnboardingResource\Pages;
use App\Models\Onboarding;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OnboardingResource extends Resource
{
    protected static ?string $model = Onboarding::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    public static function getModelLabel(): string
    {
        return __('Onboarding');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Onboardings');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Onboarding Information'))
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('Title'))
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('Enter onboarding title')),
                    
                    Forms\Components\Textarea::make('description')
                        ->label(__('Description'))
                        ->required()
                        ->rows(4)
                        ->placeholder(__('Enter onboarding description')),
                    
                    Forms\Components\TextInput::make('order')
                        ->label(__('Order'))
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->placeholder(__('Enter display order')),
                ])
                ->columns(2),

            Forms\Components\Section::make(__('Media'))
                ->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('onboarding')
                        ->label(__('Onboarding Image'))
                        ->collection('onboarding')
                        ->image()
                        ->imageEditor()
                        ->imageCropAspectRatio('16:9')
                        ->imageResizeTargetWidth('800')
                        ->imageResizeTargetHeight('450')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->helperText(__('Upload an image for this onboarding step. Recommended size: 800x450px'))
                        ->placeholder(__('Click to upload image')),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\SpatieMediaLibraryImageColumn::make('onboarding')
                    ->label(__('Image'))
                    ->collection('onboarding')
                    ->circular()
                    ->size(50),
                
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(100)
                    ->html(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOnboardings::route('/'),
            'create' => Pages\CreateOnboarding::route('/create'),
            'edit' => Pages\EditOnboarding::route('/{record}/edit'),
        ];
    }
}
