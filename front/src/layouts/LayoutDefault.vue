<template>
  <div class="flex flex-col md:flex-row h-full md:m-auto">
    <header class="flex max-md:w-full bottom-0 fixed md:relative md:h-screen max-md:bg-opacity-90 max-md:bg-slate-900 border-r border-[#4c5157]">
      <SideMenu />
    </header>
    <main class="flex w-full">
      <div class="flex flex-col min-w-[600px]">
        <SearchButton />
        <slot />
      </div>
      <div class="hidden md:flex md:w-1/3 border-l border-[#4c5157]">
        <!-- <div class="">
          <div class="bg-white rounded-lg shadow-lg p-4">
            <div class="flex items-center">
              <img
                class="w-10 h-10 rounded-full"
                src="https://i.pravatar.cc/150?img=1"
                alt="avatar"
              />
              <div class="ml-4">
                <h1 class="text-lg font-semibold">John Doe</h1>
                <p class="text-sm text-gray-500">@johndoe</p>
              </div>
            </div>
          </div>
        </div> -->
      </div>
    </main>
  </div>
</template>
<script setup>
import SideMenu from "../components/Menu/SideMenu.vue";
import SearchButton from "../components/Button/SearchButton.vue";
</script>
