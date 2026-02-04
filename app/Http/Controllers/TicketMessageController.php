<?php

namespace App\Http\Controllers;

use App\Events\TicketMessageCreated;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketAudit;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketMessageController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        Gate::authorize('ticket.reply', $ticket);

        $validated = $request->validate([
            'body' => ['required', 'string'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ]);

        $user = $request->user();

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        if (! empty($validated['attachments'])) {
            $this->storeAttachments($validated['attachments'], $ticket, $message, $user);
        }

        if ($user->isAgent()) {
            $autoAssigned = false;

            if (! $ticket->assigned_agent_id) {
                $ticket->assigned_agent_id = $user->id;
                $ticket->status = Ticket::STATUS_IN_PROGRESS;
                $autoAssigned = true;
            }

            if (! $ticket->first_response_at) {
                $ticket->first_response_at = now();
            }

            if ($autoAssigned || $ticket->isDirty()) {
                $ticket->save();
            }

            if ($autoAssigned) {
                TicketAudit::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'action' => 'auto_assigned',
                    'meta' => ['assigned_agent_id' => $user->id],
                ]);
            }
        }

        TicketAudit::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'message_added',
            'meta' => ['message_id' => $message->id],
        ]);

        broadcast(new TicketMessageCreated($ticket, $message))->toOthers();

        return back();
    }

    private function storeAttachments(array $files, Ticket $ticket, TicketMessage $message, $user): void
    {
        foreach ($files as $file) {
            $path = $file->store("tickets/{$ticket->id}");

            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'message_id' => $message->id,
                'uploaded_by' => $user->id,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
