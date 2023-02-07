<template>
  <div class="flex flex-col overflow-auto h-full">
    <ProfilHeader />
  </div>
</template>
<script setup>
import { computed, onMounted } from "vue-demi";
import { useRouter } from "vue-router";
import HeaderMenu from "../components/Menu/HeaderMenu.vue";
import ProfilHeader from "../components/Profile/ProfileHeader.vue";
import { useUserStore } from "../store/user";

const { getUserProfileByUsername } = useUserStore();
const router = useRouter();

const profile = computed(() => useUserStore().profile);

onMounted(async () => {
  if (!router.currentRoute.value.params.pseudo) {
    router.push("/");
  }

  await getUserProfileByUsername(router.currentRoute.value.params.pseudo);

  if (!profile.value) {
    router.push("/");
  }

});
</script>
