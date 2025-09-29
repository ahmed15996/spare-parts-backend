<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;


// Web broadcasting routes (for web interface with sessions)
Broadcast::routes(['middleware' => ['auth:sanctum']]);
Broadcast::routes(['middleware' => ['web', 'auth']]);

// API broadcasting routes (for mobile apps with Sanctum tokens)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Alias for user personal channel
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('conversations.{conversation_id}', function ($user, $conversation_id) {
    try {
        Log::debug('Broadcasting auth check', [
            'user_id' => $user->id,
            'conversation_id' => $conversation_id
        ]);
        
        $hasAccess = $user->conversations()->where('id', $conversation_id)->exists();
        
        Log::debug('Broadcasting auth result', [
            'user_id' => $user->id,
            'conversation_id' => $conversation_id,
            'has_access' => $hasAccess
        ]);
        
        return $hasAccess;
    } catch (\Exception $e) {
        Log::error('Broadcasting auth error', [
            'user_id' => $user->id ?? 'null',
            'conversation_id' => $conversation_id,
            'error' => $e->getMessage()
        ]);
        return false;
    }
});