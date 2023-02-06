<template>
  <div class="flex h-full justify-center w-full">
    <header class="flex">
      <SideMenu />
    </header>
    <main class="flex relative">
      <div class="flex flex-col border-r border-l border-[#4c5157]">
        <slot />
      </div>
      <div class="hidden top-0 lg:flex h-screen flex-col pt-4 pl-3">
        <SearchButton />
      </div>
    </main>
  </div>
</template>
<script setup>
import SideMenu from "../components/Menu/SideMenu.vue";
import SearchButton from "../components/Button/SearchButton.vue";
</script>
