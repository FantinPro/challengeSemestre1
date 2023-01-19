<template>
  <div class="flex flex-col md:flex-row gap-2 h-full w-full">
    <header class="flex md:flex-grow max-md:bottom-0 sticky top-0 w-full h-screen max-md:bg-opacity-90 max-md:bg-slate-900">
      <SideMenu />
    </header>
    <main class="flex w-full">
      <slot class="w-full" />
      <div class="hidden md:flex md:w-1/3">
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
            <div class="mt-4">
              <p class="text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Quisquam quod, voluptatum, quia, voluptates quas voluptatibus
                quae voluptate quibusdam quidem quos nesciunt. Quisquam, quae.
                Quisquam quod, voluptatum, quia, voluptates quas voluptatibus
                quae voluptate quibusdam quidem quos nesciunt. Quisquam, quae.
              </p>
            </div>
            <div class="mt-4">
              <button
                class="
                  bg-blue-500
                  hover:bg-blue-600
                  text-white
                  font-semibold
                  py-2
                  px-4
                  rounded-full
                "
              >
                Follow
              </button>
            </div>
          </div>
        </div> -->
      </div>
    </main>
  </div>
</template>
<script setup>
import SideMenu from "../components/Menu/SideMenu.vue";
</script>
