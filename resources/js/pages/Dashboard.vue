<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Card from 'primevue/card'
import Button from 'primevue/button';
import DataView from 'primevue/dataview';
import { router } from '@inertiajs/vue3';
import Chart from 'primevue/chart';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

function goToBooks() {
    router.visit('/books');
}

defineProps<{
    totalBuku: number,
    stokKosong: number,
    bukuTerbaru: { id: number; judul: string; kategori: string; stok: string }[],
    semingguTerakhir: number,
    bukuPerKategori: { kategori: string; total: number }[],
}>();
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <Card class="bg-zinc-400 dark:bg-zinc-900 shadow">
                    <template #title>
                        <div class="flex justify-between mb-4">
                            <div>
                                <span class="block text-zinc-500 dark:text-zinc-300 font-medium mb-4">Total Buku</span>
                                <div class="text-zinc-900 dark:text-zinc-400 font-bold !text-3xl">{{ totalBuku }}</div>
                            </div>
                            <div
                                class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/30 rounded-lg w-10 h-10 hover:bg-blue-200 dark:hover:bg-blue-400/50 transition duration-200">
                                <i class="pi pi-shopping-cart text-blue-500 dark:text-blue-200 !text-xl" />
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <span class="text-green-500 font-medium">{{ semingguTerakhir }} </span>
                        <span class="text-zinc-500 dark:text-zinc-300"> Terbaru minggu ini</span>
                    </template>
                </Card>
                <Card class="bg-zinc-400 dark:bg-zinc-900 shadow">
                    <template #title>
                        <div class="flex justify-between mb-4">
                            <div>
                                <span class="block text-zinc-500 dark:text-zinc-300 font-medium mb-4">Buku Stok
                                    Habis</span>
                                <div class="font-bold text-red-600 !text-3xl">{{ stokKosong }}</div>
                            </div>
                            <div
                                class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/30 rounded-lg w-10 h-10 hover:bg-blue-200 dark:hover:bg-blue-400/50 transition duration-200">
                                <i class="pi pi-shopping-cart text-blue-500 dark:text-blue-200 !text-xl" />
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <span class="text-green-500 font-medium">3 </span>
                        <span class="text-zinc-500 dark:text-zinc-300"> Buku mendapatkan stok baru</span>
                    </template>
                </Card>
                <Card class="col-span-1">
                    <template #title>
                        <div class="flex justify-between">
                            <div>Jumlah Buku per Kategori</div>
                        </div>
                    </template>
                    <template #content>
                        <Chart type="bar" :data="{
                            labels: bukuPerKategori.map(item => item.kategori),
                            datasets: [
                                {
                                    label: 'Jumlah Buku',
                                    backgroundColor: '#3B82F6',
                                    data: bukuPerKategori.map(item => item.total)
                                }
                            ]
                        }" :options="{
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }" style="height: 300px" />
                    </template>
                </Card>
                <Card class="col-span-2">
                    <template #title>
                        <div class="flex justify-between">
                            <div>Buku Terbaru</div>
                            <div>
                                <Button label="Lihat Semua" icon="pi pi-arrow-right" class="p-button-text"
                                    @click="goToBooks"></Button>
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <DataView :value="bukuTerbaru" dataKey="id">
                            <template #list="slotProps">
                                <div class="flex flex-col">
                                    <div v-for="(item, index) in slotProps.items" :key="index">
                                        <div class="flex flex-col sm:flex-row sm:items-center p-6 gap-4"
                                            :class="{ 'border-t border-zinc-200 dark:border-zinc-700': index !== 0 }">
                                            <div class="md:w-40 relative">
                                                <img class="block xl:block mx-auto rounded w-full"
                                                    :src="`https://img.freepik.com/free-vector/hand-drawn-w-colours-illustration_23-2149852153.jpg?t=st=1744653355~exp=1744656955~hmac=8496290d360c18be73d0fb6441b9ebbf6329d7662e60ef68a38c62c713227700&w=740`"
                                                    :alt="item.judul" />
                                            </div>
                                            <div
                                                class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6">
                                                <div
                                                    class="flex flex-row md:flex-col justify-between items-start gap-2">
                                                    <div>
                                                        <span
                                                            class="font-medium text-zinc-500 dark:text-zinc-400 text-sm">{{
                                                                item.kategori }}</span>
                                                        <div class="text-lg font-medium mt-2">{{ item.judul }}
                                                        </div>
                                                    </div>
                                                    <div class="bg-zinc-100 p-1" style="border-radius: 30px">
                                                        <div class="bg-zinc-200 flex items-center gap-2 justify-center py-1 px-2"
                                                            style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
                                                            <span class="text-zinc-900 font-medium text-sm">{{
                                                                4 }}</span>
                                                            <i class="pi pi-star-fill text-yellow-500"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col md:items-end gap-8">
                                                    <span class="text-xl font-semibold"><strong>Tersedia : </strong>{{
                                                        item.stok }}</span>
                                                    <div class="flex flex-row-reverse md:flex-row gap-2">
                                                        <Button icon="pi pi-heart" outlined></Button>
                                                        <Button icon="pi pi-shopping-cart" label="Buy Now"
                                                            :disabled="item.stok === 'OUTOFSTOCK'"
                                                            class="flex-auto md:flex-initial whitespace-nowrap"></Button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </DataView>
                    </template>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
