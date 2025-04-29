<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';
import Tree from 'primevue/tree';
import { router } from '@inertiajs/vue3';
import { reactive, ref, computed } from 'vue';

// Helper function to convert an object to PrimeVue Tree node format
function objectToTreeNodes(obj: Record<string, any>): any[] {
    return Object.entries(obj).map(([key, value]) => {
        if (typeof value === 'object' && value !== null) {
            return {
                key,
                label: key,
                children: objectToTreeNodes(value)
            };
        } else {
            return {
                key,
                label: `${value}`
            };
        }
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activity Logs',
        href: '/activity-logs',
    },
];

const props = defineProps<{
    logs: {
        id: number;
        log_name: string;
        description: string;
        subject: any;
        causer: any;
        properties: Record<string, any>;
        created_at: string;
    }[];
    current_page: number;
    total: number;
    per_page: number;
}>();

// Cache to store data for two pages
const cache = reactive(new Map<string, any>());
const currentPage = ref(props.current_page || 1);
const loading = ref(false);

// Initialize cache with the first page data
cache.set(String(currentPage.value), props.logs);

// Maximum number of pages to keep in cache
const maxCachePages = 2;

// Handle page change
const onPageChange = (event: any) => {
    const newPage = (event.page ?? 0) + 1;

    // If the data for the new page is already in cache, just update the current page
    if (cache.has(String(newPage))) {
        currentPage.value = newPage;
        return;
    }

    // Otherwise, fetch data for the new page
    loading.value = true;
    router.get(route('activity-logs.index', { page: newPage }), {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['logs', 'current_page', 'total', 'per_page'], // Fetch only necessary data
        onSuccess: (page) => {
            // Add new page data to cache
            cache.set(String(newPage), page.props.logs);
            currentPage.value = newPage;

            // Remove the oldest page from cache if cache size exceeds the limit
            if (cache.size > maxCachePages) {
                const oldestPage = Array.from(cache.keys())[0];
                cache.delete(oldestPage);
            }
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};

// Computed property to get the data for the current page
const activitiesData = computed(() => {
    return cache.get(String(currentPage.value)) || [];
});
</script>

<template>
    <Head title="Log Aktivitas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <Heading title="Log Aktivitas" description="Berikut adalah daftar log aktivitas sistem." />

            <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
                <div
                    class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                    <DataTable
                        :value="activitiesData"
                        removableSort
                        paginator
                        :rows="5"
                        responsiveLayout="scroll"
                        :lazy="true"
                        :totalRecords="props.total"
                        :loading="loading"
                        :first="(currentPage - 1) * 5"
                        @page="onPageChange"
                    >
                        <Column field="log_name" header="Log Name" sortable></Column>
                        <Column field="description" header="Description" sortable></Column>
                        <Column field="subject.name" header="Subject"></Column>
                        <Column field="causer.name" header="Causer"></Column>
                        <Column header="Properties">
                            <template #body="slotProps">
                                <Tree :value="objectToTreeNodes(slotProps.data.properties)" />
                            </template>
                        </Column>
                        <Column field="created_at" header="Date" sortable>
                            <template #body="slotProps">
                                {{ new Date(slotProps.data.created_at).toLocaleDateString() }}
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
