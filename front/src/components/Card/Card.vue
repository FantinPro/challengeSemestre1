<template>
  <div class="border-b border-[#4c5157]">
    <div class="flex flex-col p-2">
      <div v-if="props.item.parent" class="flex flex-col">
        <div class="flex items-center">
          <img
            class="ml-3 h-6 w-6 rounded-full"
            :src="props.item.parent.creator.profilePicture"
            alt="avatar" />
          <div class="text-xs text-gray-200 ml-5">from @{{ props.item.parent.creator.pseudo }}</div>
        </div>

        <div
          class="mr-auto ml-[21px] h-full min-h-[20px] border border-slate-500"></div>
      </div>
      <div class="flex">
        <img
          class="h-12 w-12 rounded-full"
          :src="props.item.creator.profilePicture"
          alt="avatar" />
        <div class="ml-2 flex-1">
          <CardHeader
            :item="props.item"
            @delete-one-message-from-feed="deleteOneMessageFromFeed" />
          <CardBody :item="props.item" />
          <CardFooter
            :item="props.item"
            @upsert-message-from-feed="upsertMessageFromFeed" />
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import CardBody from './CardBody.vue';
import CardFooter from './CardFooter.vue';
import CardHeader from './CardHeader.vue';

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['deleteOneMessageFromFeed', 'upsertMessageFromFeed']);

const deleteOneMessageFromFeed = (message) => {
  emit('deleteOneMessageFromFeed', message);
};

const upsertMessageFromFeed = (message) => {
  emit('upsertMessageFromFeed', message);
};
</script>
