<template>
    <router-link v-bind="buttonProps" class="group">
        <div
            class="flex w-fit gap-2 rounded-3xl p-3 text-left text-lg font-medium text-white transition duration-200 ease-in-out group-hover:bg-white group-hover:bg-opacity-10">
            <slot />
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
});
</script>
