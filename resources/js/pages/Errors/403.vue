<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { router, usePage } from '@inertiajs/vue3';

// function goBack() {
//   window.history.back();
//   // Optionally, you can add a toast notification here
// }

console.log("halaman sebelumnya", document.referrer);
function goBack() {
    router.visit((page.props.url as string) || '/dashboard', { replace: true });
}

// Ambil props dari Inertia
const page = usePage();
const message = page.props.message || 'Akses ditolak.';
</script>

<template>
  <AppLayout>
    <div class="text-center p-10">
      <h1 class="text-3xl font-bold text-red-500">403 - Akses Ditolak</h1>
      <p class="text-zinc-600 mt-4">{{ message }}</p>
      <Button @click="goBack" class="m-4">Go Back</Button>
    </div>
  </AppLayout>
</template>
