<?php

namespace App\Http\Controllers\API\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Client\FilterOffersRequest;
use App\Http\Requests\API\V1\Client\StoreRequest;
use App\Http\Resources\API\V1\Client\RequestResource;
use App\Http\Resources\API\V1\OfferResource;
use Illuminate\Http\Request;
use App\Services\RequestService;
use App\Services\OfferService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
class RequestController extends Controller
{
    public function __construct(protected RequestService $requestService, protected OfferService $offerService)
    {
    }
    public function index(Request $request){
        try{
            $requests = $this->requestService->getWithScopes()->where('user_id', Auth::id());
            return $this->successResponse(RequestResource::collection($requests), __('Requests retrieved successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve requests'), 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try{
            $request = $this->requestService->createWithBusinessLogic($request->validated());
            return $this->successResponse([], __('Request submitted successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to submit request'), 500);
        }
    }

    public function show($id)
    {
        try{
            $request = $this->requestService->findWithRelations($id, ['car','city','category','car.brandModel','car.brand']);            
            if (!$request) {
                return $this->errorResponse(__('Request not found'), 404);
            }
            
            return $this->successResponse(RequestResource::make($request), __('Request retrieved successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve request'), 500);
        }
    }

    public function showOffer($id, $offer_id)
    {
        try{
            $offer = $this->offerService->findWithRelations($offer_id, ['request']);
            return $this->successResponse(OfferResource::make($offer), __('Offer retrieved successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve offer'), 500);
        }
    }

    public function filterOffers(FilterOffersRequest $request,$id){
        try{
            $data = $request->validated();
            $data['request_id'] = $id;
            $offers = $this->offerService->getFilteredOffers($data);
            return $this->successResponse(OfferResource::collection($offers), __('Offers retrieved successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve offers'), 500);
        }
    }

    public function destroyOffer($id, $offer_id){
        try{
            $offer = $this->offerService->findWithRelations($offer_id, ['request']);
            $this->offerService->deleteWithBusinessLogic($offer);
            return $this->successResponse([], __('Offer deleted successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to delete offer'), 500);
        }
    }

}
