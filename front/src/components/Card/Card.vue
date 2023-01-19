<template>
  <div class="mb-6 bg-[#343A40] shadow-md border border-[#4c5157] rounded-lg">
    <div class="p-5 flex">
      <img class="w-12 h-12 rounded-full" src="https://i.pravatar.cc/150?img=1" alt="avatar" />
      <div class="ml-4">
        <CardHeader :item="{ id, pseudo }" />
        <CardBody :item="{ id, content }" />
        <CardFooter :item="{ id, commentsCount }" />
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
const { id, creator, content, commentsCount } = props.item;
const { pseudo } = creator;
</script>