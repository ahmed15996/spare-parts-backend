<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\CustomNotification;
use App\Services\NotificationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class SendNotification extends Page
{
    protected static ?int $navigationSort = 1003;
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static string $view = 'filament.pages.send-notification';
    protected static ?string $title = 'Send Notifications';
    protected static ?string $navigationLabel = 'Send Notification';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }
    public static function getNavigationLabel(): string
    {
        return __('Send Notifications');
    }
    public function getTitle(): string
    {
        return __('Send Notifications');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Notification Content'))
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label(__('Title (Arabic)'))
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('title_en')
                            ->label(__('Title (English)'))
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('body_ar')
                            ->label(__('Body (Arabic)'))
                            ->required()
                            ->rows(4)
                            ->maxLength(1000),
                        
                        Forms\Components\Textarea::make('body_en')
                            ->label(__('Body (English)'))
                            ->required()
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Target Users'))
                    ->schema([
                        Forms\Components\Select::make('target_type')
                            ->label(__('Send to'))
                            ->options([
                                'clients' => __('Clients Only'),
                                'providers' => __('Providers Only'),
                                'both' => __('Both Clients and Providers'),
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('selected_users', [])),

                        Forms\Components\Select::make('selected_users')
                            ->label(__('Specific Users (Optional)'))
                            ->multiple()
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search, callable $get) {
                                $targetType = $get('target_type');
                                
                                if (!$targetType) {
                                    return [];
                                }

                                $query = User::query();

                                if ($targetType === 'clients') {
                                    $query->whereHas('roles', function ($q) {
                                        $q->where('name', 'client');
                                    });
                                } elseif ($targetType === 'providers') {
                                    $query->whereHas('roles', function ($q) {
                                        $q->where('name', 'provider');
                                    });
                                }

                                if ($search) {
                                    $query->where(function ($q) use ($search, $targetType) {
                                        $q->where('first_name', 'like', "%{$search}%")
                                          ->orWhere('last_name', 'like', "%{$search}%")
                                          ->orWhere('email', 'like', "%{$search}%");
                                        
                                        // If searching for providers, also search in provider store names
                                        if ($targetType === 'providers') {
                                            $q->orWhereHas('provider', function ($providerQuery) use ($search) {
                                                $providerQuery->where('store_name', 'like', "%{$search}%")
                                                           ->orWhereJsonContains('store_name', $search);
                                            });
                                        }
                                    });
                                }

                                return $query->with('provider')->limit(50)->get()->mapWithKeys(function ($user) {
                                    $displayName = $user->name;
                                    if ($user->hasRole('provider') && $user->provider) {
                                        $storeName = $user->provider->getTranslation('store_name', app()->getLocale()) 
                                                   ?: $user->provider->getTranslation('store_name', 'en') 
                                                   ?: 'Store Name';
                                        $displayName = $storeName . ' (' . $user->email . ')';
                                    } else {
                                        $displayName = $user->name . ' (' . $user->email . ')';
                                    }
                                    return [$user->id => $displayName];
                                });
                            })
                            ->getOptionLabelsUsing(function (array $values) {
                                return User::with('provider')->whereIn('id', $values)->get()->mapWithKeys(function ($user) {
                                    $displayName = $user->name;
                                    if ($user->hasRole('provider') && $user->provider) {
                                        $storeName = $user->provider->getTranslation('store_name', app()->getLocale()) 
                                                   ?: $user->provider->getTranslation('store_name', 'en') 
                                                   ?: 'Store Name';
                                        $displayName = $storeName . ' (' . $user->email . ')';
                                    } else {
                                        $displayName = $user->name . ' (' . $user->email . ')';
                                    }
                                    return [$user->id => $displayName];
                                });
                            })
                            ->helperText(__('Leave empty to send to all users of the selected type')),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getActions(): array
    {
        return [
            Action::make('send')
                ->label(__('Send Notification'))
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->action('sendNotification')
                ->requiresConfirmation()
                ->modalHeading(__('Send Notification'))
                ->modalDescription(__('Are you sure you want to send this notification? This action cannot be undone.'))
                ->modalSubmitActionLabel(__('Yes, Send Notification')),
        ];
    }

    public function sendNotification(): void
    {
        $data = $this->form->getState();
        
        try {
            DB::beginTransaction();

            $targetType = $data['target_type'];
            $selectedUsers = $data['selected_users'] ?? [];

            // Get target users
            $users = $this->getTargetUsers($targetType, $selectedUsers);

            if ($users->isEmpty()) {
                Notification::make()
                    ->title(__('No users found'))
                    ->body(__('No users match the selected criteria.'))
                    ->warning()
                    ->send();
                return;
            }

            // Create notification data
            $notificationData = [
                'title' => [
                    'ar' => $data['title_ar'],
                    'en' => $data['title_en'],
                ],
                'body' => [
                    'ar' => $data['body_ar'],
                    'en' => $data['body_en'],
                ],
                'metadata' => [
                    'sent_by' => 'adminstration',
                    'type' => 'admin'
                ],
                'is_read' => false,
            ];

            // Create notifications for each user
            $notifications = [];
            foreach ($users as $user) {
                $notifications[] = [
                    'title' => json_encode($notificationData['title']),
                    'body' => json_encode($notificationData['body']),
                    'metadata' => json_encode($notificationData['metadata']),
                    'is_read' => false,
                    'notifiable_id' => $user->id,
                    'notifiable_type' => User::class,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert notifications
            CustomNotification::insert($notifications);

            DB::commit();

            Notification::make()
                ->title(__('Notification sent successfully'))
                ->body(__('Notification has been sent to') . " {$users->count()} " . __('user(s)') . ".")
                ->success()
                ->send();

            // Reset form
            $this->form->fill();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title(__('Error sending notification'))
                ->body(__('An error occurred while sending the notification. Please try again.'))
                ->danger()
                ->send();
        }
    }

    private function getTargetUsers(string $targetType, array $selectedUsers = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::query();

        // If specific users are selected, use them
        if (!empty($selectedUsers)) {
            return $query->whereIn('id', $selectedUsers)->get();
        }

        // Otherwise, get all users of the specified type
        if ($targetType === 'clients') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'client');
            });
        } elseif ($targetType === 'providers') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'provider');
            });
        } elseif ($targetType === 'both') {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['client', 'provider']);
            });
        }

        return $query->get();
    }
}
