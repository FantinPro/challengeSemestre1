<template>
  <div class="border-[#4c5157] border-b">
    <div class="p-2 flex">
      <img class="w-12 h-12 rounded-full" :src="creator.profilePicture" alt="avatar" />
      <div class="ml-2 flex-1">
        <CardHeader :item="props.item" />
        <CardBody :item="{ id, content, isDeleted }" />
        <CardFooter :item="{ id, commentsCount, sharesCount }" />
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from "vue-demi";
import CardBody from "./CardBody.vue";
import CardFooter from "./CardFooter.vue";
import CardHeader from "./CardHeader.vue";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const id = ref(props.item.id);
const content = ref(props.item.content);
const isDeleted = ref(props.item.isDeleted);
const commentsCount = ref(props.item.commentsCount);
const sharesCount = ref(props.item.sharesCount);
const creator = ref(props.item.creator);

</script>