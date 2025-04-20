<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Toast from 'primevue/toast';

const toast = useToast();

const showModal = ref(false);
const isEditing = ref(false);
const isLoading = ref(false);

const form = ref({
    id: null,
    name: '',
});

function tambahRole() {
    isEditing.value = false;
    form.value = {
        id: null,
        name: '',
    };
    showModal.value = true;
}

function editRole(role: any) {
    isEditing.value = true;
    form.value = {
        id: role.id,
        name: role.name,
    };
    showModal.value = true;
}

function submit() {
    isLoading.value = true;
    if (isEditing.value) {
        toast.add({
            severity: 'info',
            summary: 'Loading',
            detail: 'Sedang mengupdate role...',
            life: 3000,
        });
        router.put(`/roles/${form.value.id}`, form.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Role diupdate', life: 3000 });
                showModal.value = false;
            },
            onError: (errors) => {
                const allErrors = Object.values(errors).flat();
                allErrors.forEach((error: string) => {
                    toast.add({
                        severity: 'error',
                        summary: 'Gagal',
                        detail: error,
                        life: 3000,
                    });
                });
                console.log(errors);
            },
        });
    } else {
        toast.add({
            severity: 'info',
            summary: 'Loading',
            detail: 'Sedang menambahkan role...',
            life: 3000,
        });
        router.post('/roles', form.value, {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Berhasil',
                    detail: 'Role berhasil ditambahkan',
                    life: 3000,
                });
                showModal.value = false;
                form.value = {
                    id: null,
                    name: '',
                };
            },
            onError: (errors) => {
                const allErrors = Object.values(errors).flat();
                allErrors.forEach((error: string) => {
                    toast.add({
                        severity: 'error',
                        summary: 'Gagal',
                        detail: error,
                        life: 3000,
                    });
                });
                console.log(errors);
            },
        });
    }
    isLoading.value = false;
}

function confirmHapusRole(id: number) {
    if (confirm('Yakin ingin menghapus role ini?')) {
        router.delete(`/roles/${id}`, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Role dihapus', life: 3000 });
            },
            onError: (errors) => {
                const allErrors = Object.values(errors).flat();
                allErrors.forEach((error: string) => {
                    toast.add({
                        severity: 'error',
                        summary: 'Gagal',
                        detail: error,
                        life: 3000,
                    });
                });
                console.log(errors);
            },
        });
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/roles',
    },
];

defineProps({
    roles: Array,
});
</script>

<template>
    <Head title="Daftar Role" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toast />
        <div class="px-4 py-6">
            <Heading title="Daftar Role" description="Berikut adalah daftar role yang tersedia." />

            <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
                <Button label="Tambah Role" icon="pi pi-plus" @click="tambahRole" variant="outlined" class="w-48" />
                <div
                    class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                    <DataTable :value="roles" removableSort paginator :rows="10" responsiveLayout="scroll"
                        :rowsPerPageOptions="[5, 10, 20, 50]">
                        <Column field="name" sortable header="Nama Role"></Column>
                        <Column header="Aksi">
                            <template #body="slotProps">
                                <Button label="Edit" icon="pi pi-pencil" @click="editRole(slotProps.data)"
                                    class="p-button-sm p-button-warning mr-2" />
                                <Button label="Hapus" icon="pi pi-trash" @click="confirmHapusRole(slotProps.data.id)"
                                    class="p-button-sm p-button-danger" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>

            <!-- Modal Tambah/Edit Role -->
            <Dialog :header="isEditing ? 'Edit Role' : 'Tambah Role'" v-model:visible="showModal" modal
                class="w-[30rem]">
                <div class="flex flex-col gap-4">
                    <div>
                        <label>Nama Role</label>
                        <InputText v-model="form.name" class="w-full" />
                    </div>
                </div>
                <template #footer>
                    <Button label="Batal" severity="secondary" @click="showModal = false" />
                    <Button :label="isEditing ? 'Update' : 'Simpan'" @click="submit" :loading="isLoading" />
                </template>
            </Dialog>
            <!-- End Modal Tambah/Edit Role -->
        </div>
    </AppLayout>
</template>
