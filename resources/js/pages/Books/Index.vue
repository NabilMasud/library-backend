<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';

import Dialog from 'primevue/dialog';
import SplitButton from 'primevue/splitbutton';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import DatePicker from 'primevue/datepicker';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

import { router } from '@inertiajs/vue3';

import { ref } from 'vue';

const showModal = ref(false);

const isEditing = ref(false)

const form = ref({
    id: null,
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun_terbit_bef: new Date(),
    tahun_terbit: '',
    kategori: '',
    stok: 0,
})

function tambahBuku() {
    isEditing.value = false
    form.value = {
        id: null,
        judul: '',
        pengarang: '',
        penerbit: '',
        tahun_terbit_bef: new Date(),
        tahun_terbit: '',
        kategori: '',
        stok: 0,
    }
    showModal.value = true
}

function editBuku(buku: any) {
    isEditing.value = true
    console.log(buku)
    // form.value = { ...buku }
    form.value = {
        id: buku.id,
        judul: buku.judul,
        pengarang: buku.pengarang,
        penerbit: buku.penerbit,
        tahun_terbit_bef: new Date(buku.tahun_terbit),
        tahun_terbit: '',
        kategori: buku.kategori,
        stok: buku.stok,
    }
    showModal.value = true
}

const items = (rowData: any) => [
    {
        label: 'Edit',
        icon: 'pi pi-pencil',
        severity: 'warn',
        command: () => {
            editBuku(rowData);
        },
    },
    {
        label: 'Hapus',
        icon: 'pi pi-trash',
        severity: 'danger',
        command: () => {
            confirmHapusBuku(rowData.id);
        },
    },
];

const showData = (item: any) => {
    console.log(item);
}

const toast = useToast();

const submit = () => {
    if (isEditing.value) {
        form.value.tahun_terbit = form.value.tahun_terbit_bef.getFullYear().toString();
        toast.add({
            severity: 'info',
            summary: 'Loading',
            detail: 'Sedang mengupdate buku...',
            life: 3000,
        })
        router.put(`/books/${form.value.id}`, form.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Buku diupdate', life: 3000 })
                showModal.value = false
            },
            onError: (errors) => {
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
    } else {
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
                    id: null,
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
    router.reload({only: ['books']});
}

function confirmHapusBuku(id : number) {
  if (confirm('Yakin ingin menghapus buku ini?')) {
    router.delete(`/books/${id}`, {
      onSuccess: () => {
        toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Buku dihapus', life: 3000 })
      },
        onError: (errors) => {
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
                <Button label="Tambah Buku" icon="pi pi-plus" @click="tambahBuku" variant="outlined" class="w-48" />
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
                        <Column header="Aksi">
                            <!-- <template #body="slotProps"> -->
                                <!-- <Button icon="pi pi-pencil" severity="info" @click="editBuku(slotProps.data)" />
                                <Button icon="pi pi-trash" severity="danger" class="ml-2"
                                @click="confirmHapusBuku(slotProps.data.id)" /> -->
                            <!-- </template> -->
                             <template #body="slotProps">
                                <SplitButton label="Show" icon="pi-eye" :model="items(slotProps.data)" @click="showData(slotProps.data)" outlined severity="primary" size="small"></SplitButton>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
            <!-- Modal Tambah Buku -->
            <Dialog :header="isEditing ? 'Edit Buku' : 'Tambah Buku'" v-model:visible="showModal" modal class="w-[30rem]">
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
                    <Button :label="isEditing ? 'Update' : 'Simpan'" @click="submit" />
                </template>
            </Dialog>

        </div>
    </AppLayout>
</template>
