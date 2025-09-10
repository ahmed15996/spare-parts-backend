<?php

namespace App\Notifications;

use App\Models\Offer;
use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Chat\Models\Message;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Illuminate\Support\Facades\Log;

class NewProviderOffer extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Offer $offer)
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

        $title = 'عرض جديد من المزود ' . $this->offer->provider->store_name;
        $body = "عرض جديد يمكنك الإطلاع عليه من هنا ";

        // Ensure all data values are strings
        $offerId = $this->offer->id ? strval($this->offer->id) : '0';

        $message = FcmMessage::create()
            ->notification(
                FcmNotification::create()
                    ->title($title)
                    ->body($body)
            )
            ->data([
                'type' => 'new_provider_offer',
                'route' => 'client.requests.offers.show',
                'offer_id' => $offerId,
            ]);

        return $message;
    }
}