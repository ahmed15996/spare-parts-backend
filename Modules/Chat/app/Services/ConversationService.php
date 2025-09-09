<?php

namespace Modules\Chat\Services;

use App\Models\User;
use Modules\Chat\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Modules\Chat\Services\MessageService ;
use Modules\Chat\Events\MessageSent;
use Modules\Chat\Models\Message;

class ConversationService extends BaseService
{
    protected $conversation;
    protected $messageService;

    public function __construct(Conversation $conversation, MessageService $messageService)
    {
        $this->conversation = $conversation;
        $this->messageService = $messageService;
        parent::__construct($conversation);
    }

    /**
     * Get conversations for the sidebar with last message and user data
     */
    public function getConversations($user_id){
        $conversations = Conversation::whereHas('users', function($query) use ($user_id) {
            $query->where('users.id', $user_id);
        })
        ->where('last_message_id', '!=', null)
        ->with([
            'users',
            'lastMessage.sender'
        ])
        ->orderByDesc(
            Message::select('created_at')
                ->whereColumn('messages.id', 'conversations.last_message_id')
                ->take(1)
        )
        ->get();

        return $conversations;
    }

    /**
     * Start a new conversation or get existing one
     */
    public function startConversation($receiver_id)
    {
        $conversation = Conversation::getOrCreate(Auth::id(), $receiver_id);
        $this->afterCreate($conversation);
        return $conversation;
    }

    /**
     * Get messages for a specific conversation
     */
    public function getMessages($conversation_id , $limit = 10){
        $conversation = $this->conversation->find($conversation_id);
        $messages = $conversation->messages()->orderBy('messages.created_at', 'desc')->paginate($limit);
        return $messages;
    }

    /**
     * Get conversation with the other user for chat UI
     */
    public function getConversationWithOtherUser($conversation_id, $current_user_id)
    {
        $conversation = Conversation::with([
            'users' => function($query) use ($current_user_id) {
                $query->where('users.id', '!=', $current_user_id);
            },
            'lastMessage.sender'
        ])->find($conversation_id);

        return $conversation;
    }

    /**
     * Get conversation display data for sidebar
     */
    public function getConversationDisplayData($conversation, $current_user_id)
    {
        $otherUser = $conversation->getOtherUser($current_user_id);
        $lastMessage = $conversation->lastMessage;
        
        return [
            'id' => $conversation->id,
            'title' => $conversation->getDisplayTitle($current_user_id),
            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->avatar_url,
                'type' => $otherUser->type
            ],
            'last_message' => $lastMessage ? [
                'content' => $lastMessage->content,
                'sender_name' => $lastMessage->sender->name,
                'created_at' => $lastMessage->created_at,
                'is_from_current_user' => $lastMessage->sender_id == $current_user_id
            ] : null,
            'updated_at' => $conversation->updated_at,
            'unread_count' => $this->getUnreadCount($conversation->id, $current_user_id)
        ];
    }

    /**
     * Get unread message count for a conversation
     */
    public function getUnreadCount($conversation_id, $user_id)
    {
        return Message::where('conversation_id', $conversation_id)
            ->where('sender_id', '!=', $user_id)
            ->unread()
            ->count();
    }

    /**
     * Mark all messages in a conversation as read for a specific user
     */
    public function markConversationAsRead($conversation_id, $user_id)
    {
        Message::where('conversation_id', $conversation_id)
            ->where('sender_id', '!=', $user_id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    protected function afterCreate(Conversation $conversation): void
    {

    }

    public function getConversationByUsers($user1_id, $user2_id)
    {
        return Conversation::whereHas('users', function($query) use ($user1_id, $user2_id) {
            $query->where('users.id', $user1_id)->orWhere('users.id', $user2_id);
        })->first();
    }
}