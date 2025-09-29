<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\BannerController;
use App\Http\Controllers\API\V1\ContactController;
use App\Http\Controllers\API\V1\GeneralController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\BlockController;

    //# General endpoints 
    Route::group(['as'=>'auth.', 'prefix'=>'auth'], function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('verify-code', [AuthController::class, 'verifyActiveCode'])->name('verify-active-code');
        Route::post('resend-code', [AuthController::class, 'resendActiveCode'])->name('resend-active-code');
        Route::post('provider/register', [AuthController::class, 'providerRegisterRequest'])->name('provider.register');
        Route::post('client/register', [AuthController::class, 'clientRegister'])->name('client.register');
        

    Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::post('delete-account', [AuthController::class, 'deleteAccount'])->name('delete-account');
            Route::post('update-profile', [AuthController::class, 'updateProfile'])->name('update-profile');
            Route::get('profile', [AuthController::class, 'getProfile'])->name('profile');
            Route::get('notifications', [AuthController::class, 'getNotifications'])->name('notifications');
            Route::post('notifications/mark-as-read', [AuthController::class, 'markAsRead'])->name('notifications.mark-as-read');
            Route::get('delete-account-reasons', [AuthController::class, 'deleteAccountReasons'])->name('delete-account-reasons');

            // Route::get('broadcasting/test', function() {
            //     return response()->json([
            //         'authenticated' => true,
            //         'user_id' => auth()->id(),
            //         'user_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            //         'guard' => 'sanctum',
            //         'conversations_count' => auth()->user()->conversations()->count(),
            //         'broadcasting_auth_url' => url('/api/broadcasting/auth'),
            //         'channels' => [
            //             'user' => 'private-user.' . auth()->id(),
            //             'conversation_example' => 'private-conversations.1'
            //         ]
            //     ]);
            // })->name('broadcasting.test');
                // Block routes
    Route::post('/blocks', [BlockController::class, 'block'])->name('blocks.store');
    Route::delete('/blocks/{id}', [BlockController::class, 'unblock'])->name('blocks.destroy');
    Route::get('/blocks', [BlockController::class, 'listBlocks'])->name('blocks.index');
    Route::get('/blocks/{id}/status', [BlockController::class, 'checkBlockStatus'])->name('blocks.status');

            // Provider profile update request (requires provider role)
            Route::post('provider/profile-update-request', [AuthController::class, 'providerProfileUpdateRequest'])
                ->name('provider.profile-update-request');
            Route::get('provider/profile', [AuthController::class, 'providerProfile'])->name('provider.profile');
        });
    });
    