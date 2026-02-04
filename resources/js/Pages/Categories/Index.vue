<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { NButton, NCard, NInput } from 'naive-ui';

const props = defineProps({
    categories: Array,
});

const createForm = useForm({
    name: '',
    sla_response_minutes: null,
    sla_resolution_minutes: null,
});

const editingId = ref(null);
const editForm = useForm({
    name: '',
    sla_response_minutes: null,
    sla_resolution_minutes: null,
});

const startEdit = (category) => {
    editingId.value = category.id;
    editForm.name = category.name;
    editForm.sla_response_minutes = category.sla_response_minutes;
    editForm.sla_resolution_minutes = category.sla_resolution_minutes;
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
};

const submitCreate = () => {
    createForm.post(route('categories.store'), {
        onSuccess: () => createForm.reset(),
    });
};

const submitUpdate = (categoryId) => {
    editForm.patch(route('categories.update', categoryId), {
        onSuccess: () => cancelEdit(),
    });
};

const submitDelete = (categoryId) => {
    if (confirm('Hapus kategori ini?')) {
        editForm.delete(route('categories.destroy', categoryId));
    }
};

const categoryRows = computed(() => props.categories ?? []);
</script>

<template>
    <Head title="Kategori" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Kategori
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <NCard class="glass-card">
                    <div class="text-sm font-semibold text-gray-700">Tambah Kategori</div>
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="text-xs text-gray-500">Nama</label>
                            <NInput v-model:value="createForm.name" placeholder="Nama kategori" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">SLA Response (menit)</label>
                            <NInput v-model:value="createForm.sla_response_minutes" placeholder="Opsional" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">SLA Resolve (menit)</label>
                            <NInput v-model:value="createForm.sla_resolution_minutes" placeholder="Opsional" />
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <NButton type="primary" :loading="createForm.processing" @click="submitCreate">
                            Simpan
                        </NButton>
                    </div>
                </NCard>

                <NCard class="glass-card">
                    <div class="text-sm font-semibold text-gray-700">Daftar Kategori</div>
                    <div class="mt-4 space-y-3">
                        <div
                            v-for="category in categoryRows"
                            :key="category.id"
                            class="rounded-lg border border-gray-200 p-4"
                        >
                            <div v-if="editingId !== category.id" class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">{{ category.name }}</div>
                                    <div class="text-sm text-gray-500">
                                        SLA Response: {{ category.sla_response_minutes ?? '-' }} menit ·
                                        SLA Resolve: {{ category.sla_resolution_minutes ?? '-' }} menit
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <NButton size="small" @click="startEdit(category)">Edit</NButton>
                                    <NButton size="small" type="error" @click="submitDelete(category.id)">Hapus</NButton>
                                </div>
                            </div>
                            <div v-else class="grid gap-3 md:grid-cols-3">
                                <NInput v-model:value="editForm.name" placeholder="Nama kategori" />
                                <NInput v-model:value="editForm.sla_response_minutes" placeholder="SLA response" />
                                <NInput v-model:value="editForm.sla_resolution_minutes" placeholder="SLA resolve" />
                                <div class="md:col-span-3 flex gap-2 justify-end">
                                    <NButton size="small" @click="cancelEdit">Batal</NButton>
                                    <NButton size="small" type="primary" :loading="editForm.processing" @click="submitUpdate(category.id)">
                                        Update
                                    </NButton>
                                </div>
                            </div>
                        </div>
                        <div v-if="categoryRows.length === 0" class="text-sm text-gray-500">
                            Belum ada kategori.
                        </div>
                    </div>
                </NCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
