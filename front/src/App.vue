<template>
  <component :is="layout" :class="layout === 'div' ? 'mx-auto' : 'flex'">
    <router-view @update:layout="updateLayout" :layout.sync="layout" />
  </component>
</template>

<script setup>
import { shallowRef } from "vue";

let layout = shallowRef("div");

const updateLayout = (newLayout) => {
  layout.value = newLayout;
};

</script>