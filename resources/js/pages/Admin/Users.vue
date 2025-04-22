<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';
import MultiSelect from 'primevue/multiselect';
import { router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import useRole from '@/composables/useRole';

const toast = useToast();
const { hasRole } = useRole();
// const showModal = ref(false);
// const selectedUser = ref<any>(null);
// const selectedRoles = ref<string[]>([]);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

defineProps<{
    users: any[],
    roles: { name: string }[],
    permissions: { name: string }[],
}>();

// function editRoles(user: any) {
//     selectedUser.value = user;
//     selectedRoles.value = user.roles.map((role: any) => role.name);
//     showModal.value = true;
// }

function submitRoles(id : Number, roles: string[]) {
    // selectedRoles.value = roles;
    // if (!selectedUser.value) return;

    if (!hasRole('Admin') && roles.includes('Admin')) {
        toast.add({
            severity: 'error',
            summary: 'Gagal',
            detail: 'Petugas tidak dapat memberikan peran Admin kepada pengguna lain.',
            life: 3000,
        });
        router.reload({ only: ['users', 'roles', 'permissions'] });
        return;

    }

    router.put(`/users/${id}/roles`, { roles }, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Berhasil',
                detail: 'Peran pengguna berhasil diperbarui.',
                life: 3000,
            });
            // showModal.value = false;
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

function submitPermissions(id: Number, permissions: string[]) {
    router.put(`/users/${id}/permissions`, { permissions }, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Berhasil',
                detail: 'Izin pengguna berhasil diperbarui.',
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
    <Head title="Daftar Pengguna" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toast position="top-right" />
        <div class="px-4 py-6">
            <Heading title="Daftar Pengguna" description="Berikut adalah daftar pengguna, peran, dan izinnya." />

            <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
                <div
                    class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                    <DataTable :value="users" removableSort paginator :rows="10" responsiveLayout="scroll"
                        :rowsPerPageOptions="[5, 10, 20, 50]">
                        <Column field="name" sortable header="Nama"></Column>
                        <Column field="email" header="Email"></Column>

                        <!-- Kolom Manajemen Role -->
                        <Column header="Peran">
                            <template #body="slotProps">
                                <MultiSelect
                                    :options="roles"
                                    optionLabel="name"
                                    optionValue="name"
                                    :modelValue="slotProps.data.roles.map((role: any) => role.name)"
                                    placeholder="Pilih Peran"
                                    display="chip"
                                    class="w-50"
                                    :disabled="hasRole('Petugas') && slotProps.data.roles.some((role: any) => role.name === 'Admin')"
                                    @change="(e) => submitRoles(slotProps.data.id, e.value)"
                                >
                                    <template #option="slotOption">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-sm rounded text-white',
                                                slotOption.option.name === 'Admin' ? 'bg-green-500' : 'bg-blue-500',
                                                (!hasRole('Admin') && slotOption.option.name === 'Admin') ? 'opacity-50 pointer-events-none' : ''
                                            ]"
                                        >
                                            {{ slotOption.option.name }}
                                            <span v-if="!hasRole('Admin') && slotOption.option.name === 'Admin'" class="ml-1 text-xs">(Terkunci)</span>
                                        </span>
                                    </template>
                                    <template #chip="slotChip">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-sm rounded text-white mr-1',
                                                slotChip.value === 'Admin' ? 'bg-green-500' : 'bg-blue-500'
                                            ]"
                                        >
                                            {{ slotChip.value }}
                                        </span>
                                    </template>
                                </MultiSelect>
                            </template>
                        </Column>

                        <!-- Kolom Manajemen Permission -->
                        <Column header="Izin">
                            <template #body="slotProps">
                                <MultiSelect
                                    :options="permissions"
                                    optionLabel="name"
                                    optionValue="name"
                                    :modelValue="slotProps.data.permissions.map((permission: any) => permission.name)"
                                    placeholder="Pilih Izin"
                                    display="chip"
                                    :diasbled="!hasRole('Admin')"
                                    class="w-50"
                                    @change="(e) => submitPermissions(slotProps.data.id, e.value)"
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
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
