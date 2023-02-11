<template>
  <div>
    Met le calendar ici
    <button
      type="button"
      class="inline-flex justify-center gap-2 rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
      @click="openDialog">
      <span>Create ad</span>
    </button>

    <DialogCreateAd :is-open="isCreateAdDialogOpen" :start-date="startDate" :end-date="endDate" @close="closeDialog" />
  </div>
</template>
<script setup>
import { ref } from 'vue';
import DialogCreateAd from '../components/Dialog/DialogCreateAd.vue';

const isCreateAdDialogOpen = ref(false);

const startDate = ref(new Date());
const endDate = ref(new Date());

const openDialog = () => {
  isCreateAdDialogOpen.value = true;
};

const closeDialog = () => {
  isCreateAdDialogOpen.value = false;
};

</script>
