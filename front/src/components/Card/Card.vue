<template>
  <div class="mb-6 bg-[#343A40] shadow-md border border-[#4c5157] rounded-lg">
    <div class="p-5 flex">
      <img class="w-12 h-12 rounded-full" :src="creator.profilePicture" alt="avatar" />
      <div class="ml-4 flex-1">
        <CardHeader :item="props.item" />
        <CardBody :item="{ id, content, isDeleted }" />
        <CardFooter :item="{ id, commentsCount }" />
      </div>
    </div>
  </div>
</template>
<script setup>
import CardBody from "./CardBody.vue";
import CardFooter from "./CardFooter.vue";
import CardHeader from "./CardHeader.vue";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const { id, creator, content, commentsCount, created, isDeleted } = props.item;

</script>