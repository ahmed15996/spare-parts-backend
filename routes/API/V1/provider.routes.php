<?php

use App\Http\Controllers\API\V1\ProviderController;
use Illuminate\Support\Facades\Route;


    //# General endpoints 
    Route::group(['as'=>'provider.','prefix'=>'provider','middleware' => ['auth:sanctum']], function () {
        Route::get('/packages', [ProviderController::class, 'packages'])->name('packages');
    });
