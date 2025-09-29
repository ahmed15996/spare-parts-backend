<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;





Route::group(['prefix' => LaravelLocalization::setLocale(),
'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
], function () {
    Route::get('/', function () {
        abort(404);
    });
    Route::get('/chat', function () {
        return view('welcome');
    });
});

// Simple login route for testing broadcasting
Route::get('/login-test-1', function () {
    // Get the first user from the database
    $user = \App\Models\User::first();
    if ($user) {
        Auth::guard('web')->login($user);
        return redirect('/')->with('message', 'Logged in as: ' . $user->first_name . ' ' . $user->last_name);
    }
    return 'No users found in database';
});


Route::get('/login-test-2', function () {
    // Get the first user from the database
    $user = \App\Models\User::find(2);
    if ($user) {
        Auth::guard('web')->login($user);
        return redirect('/')->with('message', 'Logged in as: ' . $user->first_name . ' ' . $user->last_name);
    }
    return 'No users found in database';
});

// Debug route for broadcasting auth
Route::get('/debug-auth', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'user_name' => auth()->user() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : null,
        'guard' => 'web',
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'cookies' => request()->cookies->all(),
        'headers' => request()->headers->all(),
    ]);
});

// Test broadcasting auth manually
Route::get('/test-broadcasting-auth', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }
    
    return response()->json([
        'user_id' => auth()->id(),
        'can_access_user_channel' => true,
        'conversations' => auth()->user()->conversations()->count(),
    ]);
});

// Custom broadcasting auth route for debugging
Route::post('/debug-broadcasting-auth', function (Illuminate\Http\Request $request) {
    Log::info('Broadcasting auth request received', [
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'request_data' => $request->all(),
        'headers' => $request->headers->all(),
        'session_id' => session()->getId(),
    ]);
    
    if (!auth()->check()) {
        Log::error('Broadcasting auth failed - not authenticated');
        return response()->json(['error' => 'Not authenticated'], 401);
    }
    
    return response()->json([
        'auth' => [
            'user.1' => ['id' => auth()->id(), 'name' => auth()->user()->first_name],
        ]
    ]);
})->middleware(['web', 'auth']);
