<?php

use App\Http\Controllers\API\V1\ProviderController;
use Illuminate\Support\Facades\Route;


    //# General endpoints 
    Route::group(['as'=>'provider.','prefix'=>'provider','middleware' => ['auth:sanctum','role:provider']], function () {
        Route::get('/packages', [ProviderController::class, 'packages'])->name('packages');
        Route::group(['prefix'=>'home','as'=>'home.'],function(){
            Route::get('/statistics', [ProviderController::class, 'statistics'])->name('statistics');
            Route::get('/requests', [ProviderController::class, 'homeRequests'])->name('requests');
        });
        Route::group(['prefix'=>'requests','as'=>'requests.'],function(){
            Route::get('/{id}', [ProviderController::class, 'request'])->name('show');
            Route::get('/{id}/offers', [ProviderController::class, 'requestOffers'])->name('offers');
            Route::post('/{id}/offers', [ProviderController::class, 'sendOffer'])->name('send-offer');
        });

        Route::group(['prefix'=>'offers','as'=>'offers.'],function(){
            Route::get('/', [ProviderController::class, 'myOffers'])->name('index');
            Route::get('/{id}', [ProviderController::class, 'offerShow'])->name('show');
        });
    });