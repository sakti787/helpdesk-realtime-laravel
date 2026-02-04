<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { NButton, NInput, NSelect, NCard } from 'naive-ui';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    subject: '',
    description: '',
    category_id: null,
    attachments: [],
});

const categoryOptions = props.categories.map((category) => ({
    label: category.name,
    value: category.id,
}));

const submit = () => {
    form.post(route('tickets.store'), {
        forceFormData: true,
    });
};

const handleFiles = (event) => {
    form.attachments = Array.from(event.target.files ?? []);
};
</script>

<template>
    <Head title="Buat Ticket" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Buat Ticket
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <NCard class="glass-card">
                    <div class="mb-6 rounded-2xl border border-slate-200/70 bg-white/70 p-4">
                        <div class="text-sm uppercase tracking-[0.3em] text-slate-400">
                            New Ticket
                        </div>
                        <div class="text-lg font-semibold text-slate-900">
                            Jelaskan masalahmu dengan detail agar agent cepat membantu.
                        </div>
                        <div class="text-sm text-slate-500">
                            Tambahkan attachment jika perlu, maksimal 5 file.
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Subject</label>
                            <NInput v-model:value="form.subject" placeholder="Ringkas masalah" />
                            <div v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                                {{ form.errors.subject }}
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Kategori</label>
                            <NSelect
                                v-model:value="form.category_id"
                                :options="categoryOptions"
                                placeholder="Pilih kategori"
                                clearable
                            />
                            <div v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.category_id }}
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                            <NInput
                                v-model:value="form.description"
                                type="textarea"
                                :autosize="{ minRows: 5 }"
                                placeholder="Jelaskan detail masalah"
                            />
                            <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Attachments</label>
                            <input
                                type="file"
                                multiple
                                class="mt-2 block w-full text-sm text-gray-600"
                                @change="handleFiles"
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                            />
                            <div class="mt-1 text-xs text-gray-500">
                                Maks 5 file, 5MB/file. Tipe: jpg, png, pdf, doc, docx.
                            </div>
                            <div v-if="form.errors.attachments" class="mt-1 text-sm text-red-600">
                                {{ form.errors.attachments }}
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <NButton type="primary" :loading="form.processing" @click="submit">
                                Submit Ticket
                            </NButton>
                        </div>
                    </div>
                </NCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
