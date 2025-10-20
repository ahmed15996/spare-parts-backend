<?php

namespace App\Notifications;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Chat\Models\Message;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Illuminate\Support\Facades\Log;

class NewClientRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Request $request,public array $data)
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
                'route' => 'provider.requests.show',
                'model_id' => (string)($this->data['metadata']['model_id'] ?? ''),
            ]);
            return $message;
    }
}


