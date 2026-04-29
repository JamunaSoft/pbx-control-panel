<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The callback receives the authenticated user
| and should return true if they are authorized to listen.
|
*/

Broadcast::channel('extensions', function ($user) {
    // Allow any authenticated user to receive extension status updates
    // For finer control, check permissions:
    // return $user->can('view extensions');
    return $user !== null;
});
