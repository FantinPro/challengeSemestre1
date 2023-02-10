<template>
  <router-link
    v-slot="{ isActive }"
    exact
    v-bind="buttonProps"
    class="group"
    :active-class="'text-primary-300 font-bold'">
    <div
      class="flex w-fit gap-2 rounded-3xl px-4 py-2 text-left text-lg transition duration-200 ease-in-out group-hover:bg-white group-hover:bg-opacity-10 max-md:px-3 max-md:py-3">
      <slot :is-active="isActive" />
    </div>
  </router-link>
</template>
<script setup>
const buttonProps = defineProps({
  type: {
    type: String,
    default: 'button',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  to: {
    type: String,
    default: '',
  },
  exact: {
    type: Boolean,
    default: false,
  },
});
</script>
