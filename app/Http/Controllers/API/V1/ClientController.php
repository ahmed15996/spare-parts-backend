<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\API\V1\BannerResource;
use App\Http\Resources\API\V1\ProviderResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\SearchRequest;
use App\Http\Resources\API\V1\CategoryResource;
use App\Services\CategoryService;
use App\Services\BannerService;
use App\Services\ProviderService;
use App\Services\ProviderSearchService;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct(protected CategoryService $categoryService,protected BannerService $bannerService,protected ProviderService $providerService,protected ProviderSearchService $providerSearchService)
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

    public function search ( SearchRequest $request){
        $data = $request->validated();
        $providers = $this->providerSearchService->searchProvidersWithLocation($data);
        return $this->successResponse([
             ProviderResource::collection($providers),
        ]);
    }


    
}
