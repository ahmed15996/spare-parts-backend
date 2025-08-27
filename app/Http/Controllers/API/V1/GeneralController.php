<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\BrandModelResource;
use App\Http\Resources\API\V1\CategoryResource;
use App\Http\Resources\API\V1\CityResource;
use App\Http\Resources\API\V1\BrandResource;
use App\Http\Resources\API\V1\BannerTypeResource;
    use App\Http\Resources\API\V1\SettingResource;
use App\Http\Resources\API\V1\DayResource;
use App\Http\Resources\API\V1\PackageResource;
use App\Models\Brand;
use App\Models\City;
use App\Models\Day;
use App\Models\Package;
use App\Services\CategoryService;
use App\Services\CityService;
use App\Services\BrandService;
use App\Enums\BannerType;
use function App\Helpers\settings;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
class GeneralController extends Controller
{
    public function __construct (private CategoryService $categoryService,
    private CityService $cityService,
    private BrandService $brandService,
     ){}


    public function categories()
    {
        try {
            $categories = $this->categoryService->getWithScopes();
            return $this->collectionResponse(CategoryResource::collection($categories), __('Categories retrieved successfully'));
        } catch (\Exception $e) {
            return $this->handleException($e, __('Failed to retrieve categories'));
        }
    }
    public function brands()
    {
        try {
            $brands = $this->brandService->getWithScopes();
            return $this->collectionResponse(BrandResource::collection($brands), __('Brands retrieved successfully'));
        } catch (\Exception $e) {
            return $this->handleException($e, __('Failed to retrieve brands'));
        }
    }

    public function cities()
    {
        try {
            $cities = $this->cityService->getWithRelations();
            // add city with  0 id and name الكل , all 
            return $this->collectionResponse(CityResource::collection($cities), __('Cities retrieved successfully'));
        }
        catch (\Exception $e) {
            return $this->handleException($e, __('Failed to retrieve cities'));
        }
    }

    public function bannerTypes()
    {
        return $this->successResponse(BannerTypeResource::collection(BannerType::cases()), __('Banner types retrieved successfully'));
    }
    public function brandModels($brand)
    {
        try {
            $brand = $this->brandService->findWithRelations($brand, ['models']);
            if(!$brand){
                return $this->errorResponse(__('Brand not found'),404);
            }
            return $this->collectionResponse(BrandModelResource::collection($brand->models), __('Models retrieved successfully'));
        } catch (\Exception $e) {
            return $this->handleException($e, __('Failed to retrieve models'));
        }
    }
    public function settings()
    {
        try {
            $settings = settings();
            return $this->successResponse(SettingResource::collection($settings), __('Settings retrieved successfully'));
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->errorResponse( __('Failed to retrieve settings'),500);
        }
    }
    public function days()
    {
        try {
            $days = Cache::rememberForever('days', function () {
                return Day::all();
            });
            return $this->collectionResponse(DayResource::collection($days), __('Days retrieved successfully'));
        } catch (\Exception $e) {
            return $this->handleException($e, __('Failed to retrieve days'));
        }
    }
    public function packages()
    {
        try {
            $packages = Cache::rememberForever('packages', function () {
                return Package::all();
            });
            return $this->collectionResponse(PackageResource::collection($packages), __('Packages retrieved successfully'));
        } catch (\Exception $e) {
            return $this->handleException($e, __('Failed to retrieve packages'));
        }
    }
}       