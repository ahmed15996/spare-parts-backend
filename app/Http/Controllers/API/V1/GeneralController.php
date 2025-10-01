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
use App\Http\Resources\API\V1\OnboardingResource;
use App\Models\Brand;
use App\Models\City;
use App\Models\Day;
use App\Models\DeleteAccountReason;
use App\Models\Package;
use App\Models\Onboarding;
use App\Models\PaymentMethod;
use App\Models\Faq;
use App\Services\CategoryService;
use App\Services\CityService;
use App\Services\BrandService;
use App\Enums\BannerType;
use App\Http\Resources\API\V1\DeleteAccountReasonResource;
use App\Http\Resources\API\V1\FaqResource;
use App\Http\Resources\FaqResource as ResourcesFaqResource;
use App\Http\Resources\API\V1\PaymentMethodsResource;

use function App\Helpers\setting;
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
        public function paymentMethods()
    {
        $paymentMethods = PaymentMethod::all();
        return $this->successResponse(PaymentMethodsResource::collection($paymentMethods), __('Payment Methods retrieved successfully'));
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

    public function onboarding()
    {
        try {
            $onboardings = Cache::rememberForever('onboardings', function () {
                return Onboarding::ordered()->active()->get();
            });
            return $this->collectionResponse(OnboardingResource::collection($onboardings), __('Onboardings retrieved successfully'));
        } catch (\Exception $e) {
            return $this->handleException($e, __('Failed to retrieve onboardings'));
        }
    }

    // Content Routes 
    public function privacy()
    {
        $privacy = setting('content','privacy_'.app()->getLocale());
        return $this->successResponse($privacy, __('Privacy retrieved successfully'));
    }
    public function terms()
    {
        $terms = setting('content','terms_'.app()->getLocale());
        return $this->successResponse($terms, __('Terms retrieved successfully'));
    }
    public function aboutUs()
    {
        $aboutUs = setting('content','about_us_'.app()->getLocale());
        return $this->successResponse($aboutUs, __('About Us retrieved successfully'));
    }
    public function providerCommissionText()
    {
        $providerCommissionText = setting('commission','provider_commission_text_'.app()->getLocale());
        return $this->successResponse($providerCommissionText, __('Provider Commission Text retrieved successfully'));
    }
    public function clientCommissionText()
    {
        $clientCommissionText = setting('commission','client_commission_text_'.app()->getLocale());
        return $this->successResponse($clientCommissionText, __('Client Commission Text retrieved successfully'));
    }
    public function faqs()
    {
        $faqs = Faq::all();
        return $this->successResponse(ResourcesFaqResource::collection($faqs), __('FAQs retrieved successfully'));
    }
    public function media()
    {
        $media = [
            'linked_in' => setting('media','linked_in'),
            'facebook' => setting('media','facebook'),
            'twitter' => setting('media','twitter'),
            'tiktok' => setting('media','tiktok'),
            'instagram' => setting('media','instagram'),
            'snapchat' => setting('media','snapchat'),
            'app_store' => setting('media','app_store'),
            'google_play' => setting('media','google_play'),
        ];
        return $this->successResponse($media, __('Media links retrieved successfully'));
    }
}       