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

class ClientController extends Controller
{
    public function __construct(protected CategoryService $categoryService,protected BannerService $bannerService,protected ProviderService $providerService)
    {
    }
    public function home(Request $request){
        $user = Auth::user();
        $categories = $this->categoryService->getWithScopes();
        $banners = $this->bannerService->getWithScopes(['home']);
        $providers = $this->providerService->getNearestProviders($user->lat, $user->long,$request->limit);

        return $this->successResponse([
            'banners' => BannerResource::collection($banners),
            'categories' => CategoryResource::collection($categories),
            'providers' => ProviderResource::collection($providers),
        ]);

    }

    
}
