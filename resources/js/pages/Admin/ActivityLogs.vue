<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';
import Tree from 'primevue/tree';

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

defineProps<{
    logs: {
        id: number;
        log_name: string;
        description: string;
        subject_type: string;
        subject_id: number | null;
        causer_type: string | null;
        causer_id: number | null;
        properties: Record<string, any>;
        created_at: string;
    }[];
}>();
</script>
<template>
    <Head title="Log Aktivitas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <Heading title="Log Aktivitas" description="Berikut adalah daftar log aktivitas sistem." />

            <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
                <div
                    class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                    <DataTable :value="logs" removableSort paginator :rows="10" responsiveLayout="scroll"
                        :rowsPerPageOptions="[5, 10, 20, 50]">
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
