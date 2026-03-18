<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('consultation.user.{userId}', function ($user, int $userId) {
    $authenticatedUserId = is_object($user)
        ? (int) ($user->id ?? 0)
        : (int) data_get($user, 'id', 0);

    return $authenticatedUserId === $userId;
});
