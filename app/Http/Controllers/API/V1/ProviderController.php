<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\API\V1\BannerResource;
use App\Http\Resources\API\V1\ProviderResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\CategoryResource;
use App\Services\CategoryService;
use App\Services\BannerService;
use App\Services\ProviderService;
use App\Services\RequestService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Package;
use App\Http\Resources\API\V1\PackageResource;
use App\Http\Resources\API\V1\SubscriptionResource;
use App\Http\Resources\API\V1\Provider\RequestResource as ProviderRequestResource;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Http\Requests\API\V1\Provider\SendOfferRequest;
use App\Services\OfferService;
use App\Http\Resources\API\V1\Provider\Offers\ProviderOfferResource;
class ProviderController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected BannerService $bannerService,
        protected RequestService $requestService,
        protected ProviderService $providerService,
        protected OfferService $offerService
    ) {
    }
    public function packages()
    {
        try {
            $provider = Auth::user()->provider;
            $currentSubscription = $this->providerService->getCurrentPackage($provider);
            $packages = Cache::rememberForever('packages', function () {
                return Package::all();
            });
            return $this->successResponse([
                'current_subscription' => $currentSubscription ? SubscriptionResource::make($currentSubscription) : null,
                'packages' => PackageResource::collection($packages),
            ], __('Packages retrieved successfully'));
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
           return $this->errorResponse(__('Failed to retrieve packages'), 500);
        }
    }

    public function statistics(){
        try{
            $provider = User::with('provider')->find(Auth::id())->provider;
            $stockCount = $this->providerService->stockCount($provider);
            return $this->successResponse([
                'stock_count' => $stockCount,
                'rating' =>'4'
            ]);
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve provider statistics'), 500);
        }
    }

    public function homeRequests(Request $request){
        try{
            $provider = Auth::user()->provider;
            $requests = $this->requestService->getProviderRequests($provider, $request);
            return $this->paginatedResourceResponse($requests, ProviderRequestResource::class
            , __('Provider requests retrieved successfully'));
        }
        catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve provider requests'), 500);
        }
    }

    public function request($id){
       try{
        $request = $this->requestService->findWithRelations($id, ['user','car','city','category','car.brandModel','car.brand']);
       
        if(!$request){
            return $this->errorResponse(__('Request not found'), 404);
        }
        return $this->successResponse([
            ProviderRequestResource::make($request),
        ], __('Request retrieved successfully'));
       }catch(\Exception $e){
        Log::debug($e->getMessage());
        return $this->errorResponse(__('Failed to retrieve request'), 500);
       }
    }

    public function sendOffer($id, SendOfferRequest $httprequest){
        try{
            $request = $this->requestService->findWithRelations($id, ['user','car','city','category','car.brandModel','car.brand']);
            if(!$request){
                return $this->errorResponse(__('Request not found'), 404);
            }

            if($request->offers->where('provider_id', Auth::user()->provider->id)->first()){
                return $this->errorResponse(__('You have already sent an offer for this request'), 400);
            }
            $validated = $httprequest->validated();
            $validated['request_id'] = $request->id;
            $offer = $this->offerService->createWithBusinessLogic($validated);
            return $this->successResponse([], __('Offer sent successfully'));
        }
        catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to send offer'), 500);
        }
    }


    public function myOffers(){
        try{
            $provider = Auth::user()->provider;
            $offers = $this->offerService->getProviderOffers($provider);
            if($offers->isEmpty()){
                return $this->successResponse([], __('No offers found'));
            }
            return $this->successResponse([
                ProviderOfferResource::collection($offers),
            ], __('Offers retrieved successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve offers'), 500);
        }

    }

    public function offerShow($id){
        try{
            $offer = $this->offerService->findWithRelations($id, ['request','request.user','request.car','request.category','request.city']);
            if(!$offer){
                return $this->errorResponse(__('Offer not found'), 404);
            }
            return $this->successResponse([
                ProviderOfferResource::make($offer),
            ], __('Offer retrieved successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve offer'), 500);
        }
    }


    
}