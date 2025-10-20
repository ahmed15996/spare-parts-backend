<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Chat\Models\Message;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Illuminate\Support\Facades\Log;

class PostStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post,public array $data)
    {
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(object $notifiable): FcmMessage
    {

        try {
            $notifiableTokens = method_exists($notifiable, 'getFCMTokens') ? $notifiable->getFCMTokens() : null;
        } catch (\Throwable $e) {
            $notifiableTokens = null;
        }

       $title = (string)($this->data['title']['ar'] ?? '');
       $body = (string)($this->data['body']['ar'] ?? '');
        
        $message =    FcmMessage::create()
            ->notification(
                FcmNotification::create()
                    ->title($title)
                    ->body($body)
            )
            ->data([
                'type' => (string)($this->data['metadata']['type'] ?? ''),
                'route' => 'posts.show',
                'model_id' => (string)($this->data['metadata']['post_id'] ?? ''),
            ]);
            return $message;
    }
}


