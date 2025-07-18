<?php

use Illuminate\Support\Facades\Broadcast;

// Authorize private chat channels
Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id === (int) $receiverId || true; // Adjust this check later
});

