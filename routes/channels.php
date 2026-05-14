<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('chat', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
