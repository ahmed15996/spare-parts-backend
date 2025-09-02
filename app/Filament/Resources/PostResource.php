<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Services\PostService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use App\Enums\PostStatus;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';

    public static function getNavigationGroup(): ?string
    {
        return __('Content');
    }

    public static function getModelLabel(): string
    {
        return __('Post');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Posts');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('content')->label(__('Content'))->rows(6)->required(),
            Forms\Components\Select::make('status')->label(__('Status'))
                ->options([
                    0 => __('Pending'),
                    1 => __('Accepted'),
                    2 => __('Rejected'),
                ])->required(),
            Forms\Components\Textarea::make('rejection_reason')->label(__('Rejection Reason'))->visible(fn ($get) => (int) $get('status') === 2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('first_post_media_url')->label(__('Image'))->circular()->size(48),
                Tables\Columns\TextColumn::make('author.name')->label(__('Author'))->searchable(),
                Tables\Columns\IconColumn::make('comments_count')->label(__('Comments'))->state(fn ($record) => $record->comments()->count())->icon('heroicon-o-chat-bubble-oval-left')->sortable(false),
                Tables\Columns\IconColumn::make('likes_count')->label(__('Likes'))->state(fn ($record) => $record->likes()->where('value', 1)->count())->icon('heroicon-o-hand-thumb-up')->sortable(false),
                Tables\Columns\BadgeColumn::make('status')->label(__('Status'))
                    ->colors([
                        'warning' => function ($state) { $value = is_int($state) ? $state : (int) $state; if ($value === 0) $value = PostStatus::Pending->value; return $value === PostStatus::Pending->value; },
                        'success' => function ($state) { $value = is_int($state) ? $state : (int) $state; return $value === PostStatus::Approved->value; },
                        'danger' => function ($state) { $value = is_int($state) ? $state : (int) $state; return $value === PostStatus::Rejected->value; },
                    ])
                    ->formatStateUsing(function ($state) { $value = is_int($state) ? $state : (int) $state; if ($value === 0) $value = PostStatus::Pending->value; return PostStatus::from($value)->label(); }),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created At'))->dateTime()->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        PostStatus::Pending->value => PostStatus::Pending->label(),
                        PostStatus::Approved->value => PostStatus::Approved->label(),
                        PostStatus::Rejected->value => PostStatus::Rejected->label(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = isset($data['value']) ? (int) $data['value'] : null;
                        if ($value === null) {
                            return;
                        }
                        if ($value === PostStatus::Pending->value) {
                            $query->whereIn('status', [0, PostStatus::Pending->value]);
                        } else {
                            $query->where('status', $value);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Post Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('id')->label(__('ID')),
                        Infolists\Components\TextEntry::make('author.name')->label(__('Author')),
                        Infolists\Components\TextEntry::make('author_phone')->label(__('Phone'))->visible(fn($record)=>!empty($record->author_phone)),
                        Infolists\Components\ImageEntry::make('author_avatar_url')->label(__('Author Avatar'))->visible(fn($record) => !empty($record->author_avatar_url)),
                        Infolists\Components\TextEntry::make('status')
                        ->label(__('Status'))->badge()
                        ->color(function ($state) { $value = is_int($state) ? $state : (int) $state; if ($value === 0) $value = PostStatus::Pending->value; return match ($value) { PostStatus::Pending->value => 'warning', PostStatus::Approved->value => 'success', PostStatus::Rejected->value => 'danger', default => 'secondary' }; })
                        ->formatStateUsing(function ($state) { $value = is_int($state) ? $state : (int) $state; if ($value === 0) $value = PostStatus::Pending->value; return PostStatus::from($value)->label(); }),
                        Infolists\Components\TextEntry::make('created_at')->label(__('Created At'))->dateTime('d/m/Y'),
                        Infolists\Components\TextEntry::make('accepted_at')->label(__('Accepted At'))->date('d/m/Y')->visible(fn($record)=>!empty($record->accepted_at)),
                        Infolists\Components\TextEntry::make('rejection_reason')->label(__('Rejection Reason'))->visible(function($record){
                            $value = is_int($record->status) ? $record->status : (int) $record->status;
                            if ($value === 0) { $value = PostStatus::Pending->value; }
                            return PostStatus::from($value) === PostStatus::Rejected;
                        }),
                    ])->columns(2),
                Infolists\Components\Section::make(__('Statistics'))
                    ->schema([
                        Infolists\Components\TextEntry::make('comments_stats')->label(__('Comments'))->getStateUsing(fn($record)=>$record->comments()->count()),
                        Infolists\Components\TextEntry::make('likes_stats')->label(__('Likes'))->getStateUsing(fn($record)=>$record->likes()->where('value',1)->count()),
                    ])->columns(2),
                Infolists\Components\Section::make(__('Content'))
                    ->schema([
                        Infolists\Components\TextEntry::make('content')->label(__('Content'))->columnSpanFull(),
                    ]),
                Infolists\Components\Section::make(__('Media'))
                    ->schema([
                        Infolists\Components\ImageEntry::make('media')->label(__('Images'))->getStateUsing(fn($record) => $record->getMedia('posts')->map->getUrl()->toArray())->columnSpanFull(),
                    ])->visible(fn($record)=>$record->getMedia('posts')->isNotEmpty()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'view' => Pages\ViewPost::route('/{record}'),
        ];
    }
}


