<template>
  <component :is="layout" :class="classes">
    <router-view :layout="layout" @update:layout="updateLayout" @update:classes="updateClasses"/>
  </component>
</template>

<script setup>
import { shallowRef } from "vue";

let layout = shallowRef("div");
let classes = layout.value === "div" ? shallowRef("mx-auto") : shallowRef("flex");

const updateLayout = (newLayout) => {
  layout.value = newLayout;
};

const updateClasses = (newClass) => {
  classes.value = newClass;
};

</script>