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
        try {
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

            // Process files after message creation but before broadcasting
            if ($messageType === MessageType::File && isset($data['files'])) {
                try {
                    foreach ($data['files'] as $file) {
                        // Add timeout protection and error handling for file processing
                        $message->addMedia($file)
                            ->usingName($file->getClientOriginalName())
                            ->toMediaCollection('attachments');
                    }
                } catch (\Exception $e) {
                    Log::error('File upload failed for message: ' . $message->id, [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Don't fail the entire message creation, just log the error
                    // The message will be created without attachments
                }
            }

            $this->afterCreate($message);
            $message->load('sender');
            return $message;
        } catch (\Exception $e) {
            Log::error('Message creation failed', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }



    protected function afterCreate(Message $message): void
    {
        try {
            // Update conversation's last message
            if ($message->conversation) {
                $message->conversation->last_message_id = $message->id;
                $message->conversation->save();
            }
            
            // Broadcast event asynchronously to prevent blocking
            broadcast(new MessageSent($message))->toOthers();

            if($message->type == MessageType::Offer){
                $offerService = app(OfferService::class);
                $offerService->markAsAccepted($message->metadata['offer_id']);
            }
        } catch (\Exception $e) {
            Log::error('Error in afterCreate for message: ' . $message->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw the exception to prevent breaking the message creation
        }
    }
}