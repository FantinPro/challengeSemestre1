<template>
  <div
    class="
      border-b border-[#4c5157]
      cursor-pointer
      hover:bg-black hover:bg-opacity-10
    "
    @click.stop="onClickEcho">
    <div class="flex flex-col p-2 pt-3">
      <div
        v-if="props.item.whoHasSharedFromMyFollows?.length"
        class="flex items-center text-gray-400 ml-[50px]">
        <ArrowUturnDownIcon class="w-4 h-4" />
        <span class="flex text-sm ml-2 gap-1">
          <div
            v-for="(
              userPseudo, index
            ) in props.item.whoHasSharedFromMyFollows.slice(0, 3)"
            :key="userPseudo">
            <button
              class="text-gray-400 hover:underline"
              @click.stop="handleGoToProfile(userPseudo)">
              {{ userPseudo }}</button
            >{{
              index != 2
                ? index < props.item.whoHasSharedFromMyFollows?.length - 1
                  ? ', '
                  : ''
                : '...'
            }}
          </div>
          shared this
        </span>
      </div>
      <div
        v-if="props.item.parent"
        class="flex items-center text-gray-400 ml-[50px]">
        <ChatBubbleLeftIcon class="w-4 h-4" />
        <span class="flex text-sm ml-2 gap-1">
          {{ props.item.creator.pseudo }} replied to
          {{ props.item.parent.creator.pseudo }}
        </span>
      </div>
      <div v-if="props.item.parent" class="mb-4 flex">
        <div class="flex min-w-max flex-col">
          <img
            class="h-12 w-12 rounded-md object-cover"
            :src="props.item.parent.creator.profilePicture"
            alt="avatar" />
          <div
            class="
              mt-1
              mr-auto
              ml-[22px]
              h-full
              min-h-full
              border border-slate-500
              bg-slate-500
            " />
        </div>
        <div class="ml-2 flex w-full flex-col">
          <CardHeader
            :item="props.item.parent"
            @delete-one-message-from-feed="
              onClickProfile(props.item.parent.creator.pseudo)
            " />
          <div v-if="props.item.parent.isDeleted" class="text-white">
            <span class="font-bold text-orange-400">[deleted]</span>
            <span class="ml-2 text-gray-400">{{ props.item.parent.content }}</span>
          </div>
          <span v-else class="text-base font-medium">
            {{ props.item.parent.content }}
          </span>
        </div>
      </div>
      <div class="flex">
        <img
          class="h-12 w-12 rounded-full object-cover"
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
import { ArrowUturnDownIcon, ChatBubbleLeftIcon } from '@heroicons/vue/24/solid/index.js';

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
