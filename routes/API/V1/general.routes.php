<?php

use App\Http\Controllers\API\V1\BannerController;
use App\Http\Controllers\API\V1\ContactController;
use App\Http\Controllers\API\V1\GeneralController;
use App\Http\Controllers\API\V1\PostController;
use App\Http\Controllers\API\V1\CommentController;
use App\Http\Controllers\API\V1\LikeController;
use App\Http\Controllers\DeleteAccountReasonController;
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
        Route::get('/onboarding', [GeneralController::class, 'onboarding'])->name('onboarding');
        Route::get('/privacy',[GeneralController::class,'privacy']);
        Route::get('/about-us',[GeneralController::class,'aboutUs']);
        Route::get('/provider-commission',[GeneralController::class,'ProviderCommissionText']);
        Route::get('/client-commission',[GeneralController::class,'ClientCommissionText']);
        Route::get('/delete-account-reasons', [DeleteAccountReasonController::class, 'index'])->name('delete-account-reasons');
    });
    Route::group(['as'=>'contacts.','prefix'=>'contacts'], function () {
        Route::post('/', [ContactController::class, 'store'])->name('store');
    });

    //# Posts endpoints (authenticated users)
    Route::group(['as'=>'posts.','prefix'=>'posts', 'middleware' => 'auth:sanctum'], function () {
        Route::get('/', [PostController::class, 'feed'])->name('feed');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::get('/{id}', [PostController::class, 'show'])->name('show');
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('destroy');

        Route::post('/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::delete('/{id}/comments/{comment_id}', [CommentController::class, 'destroy'])->name('comments.destroy');

        Route::post('/{id}/like', [LikeController::class, 'reactToPost'])->name('react');
        Route::get('/{id}/likers', [LikeController::class, 'likersOfPost'])->name('likers');
    });

    // Comment reactions
    // Route::post('/comments/{id}/react', [LikeController::class, 'reactToComment'])->name('comments.react');
