<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\API\V1\BannerResource;
use App\Http\Resources\API\V1\ProviderResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\SearchRequest;
use App\Http\Resources\API\V1\BrandResource;
use App\Http\Resources\API\V1\CategoryResource;
use App\Http\Resources\API\V1\ProductResource;
use App\Models\Product;
use App\Models\Provider;
use App\Services\CategoryService;
use App\Services\BannerService;
use App\Services\ProviderService;
use App\Services\ProviderSearchService;
use App\Services\ProductService;
use App\Services\CarService;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct(protected CategoryService $categoryService,
    protected BannerService $bannerService,
    protected ProviderService $providerService,
    protected ProviderSearchService $providerSearchService,
    protected ProductService $productService)
    {
    }
    public function home(Request $request){
        $user = Auth::user();
        $categories = $this->categoryService->getWithScopes();
        $banners = $this->bannerService->getWithScopes(['home','active']);
        $providers = [];
        if($user && $user->lat && $user->long){
            $providers = $this->providerService->getNearestProviders($user->lat, $user->long,$request->limit);
        }
        else{
            $providers = $this->providerService->getActiveProviders();
        }

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

    public function providerShow(Request $request,$id){
        $provider = $this->providerService->findWithRelations($id, ['days','city','category','products','brands','activeProfileBanners']);
        return $this->successResponse([
            ProviderResource::make($provider),
        ]);
    }

    public function productShow(Request $request,$id,$product_id){
        $product = $this->productService->findWithRelations($product_id, ['provider']);
        if(!$product){
            return $this->errorResponse('Product not found',404);
        }
        return $this->successResponse([
            ProductResource::make($product),
        ],__('Product fetched successfully'));
    }

    public function bannserDetails(Request $request,$id){
        $banner = $this->bannerService->findWithRelations($id, ['provider','media']);
        if(!$banner){
            return $this->errorResponse('Banner not found',404);
        }
        return $this->successResponse([
            BannerResource::make($banner),
        ],__('Banner fetched successfully'));
    }

    public function providerBrands(Request $request,$id){
        $provider = $this->providerService->findWithRelations($id, ['brands']);
        if(!$provider){
            return $this->errorResponse('Provider not found',404);
        }
        return $this->successResponse([
            BrandResource::collection($provider->brands),
        ],__('Brands fetched successfully'));
    }

    public function providerProducts(Request $request,$id){
        $provider = $this->providerService->findWithRelations($id, ['products']);
        if(!$provider){
            return $this->errorResponse('Provider not found',404);
        }
        return $this->successResponse([
            ProductResource::collection($provider->products),
        ],__('Products fetched successfully'));
    }


}
