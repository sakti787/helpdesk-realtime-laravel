<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function show(Request $request, Ticket $ticket, TicketAttachment $attachment)
    {
        Gate::authorize('ticket.view', $ticket);

        if ($attachment->ticket_id !== $ticket->id) {
            abort(404);
        }

        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }
}
