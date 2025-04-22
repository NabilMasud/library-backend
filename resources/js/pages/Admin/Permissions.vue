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
import MultiSelect from 'primevue/multiselect';
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

function tambahPermission() {
    isEditing.value = false;
    form.value = {
        id: null,
        name: '',
    };
    showModal.value = true;
}

function editPermission(permission: any) {
    isEditing.value = true;
    form.value = {
        id: permission.id,
        name: permission.name,
    };
    showModal.value = true;
}

function submit() {
    isLoading.value = true;
    if (isEditing.value) {
        toast.add({
            severity: 'info',
            summary: 'Loading',
            detail: 'Sedang mengupdate permission...',
            life: 3000,
        });
        router.put(`/permissions/${form.value.id}`, form.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Permission diupdate', life: 3000 });
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
            detail: 'Sedang menambahkan permission...',
            life: 3000,
        });
        router.post('/permissions', form.value, {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Berhasil',
                    detail: 'Permission berhasil ditambahkan',
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

function confirmHapusPermission(id: number) {
    if (confirm('Yakin ingin menghapus permission ini?')) {
        router.delete(`/permissions/${id}`, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Permission dihapus', life: 3000 });
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
        title: 'Permissions',
        href: '/permissions',
    },
];

defineProps<{
    permissions: any[],
    roles: { id: number; name: string }[],
    users: { id: number; name: string }[],
}>();

function submitUsers(permissionId: number, users: string[]) {
    router.put(`/permissions/${permissionId}/users`, { users }, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Berhasil',
                detail: 'User berhasil diperbarui untuk permission ini.',
                life: 3000,
            });
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
        },
    });
}

function submitRoles(permissionId: number, roles: string[]) {
    router.put(`/permissions/${permissionId}/roles`, { roles }, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Berhasil',
                detail: 'Role berhasil diperbarui untuk permission ini.',
                life: 3000,
            });
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
        },
    });
}
</script>

<template>
    <Head title="Daftar Permission" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toast />
        <div class="px-4 py-6">
            <Heading title="Daftar Permission" description="Berikut adalah daftar permission yang tersedia." />

            <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
                <Button label="Tambah Permission" icon="pi pi-plus" @click="tambahPermission" variant="outlined" class="w-52" />
                <div
                    class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                    <DataTable :value="permissions" removableSort paginator :rows="10" responsiveLayout="scroll"
                        :rowsPerPageOptions="[5, 10, 20, 50]">
                        <Column field="name" sortable header="Nama Permission"></Column>
                        <Column header="User">
                            <template #body="slotProps">
                                <MultiSelect
                                    :options="users"
                                    optionLabel="name"
                                    optionValue="name"
                                    :modelValue="slotProps.data.users.map((user: any) => user.name)"
                                    placeholder="Pilih User"
                                    display="chip"
                                    class="w-50"
                                    @change="(e) => submitUsers(slotProps.data.id, e.value)"
                                >
                                    <template #option="slotOption">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-sm rounded text-white',
                                                'bg-purple-500'
                                            ]"
                                        >
                                            {{ slotOption.option.name }}
                                        </span>
                                    </template>
                                    <template #chip="slotChip">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-sm rounded text-white mr-1',
                                                'bg-purple-500'
                                            ]"
                                        >
                                            {{ slotChip.value }}
                                        </span>
                                    </template>
                                </MultiSelect>
                            </template>
                        </Column>
                        <Column header="Role">
                            <template #body="slotProps">
                                <MultiSelect
                                    :options="roles"
                                    optionLabel="name"
                                    optionValue="name"
                                    :modelValue="slotProps.data.roles.map((role: any) => role.name)"
                                    placeholder="Pilih Role"
                                    display="chip"
                                    class="w-50"
                                    @change="(e) => submitRoles(slotProps.data.id, e.value)"
                                >
                                    <template #option="slotOption">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-sm rounded text-white',
                                                'bg-blue-500'
                                            ]"
                                        >
                                            {{ slotOption.option.name }}
                                        </span>
                                    </template>
                                    <template #chip="slotChip">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-sm rounded text-white mr-1',
                                                'bg-blue-500'
                                            ]"
                                        >
                                            {{ slotChip.value }}
                                        </span>
                                    </template>
                                </MultiSelect>
                            </template>
                        </Column>
                        <Column header="Aksi">
                            <template #body="slotProps">
                                <Button label="Edit" icon="pi pi-pencil" @click="editPermission(slotProps.data)"
                                    class="p-button-sm p-button-warning mr-2" />
                                <Button label="Hapus" icon="pi pi-trash" @click="confirmHapusPermission(slotProps.data.id)"
                                    class="p-button-sm p-button-danger" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>

            <!-- Modal Tambah/Edit Permission -->
            <Dialog :header="isEditing ? 'Edit Permission' : 'Tambah Permission'" v-model:visible="showModal" modal
                class="w-[30rem]">
                <div class="flex flex-col gap-4">
                    <div>
                        <label>Nama Permission</label>
                        <InputText v-model="form.name" class="w-full" />
                    </div>
                </div>
                <template #footer>
                    <Button label="Batal" severity="secondary" @click="showModal = false" />
                    <Button :label="isEditing ? 'Update' : 'Simpan'" @click="submit" :loading="isLoading" />
                </template>
            </Dialog>
            <!-- End Modal Tambah/Edit Permission -->
        </div>
    </AppLayout>
</template>
