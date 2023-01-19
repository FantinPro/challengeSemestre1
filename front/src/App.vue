<template>
  <component :is="layout" :class="layout === 'div' ? 'mx-auto' : 'flex'">
    <router-view @update:layout="updateLayout" :layout.sync="layout" />
  </component>
</template>

<script setup>
import { ref } from "vue";

let layout = ref("div");

const updateLayout = (newLayout) => {
  layout.value = newLayout;
};

</script>