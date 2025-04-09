<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';


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
  <AppLayout :breadcrumbs="breadcrumbs" >
    <div class="px-4 py-6">
    <Heading title="Daftar Buku" description="Berikut adalah daftar buku yang tersedia." />

    <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative  flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                  <DataTable :value="books" removableSort paginator :rows="10" responsiveLayout="scroll" :rowsPerPageOptions="[5, 10, 20, 50]">
                    <Column field="judul" sortable header="Judul"></Column>
                    <Column field="pengarang" header="Pengarang"></Column>
                    <Column field="penerbit" header="Penerbit"></Column>
                    <Column field="tahun_terbit" header="Tahun"></Column>
                    <Column field="kategori" header="Kategori"></Column>
                    <Column field="stok" header="Stok"></Column>
                  </DataTable>
            </div>
        </div>

    </div>
  </AppLayout>
</template>
