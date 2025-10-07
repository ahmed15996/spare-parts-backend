<?php

use App\Http\Controllers\API\V1\CommissionController;
use App\Http\Controllers\API\V1\Provider\BannerController;
use App\Http\Controllers\API\V1\ProviderController;
use App\Http\Controllers\API\V1\Provider\ProductController as ProviderProductController;
use Illuminate\Support\Facades\Route;


    //# General endpoints 
    Route::group(['as'=>'provider.','prefix'=>'provider','middleware' => ['auth:sanctum','role:provider']], function () {
        Route::get('/packages', [ProviderController::class, 'packages'])->name('packages');
        Route::post('/packages/{id}/subscribe', [ProviderController::class, 'subscribeToPackage'])->name('packages.subscribe');
        Route::get('/reviews', [ProviderController::class, 'getProviderReviews'])->name('reviews');
        // Working days routes
        Route::get('/days', [ProviderController::class, 'days'])->name('days');
        Route::post('/days', [ProviderController::class, 'updateDays'])->name('days.update');
        Route::group(['prefix'=>'home','as'=>'home.'],function(){
            Route::get('/statistics', [ProviderController::class, 'statistics'])->name('statistics');
            Route::get('/requests', [ProviderController::class, 'homeRequests'])->name('requests');
        });
        Route::group(['prefix'=>'requests','as'=>'requests.'],function(){
            Route::get('/{id}', [ProviderController::class, 'request'])->name('show');
            Route::get('/{id}/offers', [ProviderController::class, 'requestOffers'])->name('offers');
            Route::post('/{id}/offers', [ProviderController::class, 'sendOffer'])->name('send-offer');
            Route::post('/{id}/hide', [ProviderController::class, 'hideRequest'])->name('hide');
        });

        Route::group(['prefix'=>'offers','as'=>'offers.'],function(){
            Route::get('/', [ProviderController::class, 'myOffers'])->name('index');
            Route::get('/{id}', [ProviderController::class, 'offerShow'])->name('show');
        });
        Route::group(['as'=>'commissions','prefix'=>'commissions'],function(){
            Route::post('/', [CommissionController::class, 'providerCommission'])->name('store');
            Route::get('/products', [CommissionController::class, 'products'])->name('products');
        });

        Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
            Route::get('/', [ProviderProductController::class, 'index'])->name('index');
            Route::get('/{id}', [ProviderProductController::class, 'show'])->name('show');
            Route::post('/', [ProviderProductController::class, 'store'])->name('store');
            Route::post('/{id}', [ProviderProductController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProviderProductController::class, 'destroy'])->name('destroy');
        });
        Route::group(['prefix' => 'banners', 'as' => 'banners.'], function () {
            Route::get('/', [BannerController::class, 'index'])->name('index');
            Route::get('/{id}', [BannerController::class, 'show'])->name('show');
            Route::post('/', [BannerController::class, 'store'])->name('store');
        });
    });