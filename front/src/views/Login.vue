<template>
  <div class="flex w-full justify-center items-center h-full">
    <div class="flex flex-col rounded-2xl shadow-2xl bg-neutral-800/75 py-10 px-16 w-[500px]">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold mb-6">Login</h1>
        <div class="flex mb-6 items-center justify-center gap-3">
          <h2 class="text-2xl font-medium">Echo</h2>
          <img src="/logo.svg" alt="Logo" class="w-10 h-10 invert" />
        </div>
      </div>
      <FormKit type="form" submit-label="Login" :submit-attrs="{ outerClass: 'pt-4', inputClass: '!w-full !bg-primary-500 !font-medium' }" @submit="submit" >
        <FormKit type="email" name="email" label="E-mail" validation="required" :classes="{ input: '!text-white' }" />
        <FormKit type="password" name="password" label="Password" validation="required" :classes="{ input: '!text-white' }" />
      </FormKit>
      <span class="mt-3">
        New to Echo?
        <router-link to="/register" class="text-primary-400">Create an account</router-link>
      </span>
    </div>
  </div>
</template>


<script setup>

import { onMounted } from 'vue';
import { useUserStore } from "../store/user";

const userStore = useUserStore();

const emit = defineEmits(["update:layout"]);
onMounted(() => {
  emit('update:layout', 'div')
});

const submit = async (values) => {
  await userStore.signIn(values);
}

</script>