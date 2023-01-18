<template>
  <div class="mb-6 bg-[#343A40] shadow-md border-2 border-[#4c5157] rounded-lg">
    <div class="p-5 flex">
      <img class="w-12 h-12 rounded-full" :src="avatar" alt="avatar" />
      <div class="ml-4">
        <CardHeader :item="{ id, avatar, username, time }" />
        <CardBody :item="{ id, text }" />
        <CardFooter :item="{ id, comments, shares }" />
      </div>
    </div>
  </div>
</template>
<script setup>
import { reactive } from "vue";
import CardHeader from "./CardHeader.vue";
import CardBody from "./CardBody.vue";
import CardFooter from "./CardFooter.vue";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const { id, username, avatar, text, time, comments, shares } = props.item;
</script>