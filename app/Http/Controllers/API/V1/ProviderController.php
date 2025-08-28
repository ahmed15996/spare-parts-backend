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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Package;
use App\Http\Resources\API\V1\PackageResource;
use App\Http\Resources\API\V1\SubscriptionResource;
use Illuminate\Support\Facades\Log;

class ProviderController extends Controller
{
    public function __construct(protected CategoryService $categoryService,protected BannerService $bannerService,protected ProviderService $providerService)
    {
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

    
}
