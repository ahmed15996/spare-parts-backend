<?php

namespace Modules\Chat\Transformers;

use App\Services\OfferService;
use App\Http\Resources\API\V1\Client\RequestResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Chat\Enums\MessageType;
use Modules\Chat\Models\Message;

class MessageResource extends JsonResource
{
    protected $offerService;
    
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->offerService = app(OfferService::class);
    }
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // $this->type is a MessageType enum instance due to enum cast
        $type = $this->type instanceof MessageType ? $this->type : MessageType::from($this->type);

        $data =  [
            'id' => $this->id,
            'type' => $type->value,
            'content' => $this->content,
            'sender' => new MessageSenderResource($this->sender),
            'reciver'=> new MessageSenderResource($this->conversation->getOtherUser($this->sender_id)),
            'created_at' => Carbon::parse($this->created_at)->format('H:i'),
        ];

        if($type == MessageType::Offer && isset($this->metadata['offer_id'])){
            $offer = $this->offerService->findWithRelations($this->metadata['offer_id']);
            if($offer && $offer->request) {
                $data['request'] =  [
                    'id' => $offer->request->id,
                    'number' => $offer->request->number,
                    'description' => $offer->request->description,
                ];
            }
        }
        if($type == MessageType::File){
            $data['attachments'] = $this->getMedia('attachments')->map(function($media){
                return [
                    'id' => $media->id,
                    'name' => $media->name,
                    'mime_type' => $media->mime_type,
                    'url' => $media->getUrl(),
                ];
            });
        }

        if($request &&$request->route() && $request->route()->getName() == 'api.conversations.index'){
            unset($data['sender']);
            unset($data['reciver']);
        }




        return $data;
    }
}
