<template>
  <div
    :class="[
      'flex items-center px-3 py-4 hover:cursor-pointer',
      type === 'searchBar' ? 'bg-neutral-800 hover:bg-neutral-600' : ''
    ]"
  >
    <div class="flex flex-col">
      <span
        :class="[
          'font-medium text-md ml- hidden md:block',
          type === 'default' && 'hover:underline'
        ]"
      >
        {{ props.echo.content }}
      </span>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  echo: {
    type: Object,
    required: true,
  },
  type: {
    type: String,
    default: 'default'
  },
})

</script>