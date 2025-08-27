<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\BannerController;
use App\Http\Controllers\API\V1\ContactController;
use App\Http\Controllers\API\V1\GeneralController;
use Illuminate\Support\Facades\Route;


    //# General endpoints 
    Route::group(['as'=>'auth.', 'prefix'=>'auth'], function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('verify-code', [AuthController::class, 'verifyActiveCode'])->name('verify-active-code');
        Route::post('provider/register', [AuthController::class, 'providerRegisterRequest'])->name('provider.register');
    });
