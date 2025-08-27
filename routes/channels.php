<?php

// use Illuminate\Support\Facades\Broadcast;
// use Illuminate\Support\Facades\Log;
// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// // Alias for user personal channel
// Broadcast::channel('user.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });


// Broadcast::channel('conversations.{conversation_id}', function ($user, $conversation_id) {
//     return $user->conversations()->where('id', $conversation_id)->exists();
// });