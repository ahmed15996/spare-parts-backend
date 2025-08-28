<?php

use App\Http\Controllers\API\V1\BannerController;
use App\Http\Controllers\API\V1\ContactController;
use App\Http\Controllers\API\V1\GeneralController;
use Illuminate\Support\Facades\Route;


    //# General endpoints 
    Route::group(['as'=>'general.','prefix'=>'general'], function () {
        Route::get('/categories', [GeneralController::class, 'categories'])->name('categories');
        Route::get('/brands', [GeneralController::class, 'brands'])->name('brands');
        Route::get('/brand/{brand}/models', [GeneralController::class, 'brandModels'])->name('brand-models');
        Route::get('/cities', [GeneralController::class, 'cities'])->name('cities');
        Route::get('/settings', [GeneralController::class, 'settings'])->name('settings');
        Route::get('/banner-types', [GeneralController::class, 'bannerTypes'])->name('banner-types');
        Route::get('/days', [GeneralController::class, 'days'])->name('days');
    });
