<?php

use App\Models\Agent;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('backoffice-notifications', function ($user) {
    // Ensure the authenticated user is an agent
    return $user instanceof Agent;
});
