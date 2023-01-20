<template>
  <router-link v-bind="buttonProps" class="group" :active-class="'text-primary-300 font-bold'" v-slot="{ isActive }">
    <div
      class="
        flex
        text-left
        text-lg
        rounded-3xl
        px-4
        py-2
        max-md:px-3
        max-md:py-3
        w-fit
        group-hover:bg-opacity-10 group-hover:bg-white
        transition
        duration-200
        ease-in-out
        gap-2
      "
    >
      <slot :isActive="isActive" />
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
