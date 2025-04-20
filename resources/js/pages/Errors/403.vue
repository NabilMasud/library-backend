<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue';
import { router } from '@inertiajs/vue3';

// function goBack() {
//   window.history.back();
//   // Optionally, you can add a toast notification here
// }
function goBack() {
    router.visit(document.referrer || '/dashboard', { replace: true });
}
</script>

<template>
  <AppLayout>
    <div class="text-center p-10">
      <h1 class="text-3xl font-bold text-red-500">403 - Akses Ditolak</h1>
      <p class="text-zinc-600 mt-4">Kamu tidak memiliki izin untuk mengakses halaman ini.</p>
      <Button @click="goBack">Go Back</Button>
    </div>
  </AppLayout>
</template>
