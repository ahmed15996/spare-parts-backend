<?php

namespace Modules\Chat\Services;

use Modules\Chat\Models\Message;
use Illuminate\Database\Eloquent\Collection;
use App\Services\BaseService;
use Modules\Chat\Events\MessageSent;
use Modules\Chat\Enums\MessageType;
use App\Services\OfferService;
// use App\Notifications\ChatMessageNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class MessageService extends BaseService
{
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        parent::__construct($message);
    }


    public function createWithBusinessLogic(array $data)
    {
        Log::debug('here');
        // Normalize to enum instance
        $messageType =  MessageType::from($data['type'] ?? MessageType::Text->value);

        if ($messageType === MessageType::Offer) {
            $data['content'] = $data['content'] ?? '';
            $data['metadata'] = [
                'offer_id' => $data['offer_id'] ?? null,
            ];
        } else {
            // For text and file, default to null metadata unless explicitly passed
            $data['metadata'] = $data['metadata'] ?? null;
        }

        $message = $this->message->create([
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $data['sender_id'],
            'content' => $data['content'] ?? '',
            'type' => $messageType, // enum-aware cast accepts enum instance
            'metadata' => $data['metadata'] ?? null,
        ]);

        if ($messageType === MessageType::File && request()->hasFile('file')) {
            $message->addMediaFromRequest('file')->toMediaCollection('attachments');
        }

        $this->afterCreate($message);
        $message->load('sender');
        return $message;
    }



    protected function afterCreate(Message $message): void
    {
        // Update conversation's last message
        if ($message->conversation) {
            $message->conversation->last_message_id = $message->id;
            $message->conversation->save();
        }
        // Broadcast event
        broadcast(new MessageSent($message));

        if($message->type == MessageType::Offer){
            $offerService = app(OfferService::class);
            $offerService->markAsAccepted($message->metadata['offer_id']);
        }
    }
}