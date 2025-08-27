<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Dotswan\FilamentGrapesjs\Fields\GrapesJs;
use Filament\Actions\LocaleSwitcher;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Support\Str;
use Filament\Resources\Concerns\Translatable;
use Illuminate\Support\Facades\Log;

class PageResource extends Resource
{
    use Translatable;
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'fas-file-alt';

    /**
     * Get custom CSS content for GrapesJS canvas
     */
    protected static function getCustomCanvasCss(): string
    {
        $cssPath = resource_path('css/custom.css');
        
        if (file_exists($cssPath)) {
            return file_get_contents($cssPath);
        }
        return '';
    }
    public static function form(Form $form): Form
    {
        $customCss = static::getCustomCanvasCss();
        // make mount 

        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->label('Title')->translateLabel()
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (string $operation, $state, $set, $get, $livewire) {
                  
                    $activeLocale = $livewire->activeLocale ?? 'en';
                    
                    if ($activeLocale === 'en' && filled($state)) {
                        $slug = Str::slug($state);
                        $set('slug', $slug);
                    }
                }),
                    
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')->translateLabel()
                    ->required()
                    ->maxLength(255)
                    ->readOnly()
                    ->unique(ignoreRecord: true) // ignore on edit 
                    ->helperText(__('Auto Generated from English Title')),
                    
                    // prevent submit on enter
                Section::make('Layouts')
                ->label('Layouts')->translateLabel()
                ->schema([
                   Tabs::make('Layouts')
                   ->label('Layouts')->translateLabel()
                   ->tabs([
                    Tabs\Tab::make('English')
                    ->label('English')->translateLabel()
                    ->schema([
                        GrapesJs::make('page_layout_en')
                        ->label('English Layout')->translateLabel()
                        ->tools([

                        ])
                        ->plugins([
                            'grapesjs-tailwind',
                        ])
                        ->settings([
                            'canvasCss' => $customCss,
                        ])
                        ->id('page_layout_en')
                    ->columnSpanFull(),                  
                    ]),
                    Tabs\Tab::make('Arabic')
                    ->label('Arabic')->translateLabel()
                    ->schema([
                        GrapesJs::make('page_layout_ar')
                        ->label('Arabic Layout')->translateLabel()
                        ->tools([

                        ])
                        ->plugins([
                            'grapesjs-tailwind',
                        ])
                        ->settings([
                            'canvasCss' => $customCss,
                            
                        ])
                        ->id('page_layout_ar')
                    ->columnSpanFull(),
                    ])
                   ])
                ])
                  

            ])
            ->extraAttributes([
                
                'x-on:keydown.enter.prevent' => 'if($event.target.tagName !== "TEXTAREA") { $event.preventDefault(); }',
            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Page Title')),

                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug')),

                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('Created At')),

                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('Updated At')),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                // Tables\Actions\ViewAction::make()->url(fn(Page $record) => route('api.pages.visit', ['slug' => $record->slug])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getPluralLabel(): ?string
    {
        return __('Pages');
    }

}
