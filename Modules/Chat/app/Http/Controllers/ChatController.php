<?php

namespace Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Modules\Chat\Services\ConversationService;
use Modules\Chat\Services\MessageService;
use Modules\Chat\Http\Requests\StartConversationRequest;
use Modules\Chat\Transformers\ConversationResource;
use Modules\Chat\Transformers\MessageResource;
use Modules\Chat\Http\Requests\SendMessageRequest;

class ChatController extends Controller
{
    public function __construct(private ConversationService $conversationService, private MessageService $messageService)
    {
    }

    public function getConversations()
    {
        try{
            $conversations =  $this->conversationService->getConversations(Auth::id());
            return $this->ok(
                ConversationResource::collection($conversations),
                __('Conversations fetched successfully')
            );
        }catch(\Exception $e){
            Log::debug($e->getMessage(),$e->getTrace());
            return $this->errorResponse(
                __('Failed to fetch conversations'),
                statusCode: 500
            );
        }
    }

    public function startConversation(StartConversationRequest $request){
        try{
           $conversation = $this->conversationService->startConversation($request->validated());
            return $this->ok(
                ConversationResource::make($conversation),
                __('Conversation started successfully')
            );
        }catch(\Exception $e){
            Log::debug($e->getMessage(),$e->getTrace());

            return $this->errorResponse(
                __('Failed to start conversation'),
                500
            );
        }
    }

    public function getConversationMessages($id){
        try{
            $limit = request()->get('per_page', 10);

            $messages = $this->conversationService->getMessages($id, $limit);
            $this->conversationService->markConversationAsRead($id, Auth::id());

            return $this->paginatedResourceResponse(
                $messages,
                MessageResource::class,
                __('Messages fetched successfully')
            );
        }
        catch(InvalidArgumentException $e){
            return $this->errorResponse(
                __('Invalid conversation id'),
                400
            );
        }
        
        catch(\Exception $e){
            Log::debug($e->getMessage(),$e->getTrace());

            return $this->errorResponse(
                __('Failed to fetch messages'),
                500
            );
        }
    }

    public function sendMessage(SendMessageRequest $request){
        try{
            $data = $request->validated();
            $data['sender_id'] = Auth::id();
            $receiver_id = $data['receiver_id'] ?? null;
            // if conversation_id exist ust it directly 
            if(!isset($data['conversation_id'])){
                $conversation = $this->conversationService->startConversation($receiver_id);
                $data['conversation_id'] = $conversation->id;
                unset($data['receiver_id']);
            }
            if(!isset($data['receiver_id'])){
                unset($data['receiver_id']);
            }
            $this->messageService->createWithBusinessLogic($data);
            return $this->ok(
                [],
                __('Message sent successfully')
            );
        }catch(\Exception $e){
            Log::debug($e->getMessage(),$e->getTrace());

            return $this->errorResponse(
                __('Failed to send message'),
                500
            );
        }
    }
}
