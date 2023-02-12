<template>
  <div
    class="
      border-b border-[#4c5157]
      cursor-pointer
      hover:bg-black hover:bg-opacity-10
    "
    @click.stop="onClickEcho">
    <div class="flex flex-col p-2 pt-3">
      <div v-if="props.item.whoHasSharedFromMyFollows?.length" class="flex items-center text-gray-400 ml-[50px]">
        <ArrowUturnDownIcon class="w-4 h-4" />
        <span class="flex text-sm ml-2 gap-1">
          <div v-for="(userPseudo, index) in props.item.whoHasSharedFromMyFollows.slice(0, 3)" :key="userPseudo">
            <button
              class="text-gray-400 hover:underline"
              @click.stop="handleGoToProfile(userPseudo)"
            >
              {{ userPseudo }}
            </button>{{ index != 2 ? index < props.item.whoHasSharedFromMyFollows?.length - 1 ? ', ' : '' : '...' }}
          </div>
          shared
        </span>
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
import { useRouter } from 'vue-router';
import CardBody from './CardBody.vue';
import CardFooter from './CardFooter.vue';
import CardHeader from './CardHeader.vue';
import { ArrowUturnDownIcon } from "@heroicons/vue/24/solid/index.js";

const router = useRouter();

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

const onClickEcho = () => {
  router.push(`/profile/${props.item.creator.pseudo}/status/${props.item.id}`);
};

const handleGoToProfile = (pseudo) => {
  router.push(`/profile/${pseudo}`);
};
</script>
