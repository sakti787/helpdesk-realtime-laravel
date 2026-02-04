<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { NButton, NTag } from 'naive-ui';

const props = defineProps({
    tickets: Object,
    canCreate: Boolean,
});

const statusColor = (status) => {
    switch (status) {
        case 'OPEN':
            return 'default';
        case 'IN_PROGRESS':
            return 'info';
        case 'WAITING_CUSTOMER':
            return 'warning';
        case 'RESOLVED':
            return 'success';
        case 'CLOSED':
            return 'error';
        default:
            return 'default';
    }
};

const statusCounts = computed(() => {
    const counts = {
        OPEN: 0,
        IN_PROGRESS: 0,
        WAITING_CUSTOMER: 0,
        RESOLVED: 0,
        CLOSED: 0,
    };

    (Array.isArray(props.tickets?.data) ? props.tickets.data : []).forEach((ticket) => {
        if (counts[ticket.status] !== undefined) {
            counts[ticket.status] += 1;
        }
    });

    return counts;
});
</script>

<template>
    <Head title="Tickets" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Tickets
                </h2>
                <Link v-if="canCreate" :href="route('tickets.create')">
                    <NButton type="primary">Buat Ticket</NButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="glass-card overflow-hidden sm:rounded-3xl">
                    <div class="border-b border-slate-200/70 bg-white/60 p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="text-sm uppercase tracking-[0.3em] text-slate-400">
                                    Ticket Overview
                                </div>
                                <div class="text-2xl font-semibold text-slate-900">
                                    Pantau antrian dan status ticket.
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <div class="rounded-2xl border border-slate-200/70 bg-white px-4 py-3 text-sm text-slate-600">
                                    <div class="text-xs uppercase tracking-wider text-slate-400">OPEN</div>
                                    <div class="text-xl font-semibold text-slate-900">
                                        {{ statusCounts.OPEN }}
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200/70 bg-white px-4 py-3 text-sm text-slate-600">
                                    <div class="text-xs uppercase tracking-wider text-slate-400">IN PROGRESS</div>
                                    <div class="text-xl font-semibold text-slate-900">
                                        {{ statusCounts.IN_PROGRESS }}
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200/70 bg-white px-4 py-3 text-sm text-slate-600">
                                    <div class="text-xs uppercase tracking-wider text-slate-400">WAITING</div>
                                    <div class="text-xl font-semibold text-slate-900">
                                        {{ statusCounts.WAITING_CUSTOMER }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto rounded-2xl border border-slate-200/70 bg-white/80">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                        <th class="px-4 py-3">Subject</th>
                                        <th class="px-4 py-3">Customer</th>
                                        <th class="px-4 py-3">Assigned</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Updated</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <tr
                                        v-for="ticket in tickets.data"
                                        :key="ticket.id"
                                        class="hover:bg-slate-50"
                                    >
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-gray-900">
                                                {{ ticket.subject }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                #{{ ticket.id }}
                                                <span v-if="ticket.category"> • {{ ticket.category.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ ticket.customer?.name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ ticket.assigned_agent?.name ?? 'Unassigned' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <NTag :type="statusColor(ticket.status)" size="small">
                                                {{ ticket.status }}
                                            </NTag>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ new Date(ticket.updated_at).toLocaleString() }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <Link :href="route('tickets.show', ticket.id)">
                                                <NButton size="small">Detail</NButton>
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="tickets.data.length === 0">
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                            Belum ada ticket.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex items-center justify-between text-sm text-gray-600">
                            <div>
                                Menampilkan {{ tickets.from ?? 0 }}-{{ tickets.to ?? 0 }} dari
                                {{ tickets.total ?? 0 }}
                            </div>
                            <div class="flex gap-2">
                                <Link v-if="tickets.prev_page_url" :href="tickets.prev_page_url">
                                    <NButton size="small" secondary>Prev</NButton>
                                </Link>
                                <Link v-if="tickets.next_page_url" :href="tickets.next_page_url">
                                    <NButton size="small" secondary>Next</NButton>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
