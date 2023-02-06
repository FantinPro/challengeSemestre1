<template>
  <div class="mb-6 bg-[#343A40] shadow-md border border-[#4c5157] rounded-lg">
    <div class="p-5 flex">
      <img class="w-12 h-12 rounded-full" src="https://i.pravatar.cc/150?img=1" alt="avatar" />
      <div class="ml-4">
        <CardHeader :item="{ id, pseudo, created }" />
        <CardBody :item="{ id, content, isDeleted }" />
        <CardFooter :item="{ id, commentsCount }" />
      </div>
    </div>
  </div>
</template>
<script setup>
import { reactive, ref } from "vue";
import CardHeader from "./CardHeader.vue";
import CardBody from "./CardBody.vue";
import CardFooter from "./CardFooter.vue";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});
const { id, creator, content, commentsCount, created, isDeleted } = props.item;
const { pseudo } = creator;

</script>