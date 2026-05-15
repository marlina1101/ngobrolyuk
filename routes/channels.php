<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| PRESENCE CHANNEL — Chat Online/Offline
|--------------------------------------------------------------------------
|
| Channel ini dipakai untuk tracking user online
| menggunakan Echo.join('chat')
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
| PRIVATE CHANNEL — User Notification
|--------------------------------------------------------------------------
|
| Channel private untuk notifikasi per-user
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {

    return (int) $user->id === (int) $id;

});