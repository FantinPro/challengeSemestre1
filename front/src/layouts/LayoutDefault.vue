<template>
  <div class="flex h-full justify-center container mx-auto">
    <header class="flex">
      <SideMenu />
    </header>
    <main class="flex flex-1 relative">
      <div class="flex flex-col flex-1 border-r border-l border-[#4c5157]">
        <router-view></router-view>
      </div>
      <div class="hidden lg:flex lg:min-w-[33%] flex-col pt-4 pl-4">
        <SearchButton />
        <CardFollowings class="mt-6" />
      </div>
    </main>
  </div>
</template>
<script setup>
import SideMenu from "../components/Menu/SideMenu.vue";
import SearchButton from "../components/Button/SearchButton.vue";
import CardFollowings from "../components/CardFollowings/CardFollowings.vue";
</script>
