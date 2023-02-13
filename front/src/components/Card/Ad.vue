<template>
  <div class="flex p-2">
    <img
      class="h-12 w-12 rounded-full"
      :src="props.item.owner.profilePicture"
      alt="avatar" />
    <div class="ml-2 flex-1 flex flex-col">
      <div class="flex gap-1 items-center">
        <router-link
          :to="`/profile/${props.item.owner.pseudo}`"
          class="font-bold text-gray-200">
          {{ props.item.owner.pseudo }}
        </router-link>
        <CheckBadgeIcon class="h-4 w-4 text-green-500" />
        <router-link
          :to="`/profile/${props.item.owner.pseudo}`"
          class="text-sm text-gray-400">
          @{{ props.item.owner.pseudo }}
        </router-link>
      </div>
      <div>
        {{ props.item.message }}
      </div>
      <div
        class="
          flex
          text-[10px]
          font-semibold
          text-gray-500
          hover:underline
          items-center
          gap-1
          cursor-pointer
          w-max
          mt-1
        ">
        <span
          class="
            ease
            flex
            w-max
            cursor-pointer
            items-center
            rounded-sm
            bg-gray-500
            opacity-60
            transition
            duration-300
            active:bg-gray-600
          ">
          <ArrowUpRightIcon class="h-4 w-4 fill-gray-50" />
        </span>
        Sponsored
      </div>
    </div>
  </div>
</template>
<script setup>
import { ArrowUpRightIcon, CheckBadgeIcon } from '@heroicons/vue/20/solid';
const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});
</script>
