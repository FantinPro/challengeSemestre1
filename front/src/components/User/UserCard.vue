<template>
  <div
    :class="[
      'flex items-center px-3 py-4 hover:cursor-pointer',
      type === 'searchBar' ? 'bg-neutral-800 hover:bg-neutral-600' : ''
    ]"
  >
    <img
      v-if="profilePicture"
      :src="profilePicture"
      :class="[
        'w-8 h-8 rounded-full object-cover',
        type == 'default' && 'self-start'
      ]"
      alt="User avatar"
    />
    <div v-else class="w-6 h-6 bg-neutral-500 rounded-full" />
    <div class="flex flex-col">
      <span
        :class="[
          'font-bold text-md ml-3 hidden md:block',
          type === 'default' && 'hover:underline'
        ]"
      >
        {{ pseudo }}
      </span>
      <span class="text-sm ml-3 line-clamp-2">{{ bio }}</span>
    </div>
  </div>
</template>

<script setup>
import { toRefs } from "vue";


const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  type: {
    type: String,
    default: 'default'
  },
})

const { profilePicture, pseudo, bio } = toRefs(props.user)

</script>