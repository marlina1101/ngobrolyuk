<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| PRESENCE CHANNEL — ONLINE/OFFLINE TRACKING
|--------------------------------------------------------------------------
|
| Dipakai oleh:
| window.Echo.join('chat')
|
*/

Broadcast::channel('chat', function ($user) {

    return [
        'id'     => $user->id,
        'name'   => $user->name,
        'avatar' => $user->avatar ?? null,
    ];

});

/*
|--------------------------------------------------------------------------
| PRIVATE CHAT CHANNEL
|--------------------------------------------------------------------------
|
| Dipakai oleh:
| Echo.private(`chat.${userId}`)
|
| Contoh:
| User 5 hanya bisa masuk ke chat.5
| User 9 hanya bisa masuk ke chat.9
|
*/

Broadcast::channel('chat.{id}', function ($user, $id) {

    return (int) $user->id === (int) $id;

});

/*
|--------------------------------------------------------------------------
| DEFAULT USER PRIVATE CHANNEL
|--------------------------------------------------------------------------
|
| Channel bawaan Laravel
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {

    return (int) $user->id === (int) $id;

});