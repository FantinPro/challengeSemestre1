<template>
  <div class="flex w-full justify-center items-center h-full">
    <div class="flex flex-col rounded-2xl shadow-2xl bg-neutral-800/75 py-10 px-16 w-[500px]">
      <h2 class="text-3xl font-bold mb-6 text-center">Sign Up</h2>
      <FormKit type="form" submit-label="S'inscrire" :submit-attrs="{ outerClass: 'pt-4', inputClass: '!w-full !bg-primary-500' }" @submit="submit" >
        <FormKit type="text" name="email" validation="required" label="E-mail" :classes="{ input: '!text-white' }" />
        <FormKit type="text" name="pseudo" validation="required" label="Username" :classes="{ input: '!text-white' }" />
        <FormKit type="password" name="password" validation="required" label="Password" :classes="{ input: '!text-white' }" />
        <FormKit type="password" name="password_confirm" validation="required|confirm" label="Confirm Password" :classes="{ input: '!text-white' }" />
      </FormKit>
      <span class="mt-3">
        Already have an account?
        <router-link to="/login" class="text-primary-400">Login</router-link>
      </span>
    </div>
  </div>
</template>


<script setup>

import { useUserStore } from "../store/user";
import { toast } from 'vue3-toastify';
import { useRouter } from 'vue-router';

const router = useRouter();
const userStore = useUserStore();

const submit = async (values) => {
  const response = await userStore.signUp(values);

  if (response.ok) {
    toast.success('Your account has been successfully created!')
    router.push('/login')
  } else {
    toast.error('Error in the request')
  }
}

</script>