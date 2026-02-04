<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAudit;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HelpdeskSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@helpdesk.test'],
            [
                'name' => 'Admin Helpdesk',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        );

        $agent = User::updateOrCreate(
            ['email' => 'agent@helpdesk.test'],
            [
                'name' => 'Agent Support',
                'password' => Hash::make('password'),
                'role' => 'agent',
            ],
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@helpdesk.test'],
            [
                'name' => 'Customer Demo',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ],
        );

        $billing = Category::updateOrCreate(
            ['name' => 'Billing'],
            ['sla_response_minutes' => 60, 'sla_resolution_minutes' => 360],
        );

        $technical = Category::updateOrCreate(
            ['name' => 'Technical'],
            ['sla_response_minutes' => 30, 'sla_resolution_minutes' => 240],
        );

        $ticketA = Ticket::create([
            'customer_id' => $customer->id,
            'assigned_agent_id' => $agent->id,
            'category_id' => $technical->id,
            'subject' => 'Aplikasi tidak bisa login',
            'description' => "Tidak bisa login sejak pagi. Sudah coba reset password.",
            'status' => Ticket::STATUS_IN_PROGRESS,
            'first_response_at' => now()->subMinutes(45),
        ]);

        $ticketB = Ticket::create([
            'customer_id' => $customer->id,
            'assigned_agent_id' => null,
            'category_id' => $billing->id,
            'subject' => 'Invoice bulan ini salah',
            'description' => "Total invoice tidak sesuai paket langganan.",
            'status' => Ticket::STATUS_OPEN,
        ]);

        $messageA1 = TicketMessage::create([
            'ticket_id' => $ticketA->id,
            'user_id' => $customer->id,
            'body' => 'Saya tidak bisa login setelah update aplikasi.',
        ]);

        $messageA2 = TicketMessage::create([
            'ticket_id' => $ticketA->id,
            'user_id' => $agent->id,
            'body' => 'Kami cek ya. Bisa kirimkan screenshot error?',
        ]);

        TicketAudit::create([
            'ticket_id' => $ticketA->id,
            'user_id' => $customer->id,
            'action' => 'created',
        ]);

        TicketAudit::create([
            'ticket_id' => $ticketA->id,
            'user_id' => $agent->id,
            'action' => 'auto_assigned',
            'meta' => ['assigned_agent_id' => $agent->id],
        ]);

        TicketAudit::create([
            'ticket_id' => $ticketA->id,
            'user_id' => $agent->id,
            'action' => 'message_added',
            'meta' => ['message_id' => $messageA2->id],
        ]);

        TicketAudit::create([
            'ticket_id' => $ticketB->id,
            'user_id' => $customer->id,
            'action' => 'created',
        ]);
    }
}
