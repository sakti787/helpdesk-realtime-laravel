<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { NButton, NCard, NSelect, NTag, NInput } from 'naive-ui';

const props = defineProps({
    ticket: Object,
    can: Object,
    agents: Array,
});

const statusOptions = [
    { label: 'OPEN', value: 'OPEN' },
    { label: 'IN_PROGRESS', value: 'IN_PROGRESS' },
    { label: 'WAITING_CUSTOMER', value: 'WAITING_CUSTOMER' },
    { label: 'RESOLVED', value: 'RESOLVED' },
    { label: 'CLOSED', value: 'CLOSED' },
];

const statusForm = useForm({
    status: props.ticket.status,
});

const assignForm = useForm({
    assigned_agent_id: props.ticket.assigned_agent_id ?? null,
});

const messageForm = useForm({
    body: '',
    attachments: [],
});

const messages = ref([...props.ticket.messages]);

const handleFiles = (event) => {
    messageForm.attachments = Array.from(event.target.files ?? []);
};

const submitStatus = () => {
    statusForm.patch(route('tickets.status', props.ticket.id), {
        preserveScroll: true,
    });
};

const submitAssign = () => {
    assignForm.patch(route('tickets.assign', props.ticket.id), {
        preserveScroll: true,
    });
};

const submitMessage = () => {
    messageForm.post(route('tickets.messages.store', props.ticket.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            messageForm.reset('body', 'attachments');
        },
    });
};

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

const attachmentUrl = (attachment) =>
    route('tickets.attachments.show', {
        ticket: props.ticket.id,
        attachment: attachment.id,
    });

const messageCardClass = (message) => {
    const isSupport = ['agent', 'admin'].includes(message.user?.role);
    return [
        'rounded-2xl border p-4',
        isSupport ? 'border-teal-200 bg-teal-50/70' : 'border-slate-200 bg-white',
    ].join(' ');
};

let channel = null;

onMounted(() => {
    if (window.Echo) {
        channel = window.Echo.private(`ticket.${props.ticket.id}`);
        channel.listen('.ticket.message.created', (event) => {
            if (event?.message) {
                messages.value.push(event.message);
            }
        });
    }
});

onBeforeUnmount(() => {
    if (channel) {
        channel.stopListening('.ticket.message.created');
        window.Echo.leave(`ticket.${props.ticket.id}`);
    }
});
</script>

<template>
    <Head :title="`Ticket #${ticket.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Ticket #{{ ticket.id }}
                    </h2>
                    <div class="text-sm text-gray-500">{{ ticket.subject }}</div>
                </div>
                <NTag :type="statusColor(ticket.status)">{{ ticket.status }}</NTag>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto grid max-w-7xl gap-6 lg:grid-cols-[1.2fr,0.8fr] sm:px-6 lg:px-8">
                <div class="space-y-6">
                    <NCard class="glass-card">
                        <div class="space-y-3">
                            <div class="text-sm text-gray-500">Deskripsi</div>
                            <div class="text-gray-900 whitespace-pre-line">{{ ticket.description }}</div>
                            <div class="text-sm text-gray-500">
                                Customer: {{ ticket.customer?.name }} •
                                {{ ticket.customer?.email }}
                            </div>
                            <div v-if="ticket.attachments.length" class="mt-4">
                                <div class="text-sm font-medium text-gray-700">Attachments</div>
                                <ul class="mt-2 space-y-1 text-sm text-blue-600">
                                    <li v-for="file in ticket.attachments" :key="file.id">
                                        <a :href="attachmentUrl(file)" target="_blank" class="underline">
                                            {{ file.original_name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </NCard>

                    <NCard class="glass-card">
                        <div class="mb-4 text-sm font-semibold text-gray-700">Chat</div>
                        <div class="space-y-4">
                            <div
                                v-for="message in messages"
                                :key="message.id"
                                :class="messageCardClass(message)"
                            >
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <div>
                                        <span class="font-medium text-gray-700">{{ message.user?.name }}</span>
                                        <span class="ml-2 text-xs uppercase">{{ message.user?.role }}</span>
                                    </div>
                                    <div>{{ new Date(message.created_at).toLocaleString() }}</div>
                                </div>
                                <div class="mt-2 whitespace-pre-line text-gray-900">
                                    {{ message.body }}
                                </div>
                                <ul v-if="message.attachments?.length" class="mt-2 space-y-1 text-sm text-blue-600">
                                    <li v-for="file in message.attachments" :key="file.id">
                                        <a :href="attachmentUrl(file)" target="_blank" class="underline">
                                            {{ file.original_name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div v-if="can.reply" class="mt-6 space-y-3">
                            <NInput
                                v-model:value="messageForm.body"
                                type="textarea"
                                :autosize="{ minRows: 3 }"
                                placeholder="Tulis balasan..."
                            />
                            <input
                                type="file"
                                multiple
                                class="block w-full text-sm text-gray-600"
                                @change="handleFiles"
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                            />
                            <div class="text-xs text-gray-500">
                                Maks 5 file, 5MB/file. Tipe: jpg, png, pdf, doc, docx.
                            </div>
                            <div class="flex justify-end">
                                <NButton type="primary" :loading="messageForm.processing" @click="submitMessage">
                                    Kirim
                                </NButton>
                            </div>
                            <div v-if="messageForm.errors.body" class="text-sm text-red-600">
                                {{ messageForm.errors.body }}
                            </div>
                        </div>
                    </NCard>
                </div>

                <div class="space-y-6">
                    <NCard class="glass-card">
                        <div class="space-y-4">
                            <div>
                                <div class="text-sm text-gray-500">Kategori</div>
                                <div class="font-medium text-gray-900">
                                    {{ ticket.category?.name ?? '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Assigned Agent</div>
                                <div class="font-medium text-gray-900">
                                    {{ ticket.assigned_agent?.name ?? 'Unassigned' }}
                                </div>
                            </div>
                            <div v-if="can.assign">
                                <div class="text-sm text-gray-500">Assign / Claim</div>
                                <NSelect
                                    v-model:value="assignForm.assigned_agent_id"
                                    :options="agents.map(agent => ({ label: agent.name, value: agent.id }))"
                                    placeholder="Pilih agent"
                                />
                                <div class="mt-3">
                                    <NButton size="small" :loading="assignForm.processing" @click="submitAssign">
                                        Simpan
                                    </NButton>
                                </div>
                            </div>
                        </div>
                    </NCard>

                    <NCard v-if="can.updateStatus" class="glass-card">
                        <div class="text-sm text-gray-500">Update Status</div>
                        <div class="mt-2">
                            <NSelect v-model:value="statusForm.status" :options="statusOptions" />
                        </div>
                        <div class="mt-3">
                            <NButton size="small" type="primary" :loading="statusForm.processing" @click="submitStatus">
                                Update
                            </NButton>
                        </div>
                    </NCard>

                    <NCard class="glass-card">
                        <div class="text-sm font-semibold text-gray-700">Audit Log</div>
                        <ul class="mt-3 space-y-2 text-sm text-gray-600">
                            <li v-for="audit in ticket.audits" :key="audit.id">
                                <span class="font-medium text-gray-700">
                                    {{ audit.user?.name ?? 'System' }}
                                </span>
                                · {{ audit.action }}
                                · {{ new Date(audit.created_at).toLocaleString() }}
                            </li>
                        </ul>
                    </NCard>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
