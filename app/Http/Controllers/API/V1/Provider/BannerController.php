<?php

namespace App\Http\Controllers\API\V1\Provider;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\Provider\BannerResource;
use App\Services\BannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\API\V1\Provider\StoreBanner;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    public function __construct(protected BannerService $bannerService)
    {
    }
    public function index()
    {
        $provider = Auth::user()->provider;
        $banners = $this->bannerService->getProviderBanners($provider);
        if(!$banners){
            return $this->successResponse([], __('no banners found'));
        }
        return $this->successResponse(BannerResource::collection($banners), __('Banners retrieved successfully'));
    }


    public function show($id){
        $banner = $this->bannerService->findWithRelations($id, ['provider','media']);
        if(!$banner){
            return $this->errorResponse(__('Banner not found'),404);
        }
        return $this->successResponse(BannerResource::make($banner), __('Banner retrieved successfully'));
    }
    public function store(StoreBanner $request)
    {
        $provider = Auth::user()->provider;
        $data = $request->validated();

        if(!$provider->hasActiveSubscription()){
            return $this->errorResponse(__('You don\'t have an active subscription'));
        }
        $type = $provider->subscriptions->where('is_active', true)->first()->package->banner_type;
        $data['type'] = $type;
        $data['number'] = rand(100000, 999999);
        $data['status'] = \App\Enums\BannerStatus::Pending->value;
        $data['provider_id'] = $provider->id;
         
        try{
            
        $banner = $this->bannerService->createWithBusinessLogic($data);
        return $this->successResponse([], __('Banner created successfully and awaiting approval'));
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return $this->errorResponse(__('Failed to create banner'),500);
        }

    }
}
