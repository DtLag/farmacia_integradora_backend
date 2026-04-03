<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('staff.orders', function ($user) {
    return $user->role_id === 1 || $user->role_id === 2;
});
Broadcast::channel('staff.alerts', function ($user) {
    return $user->role_id === 1 || $user->role_id === 2;
});