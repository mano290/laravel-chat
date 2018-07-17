<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Room;

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat', function ($user) {
    return Auth::check();
});

Broadcast::channel('roomChat.{uid}', function ($user, $uid) {

    $room = Room::whereHas("participants", function ($query) use ($user) {
        return $query->where("user_id", $user->id);
    })->where("uid", $uid)->first();

    return (is_object($room));
});