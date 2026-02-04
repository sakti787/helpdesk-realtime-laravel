<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { NButton } from 'naive-ui';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Helpdesk" />
    <div class="min-h-screen bg-slate-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(14,116,144,0.3),transparent_50%),radial-gradient(circle_at_30%_20%,rgba(249,115,22,0.25),transparent_45%)]"></div>
        <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col px-6 py-10">
            <header class="flex items-center justify-between">
                <div class="text-lg font-semibold tracking-wide">Helpdesk</div>
                <div v-if="canLogin" class="flex gap-3">
                    <Link :href="route('login')">
                        <NButton type="success">Login</NButton>
                    </Link>
                    <Link v-if="canRegister" :href="route('register')">
                        <NButton type="success">Register</NButton>
                    </Link>
                </div>
            </header>

            <main class="flex flex-1 flex-col justify-center gap-12 py-12 lg:flex-row lg:items-center">
                <div class="max-w-xl space-y-6">
                    <div class="text-xs uppercase tracking-[0.4em] text-slate-400">
                        Realtime Support
                    </div>
                    <h1 class="text-4xl font-bold leading-tight lg:text-5xl">
                        Helpdesk realtime untuk tim CS yang gesit.
                    </h1>
                    <p class="text-lg text-slate-300">
                        Ticketing, chat realtime, dan attachment dalam satu dashboard.
                        Atur status, assignment, dan audit log dengan workflow yang jelas.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <Link v-if="canRegister" :href="route('register')">
                            <NButton type="success">Mulai Sekarang</NButton>
                        </Link>
                        <Link v-else :href="route('dashboard')">
                            <NButton type="success">Buka Dashboard</NButton>
                        </Link>
                        <Link v-if="canLogin" :href="route('login')">
                            <NButton type="success">Masuk</NButton>
                        </Link>
                    </div>
                </div>

                <div class="grid gap-4 rounded-3xl bg-white/5 p-6 backdrop-blur lg:w-[420px]">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="text-sm text-slate-300">Ticket Status</div>
                        <div class="mt-2 text-2xl font-semibold">OPEN → IN_PROGRESS</div>
                        <div class="text-sm text-slate-400">SLA dan audit log otomatis</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="text-sm text-slate-300">Realtime Chat</div>
                        <div class="mt-2 text-2xl font-semibold">Pusher + Echo</div>
                        <div class="text-sm text-slate-400">Auto-assign saat agent membalas</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="text-sm text-slate-300">Attachments</div>
                        <div class="mt-2 text-2xl font-semibold">5 file / 5MB</div>
                        <div class="text-sm text-slate-400">jpg, png, pdf, doc, docx</div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
