<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';

import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import DatePicker from 'primevue/datepicker';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

import { router } from '@inertiajs/vue3';

import { ref } from 'vue';

const showModal = ref(false);

const form = ref({
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun_terbit_bef: new Date(),
    tahun_terbit: '',
    kategori: '',
    stok: 0,
})

const toast = useToast();

const submit = () => {
    toast.add({
        severity: 'info',
        summary: 'Loading',
        detail: 'Sedang menambahkan buku...',
        life: 3000,
    })
    form.value.tahun_terbit = form.value.tahun_terbit_bef.getFullYear().toString(); // Assign the year string to the new property
    router.post('/books', form.value, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Berhasil',
                detail: 'Buku berhasil ditambahkan',
                life: 3000,
            })
            showModal.value = false
            form.value = {
                judul: '',
                pengarang: '',
                penerbit: '',
                tahun_terbit_bef: new Date(),
                tahun_terbit: '',
                kategori: '',
                stok: 0,
            }
        },
        onError: (errors) => {
            // toast.add({
            //     severity: 'error',
            //     summary: 'Gagal',
            //     detail: errors,
            //     life: 3000,
            // })
            const allErrors = Object.values(errors).flat();
            allErrors.forEach((error: string) => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: error,
                    life: 3000,
                })
            });
            console.log(errors)
        },
    })
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Books',
        href: '/books',
    },
];


defineProps({
    books: Array
})
</script>

<template>

    <Head title="Daftar Buku" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toast />
        <div class="px-4 py-6">
            <Heading title="Daftar Buku" description="Berikut adalah daftar buku yang tersedia." />

            <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
                <Button label="Tambah Buku" icon="pi pi-plus" @click="showModal = true" />
                <div
                    class="relative  flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                    <DataTable :value="books" removableSort paginator :rows="10" responsiveLayout="scroll"
                        :rowsPerPageOptions="[5, 10, 20, 50]">
                        <Column field="judul" sortable header="Judul"></Column>
                        <Column field="pengarang" header="Pengarang"></Column>
                        <Column field="penerbit" header="Penerbit"></Column>
                        <Column field="tahun_terbit" header="Tahun"></Column>
                        <Column field="kategori" header="Kategori"></Column>
                        <Column field="stok" header="Stok"></Column>
                    </DataTable>
                </div>
            </div>
            <!-- Modal Tambah Buku -->
            <Dialog header="Tambah Buku" v-model:visible="showModal" modal class="w-[30rem]">
                <div class="flex flex-col gap-4">
                    <div>
                        <label>Judul</label>
                        <InputText v-model="form.judul" class="w-full" />
                    </div>
                    <div>
                        <label>Penerbit</label>
                        <InputText v-model="form.penerbit" class="w-full" />
                    </div>
                    <div>
                        <label>Pengarang</label>
                        <InputText v-model="form.pengarang" class="w-full" />
                    </div>
                    <div>
                        <label>Kategori</label>
                        <InputText v-model="form.kategori" class="w-full" />
                    </div>
                    <div>
                        <label>Tahun Terbit</label>
                        <!-- <InputNumber v-model="form.tahun_terbit" class="w-full" /> -->
                        <DatePicker v-model="form.tahun_terbit_bef" view="year" dateFormat="yy" class="w-full" />
                    </div>
                    <div>
                        <label>Stok</label>
                        <InputNumber v-model="form.stok" class="w-full" />
                    </div>
                </div>
                <template #footer>
                    <Button label="Batal" severity="secondary" @click="showModal = false" />
                    <Button label="Simpan" @click="submit" />
                </template>
            </Dialog>

        </div>
    </AppLayout>
</template>
