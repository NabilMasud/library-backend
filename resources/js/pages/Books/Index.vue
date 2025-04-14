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
import Image from 'primevue/image';
import Skeleton from 'primevue/skeleton';

import { router } from '@inertiajs/vue3';

import { ref } from 'vue';

const showModal = ref(false);
const isEditing = ref(false)
const isLoading = ref(false)
const imageLoaded = ref(false);

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

const showDetailModal = ref(false);
const detailData = ref({
    judul: '',
    pengarang: '',
    penerbit: '',
    kategori: '',
    tahun_terbit: '',
    stok: 0,
});

const showData = (item: any) => {
    imageLoaded.value = false;
    detailData.value = {
        judul: item.judul,
        pengarang: item.pengarang,
        penerbit: item.penerbit,
        kategori: item.kategori,
        tahun_terbit: item.tahun_terbit,
        stok: item.stok,
    };
    showDetailModal.value = true;
}

const toast = useToast();

const submit = () => {
    isLoading.value = true
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
    isLoading.value = false
    router.reload({ only: ['books'] });
}

function confirmHapusBuku(id: number) {
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
                                <SplitButton label="Show" icon="pi-eye" :model="items(slotProps.data)"
                                    @click="showData(slotProps.data)" outlined severity="primary" size="small">
                                </SplitButton>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
            <!-- Modal Tambah/Edit Buku -->
            <Dialog :header="isEditing ? 'Edit Buku' : 'Tambah Buku'" v-model:visible="showModal" modal
                class="w-[30rem]">
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
                    <Button :label="isEditing ? 'Update' : 'Simpan'" @click="submit" :loading="isLoading" />
                </template>
            </Dialog>
            <!-- End Modal Tambah/Edit Buku -->

            <!-- Modal Show -->
            <!-- <Dialog header="Detail Buku" v-model:visible="showDetailModal" modal class="w-[30rem]">
                <div class="flex flex-col gap-4">
                    <div>
                        <strong>Judul:</strong> {{ detailData.judul }}
                    </div>
                    <div>
                        <strong>Pengarang:</strong> {{ detailData.pengarang }}
                    </div>
                    <div>
                        <strong>Penerbit:</strong> {{ detailData.penerbit }}
                    </div>
                    <div>
                        <strong>Kategori:</strong> {{ detailData.kategori }}
                    </div>
                    <div>
                        <strong>Tahun Terbit:</strong> {{ detailData.tahun_terbit }}
                    </div>
                    <div>
                        <strong>Stok:</strong> {{ detailData.stok }}
                    </div>
                </div>
                <template #footer>
                    <Button label="Tutup" @click="showDetailModal = false" severity="secondary" />
                </template>
            </Dialog> -->
            <!-- End Modal Show -->

            <Dialog v-model:visible="showDetailModal" modal header="Detail Buku" :style="{ width: '700px' }">
                <div class="grid grid-cols-12 gap-4">
                    <!-- Cover Buku -->
                    <div class="col-span-4">
                        <!-- <div v-if="!imageLoaded" class="h-[250px] w-full bg-gray-700 animate-pulse rounded-md"></div> -->
                        <Skeleton v-if="!imageLoaded" width="100%" height="100%"></Skeleton>
                        <Image v-show="imageLoaded"
                            src="https://img.freepik.com/free-vector/hand-drawn-w-colours-illustration_23-2149852153.jpg?t=st=1744653355~exp=1744656955~hmac=8496290d360c18be73d0fb6441b9ebbf6329d7662e60ef68a38c62c713227700&w=740"
                            alt="Cover Buku" imageStyle="border-radius: 0.5rem" preview @load="imageLoaded = true" />
                    </div>

                    <!-- Info Buku -->
                    <div class="col-span-8">
                        <h2 class="text-xl font-bold mb-2">{{ detailData.judul }}</h2>
                        <p class="mb-2 text-sm text-gray-400">Pengarang: <strong>{{ detailData.pengarang }}</strong></p>
                        <p class="mb-2 text-sm text-gray-400">Penerbit: <strong>{{ detailData.penerbit }}</strong></p>
                        <p class="mb-2 text-sm text-gray-400">Tahun Terbit: <strong>{{ detailData.tahun_terbit
                                }}</strong></p>

                        <div class="mb-2">
                            <Tag severity="info" :value="detailData.kategori" class="mr-2" />
                            <Tag severity="success" :value="detailData.stok + ' Stok'" />
                        </div>

                        <!-- Rating (jika ada) -->
                        <div class="flex items-center mt-3">
                            <Rating :modelValue="4" readonly cancel="false" class="mr-2" />
                            <span class="text-yellow-400 font-semibold text-lg">4.0</span>
                        </div>
                    </div>
                </div>

                <Divider class="my-3" />

                <!-- Deskripsi / Sinopsis -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Deskripsi:</h3>
                    <p class="text-sm text-justify text-gray-200">
                        <!-- {{ detailData.deskripsi || 'Belum ada deskripsi untuk buku ini.' }} -->
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut
                        labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                        ut
                        aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                        culpa qui officia deserunt mollit anim id est laborum.

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut
                        labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                        ut
                        aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                        culpa qui officia deserunt mollit anim id est laborum.
                    </p>
                </div>
            </Dialog>


        </div>
    </AppLayout>
</template>
