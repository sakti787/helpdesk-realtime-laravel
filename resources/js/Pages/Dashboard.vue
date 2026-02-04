<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { NButton, NCard } from 'naive-ui';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <NCard class="glass-card">
                    <div class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
                        <div class="space-y-3">
                            <div class="text-sm uppercase tracking-[0.3em] text-slate-400">
                                Overview
                            </div>
                            <div class="text-3xl font-semibold text-slate-900">
                                Selamat datang di Helpdesk.
                            </div>
                            <p class="text-slate-600">
                                Kelola ticket, balas chat realtime, dan pantau status
                                dengan workflow yang rapi.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <Link :href="route('tickets.index')">
                                    <NButton type="primary">Lihat Tickets</NButton>
                                </Link>
                                <Link v-if="$page.props.auth.role === 'customer'" :href="route('tickets.create')">
                                    <NButton secondary>Buat Ticket</NButton>
                                </Link>
                                <Link v-if="$page.props.auth.role === 'admin'" :href="route('categories.index')">
                                    <NButton secondary>Kelola Kategori</NButton>
                                </Link>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-6">
                            <div class="text-sm text-slate-500">Quick Tips</div>
                            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                                <li>Gunakan status `WAITING_CUSTOMER` saat menunggu respon.</li>
                                <li>Agent pertama yang membalas auto-assign ticket.</li>
                                <li>Attachments maksimal 5 file per submit.</li>
                            </ul>
                        </div>
                    </div>
                </NCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
