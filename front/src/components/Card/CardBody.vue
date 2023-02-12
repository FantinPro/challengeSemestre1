<template>
  <div class="font-medium text-base">
    <div v-if="props.item.parent" class="flex flex-col">
      <div class="flex items-center" @click.stop="onClickProfile">
        <!-- <img
            class="ml-1 h-10 w-10 rounded-full"
            :src="props.item.parent.creator.profilePicture"
            alt="avatar" /> -->
        <div class="flex text-base font-medium text-gray-400">
          Replying to
          <div class="ml-1 text-primary-400 hover:underline">
            @{{ props.item.parent.creator.pseudo }}
          </div>
        </div>
      </div>

      <!-- <div
          class="
            mr-auto
            ml-[21px]
            h-full
            min-h-[20px]
            border border-slate-500
          "></div> -->
    </div>
    <div v-if="props.item.isDeleted" class="text-white">
      <span class="font-bold text-orange-400">[deleted]</span>
      <span class="ml-2 text-gray-400">{{ props.item.content }}</span>
    </div>
    <div v-else>
      <span>{{ props.item.content }}</span>
    </div>
  </div>
</template>
<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();
const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const onClickProfile = () => {
  router.push(`/profile/${props.item.creator.pseudo}`);
};
</script>
