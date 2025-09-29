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
use App\Models\Review;
use App\Http\Requests\API\V1\Provider\SendOfferRequest;
use App\Http\Requests\API\V1\Provider\UpdateDaysRequest;
use App\Services\OfferService;
use App\Services\ProviderDaysService;
use App\Http\Resources\API\V1\Provider\Offers\ProviderOfferResource;
use App\Http\Resources\API\V1\Provider\ProviderDayResource;
use App\Services\ReviewService;
use App\Http\Resources\API\V1\ReviewResource;
use App\Models\ProviderHiddenRequest;

class ProviderController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected BannerService $bannerService,
        protected RequestService $requestService,
        protected ProviderService $providerService,
        protected OfferService $offerService,
        protected ProviderDaysService $providerDaysService,
        protected ReviewService $reviewService
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
            
            // Get the actual calculated rating from reviews
            $rating = $provider->getAverageRating();
            
            return $this->successResponse([
                'stock_count' => $stockCount,
                'rating' => $rating,
            ]);
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve provider statistics'), 500);
        }
    }
    public function getProviderReviews()
    {
        try {
            // Check if provider exists
            $provider = Auth::user()->provider;            
            
            $reviews = $this->reviewService->getProviderReviews($provider->id, ['user']);
            
            return $this->successResponse(
                [
                    'provider' =>[
                        'name' => $provider->store_name,
                        'avatar' => $provider->getFirstMediaUrl('logo'),
                        'rating' => $provider->getAverageRating(),
                    ],
                    'reviews' => ReviewResource::collection($reviews),
                ],
                __('Provider reviews retrieved successfully')
            );
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return  $this->errorResponse(__('Failed to retrieve provider reviews'), 500);
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
        return $this->successResponse(
            ProviderRequestResource::make($request),
         __('Request retrieved successfully'));
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
            return $this->successResponse(
                ProviderOfferResource::collection($offers),
            __('Offers retrieved successfully'));
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
            return $this->successResponse(
                ProviderOfferResource::make($offer),
             __('Offer retrieved successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve offer'), 500);
        }
    }    

    public function days(){
        try{
            $provider = Auth::user()->provider;
            $days = $this->providerDaysService->getProviderDays($provider);
            return $this->successResponse(
                ProviderDayResource::collection($days),
                __('Working days retrieved successfully')
            );
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to retrieve working days'), 500);
        }
    }

    public function updateDays(UpdateDaysRequest $request){
        try{
            $provider = Auth::user()->provider;
            $updated = $this->providerDaysService->updateProviderDays($provider, $request->validated()['days']);
            
            if ($updated) {
                $days = $this->providerDaysService->getProviderDays($provider);
                return $this->successResponse(
                    ProviderDayResource::collection($days),
                    __('Working days updated successfully')
                );
            }
            
            return $this->errorResponse(__('Failed to update working days'), 500);
        }
        catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to update working days'), 500);
        }
    }

    /**
     * Check if provider is currently open
     */
    public function checkAvailability()
    {
        try {
            $provider = Auth::user()->provider;
            $isOpen = $this->providerDaysService->isProviderOpen($provider);
            $nextOpening = $this->providerDaysService->getNextOpeningTime($provider);
            
            return $this->successResponse([
                'is_open' => $isOpen,
                'next_opening' => $nextOpening,
            ], __('Availability status retrieved successfully'));
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to check availability'), 500);
        }
    }

    /**
     * Hide/Unhide a request from the provider's suggested list
     */
    public function hideRequest($id)
    {
        try {
            $provider = Auth::user()->provider;
            $result = $this->requestService->toggleHideRequest($provider, $id);
            
            if ($result['success']) {
                return $this->successResponse([], $result['message']);
            }
            
            return $this->errorResponse($result['message'], $result['status_code']);
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to process request'), 500);
        }
    }
}