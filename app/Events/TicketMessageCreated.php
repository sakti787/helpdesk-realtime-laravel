<?php

namespace App\Events;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketMessageCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public TicketMessage $message
    ) {
        $this->message->load(['user:id,name,role', 'attachments']);
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('ticket.'.$this->ticket->id);
    }

    public function broadcastAs(): string
    {
        return 'ticket.message.created';
    }

    public function broadcastWith(): array
    {
        return [
            'ticketId' => $this->ticket->id,
            'message' => [
                'id' => $this->message->id,
                'body' => $this->message->body,
                'created_at' => $this->message->created_at?->toISOString(),
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'role' => $this->message->user->role,
                ],
                'attachments' => $this->message->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'original_name' => $attachment->original_name,
                        'path' => $attachment->path,
                        'mime' => $attachment->mime,
                        'size' => $attachment->size,
                    ];
                })->values(),
            ],
        ];
    }
}
