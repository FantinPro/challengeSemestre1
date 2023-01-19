<template>
  <div>
    {{ user?.pseudo }}
  </div>
</template>
<script setup>
import { onMounted } from "vue-demi";
import { useUserStore } from "../store/user";
import LayoutDefault from "../layouts/LayoutDefault.vue";
import { useRouter } from "vue-router";

const { user } = useUserStore();
const router = useRouter();
const emit = defineEmits(["update:layout"]);

onMounted(() => {
  emit("update:layout", LayoutDefault);
  // TODO modifier pour accepter les routes des autres utilisateurs
  if (router.currentRoute.value.params.pseudo !== user?.pseudo) {
    router.push("/");
  }
});
</script>
