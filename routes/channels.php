<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Convo Lite Broadcast Channels
|--------------------------------------------------------------------------
|
| Add these channel definitions to your application's routes/channels.php
|
*/

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('convo-lite.online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});
