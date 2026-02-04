<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Ticket::query()
            ->with(['customer:id,name,email', 'assignedAgent:id,name,email', 'category:id,name'])
            ->latest();

        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        $tickets = $query->paginate(10)->withQueryString();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'canCreate' => $user->isCustomer(),
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if (! $user->isCustomer()) {
            abort(403);
        }

        return Inertia::render('Tickets/Create', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->isCustomer()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ]);

        $ticket = Ticket::create([
            'customer_id' => $user->id,
            'category_id' => $validated['category_id'] ?? null,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => Ticket::STATUS_OPEN,
        ]);

        if (! empty($validated['attachments'])) {
            $this->storeAttachments($validated['attachments'], $ticket, null, $user);
        }

        TicketAudit::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'created',
        ]);

        return redirect()->route('tickets.show', $ticket);
    }

    public function show(Request $request, Ticket $ticket)
    {
        Gate::authorize('ticket.view', $ticket);

        $ticket->load([
            'customer:id,name,email',
            'assignedAgent:id,name,email',
            'category:id,name',
            'messages' => function ($query) {
                $query->with(['user:id,name,role', 'attachments'])
                    ->orderBy('created_at');
            },
            'audits' => function ($query) {
                $query->with(['user:id,name,role'])->latest();
            },
        ]);

        $ticket->setRelation(
            'attachments',
            $ticket->attachments()->whereNull('message_id')->get()
        );

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
            'can' => [
                'reply' => Gate::check('ticket.reply', $ticket),
                'updateStatus' => Gate::check('ticket.updateStatus', $ticket),
                'assign' => Gate::check('ticket.assign', $ticket),
            ],
            'agents' => $request->user()->isAdmin()
                ? \App\Models\User::query()->where('role', 'agent')->orderBy('name')->get(['id', 'name'])
                : ($request->user()->isAgent()
                    ? collect([$request->user()])->map(function ($user) {
                        return $user->only(['id', 'name']);
                    })
                    : []),
        ]);
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        Gate::authorize('ticket.updateStatus', $ticket);

        $validated = $request->validate([
            'status' => ['required', 'in:OPEN,IN_PROGRESS,WAITING_CUSTOMER,RESOLVED,CLOSED'],
        ]);

        $nextStatus = $validated['status'];
        $allowedTransitions = [
            Ticket::STATUS_OPEN => [Ticket::STATUS_IN_PROGRESS],
            Ticket::STATUS_IN_PROGRESS => [Ticket::STATUS_WAITING_CUSTOMER, Ticket::STATUS_RESOLVED],
            Ticket::STATUS_WAITING_CUSTOMER => [Ticket::STATUS_IN_PROGRESS, Ticket::STATUS_RESOLVED],
            Ticket::STATUS_RESOLVED => [Ticket::STATUS_CLOSED],
            Ticket::STATUS_CLOSED => [],
        ];

        if (! in_array($nextStatus, $allowedTransitions[$ticket->status] ?? [], true)) {
            return back()->withErrors([
                'status' => 'Transisi status tidak valid.',
            ]);
        }

        $ticket->status = $nextStatus;

        if ($nextStatus === Ticket::STATUS_RESOLVED) {
            $ticket->resolved_at = now();
        }

        if ($nextStatus === Ticket::STATUS_CLOSED) {
            $ticket->closed_at = now();
        }

        $ticket->save();

        TicketAudit::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'action' => 'status_updated',
            'meta' => ['status' => $validated['status']],
        ]);

        return back();
    }

    public function assign(Request $request, Ticket $ticket)
    {
        Gate::authorize('ticket.assign', $ticket);

        $validated = $request->validate([
            'assigned_agent_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('role', 'agent'),
            ],
        ]);

        $user = $request->user();

        if ($user->isAgent() && (int) $validated['assigned_agent_id'] !== $user->id) {
            abort(403);
        }

        $ticket->assigned_agent_id = $validated['assigned_agent_id'];

        if (! $ticket->first_response_at && $user->isAgent()) {
            $ticket->first_response_at = now();
        }

        $ticket->save();

        TicketAudit::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'assigned',
            'meta' => ['assigned_agent_id' => (int) $validated['assigned_agent_id']],
        ]);

        return back();
    }

    private function storeAttachments(array $files, Ticket $ticket, $message, $user): void
    {
        foreach ($files as $file) {
            $path = $file->store("tickets/{$ticket->id}");

            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'message_id' => $message?->id,
                'uploaded_by' => $user->id,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
