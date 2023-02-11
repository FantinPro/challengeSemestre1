<template>
  <div class="border-[#4c5157] border-b">
    <div class="p-2 flex">
      <img class="w-12 h-12 rounded-full" :src="props.item.creator.profilePicture" alt="avatar" />
      <div class="ml-2 flex-1">
        <CardHeader :item="props.item" @delete-one-message-from-feed="deleteOneMessageFromFeed" />
        <CardBody :item="props.item" />
        <CardFooter :item="props.item" @update-message-from-feed="updateMessageFromFeed" />
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

const emit = defineEmits(["deleteOneMessageFromFeed", "updateMessageFromFeed"]);

const deleteOneMessageFromFeed = (message) => {
  emit("deleteOneMessageFromFeed", message);
};

const updateMessageFromFeed = (message) => {
  emit("updateMessageFromFeed", message);
};

</script>