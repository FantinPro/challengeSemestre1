<template>
  <router-link v-bind="buttonProps" class="group">
    <div
      class="
        flex
        text-left text-white text-lg
        font-medium
        rounded-3xl
        px-4
        py-2
        w-fit
        group-hover:bg-opacity-10 group-hover:bg-white
        transition
        duration-200
        ease-in-out
        gap-2
      "
    >
      <slot />
    </div>
  </router-link>
</template>
<script setup>
const buttonProps = defineProps({
  type: {
    type: String,
    default: "button",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  to: {
    type: String,
    default: "",
  },
});
</script>
