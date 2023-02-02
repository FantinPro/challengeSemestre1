<template>
  <component :is="layout" :class="layout === 'div' ? 'mx-auto' : 'flex'">
    <router-view v-model:layout="layout" @update:layout="updateLayout" />
  </component>
</template>

<script setup>
import { shallowRef } from "vue";

let layout = shallowRef("div");

const updateLayout = (newLayout) => {
  layout.value = newLayout;
};

</script>