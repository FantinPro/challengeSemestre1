<template>
  <div>
    Messages
  </div>
</template>
<script setup>
import { onMounted } from "vue";
import LayoutDefaultVue from "../layouts/LayoutDefault.vue";

const emit = defineEmits(["updateLayout"]);

onMounted(() => {
  emit('update:layout', LayoutDefaultVue)
});
</script>