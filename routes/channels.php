<?php

use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('ticket.{ticket}', function ($user, Ticket $ticket) {
    return $user->isAdmin()
        || $user->isAgent()
        || $ticket->customer_id === $user->id;
});
