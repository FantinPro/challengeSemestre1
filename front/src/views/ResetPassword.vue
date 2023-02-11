<template>
  <div class="flex w-full justify-center items-center h-full">
    <div class="flex flex-col rounded-2xl shadow-2xl bg-neutral-800/75 py-10 px-16 w-[500px]">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold mb-6">Reset Password</h1>
        <div class="flex mb-6 items-center justify-center gap-3">
          <h2 class="text-2xl font-medium">Echo</h2>
          <img src="/logo.svg" alt="Logo" class="w-10 h-10 invert" />
        </div>
      </div>
      <FormKit type="form" submit-label="Reset password" :submit-attrs="{ outerClass: 'pt-4', inputClass: '!w-full !bg-primary-500 !font-medium' }" @submit="submit" >
        <FormKit type="password" name="password" validation="required" label="Password" :classes="{ input: '!text-white' }" />
        <FormKit type="password" name="password_confirm" validation="required|confirm" label="Confirm Password" :classes="{ input: '!text-white' }" />
      </FormKit>
    </div>
  </div>
</template>


<script setup>

import { onMounted } from 'vue';
import { useUserStore } from "../store/user";
import { useRoute, useRouter } from 'vue-router';
import { toast } from 'vue3-toastify';

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();

const emit = defineEmits(["update:layout"]);
onMounted(() => {
  emit('update:layout', 'div')
});

const submit = async (values) => {
  const response = await userStore.resetPassword(values, route.query['token']);

  if (response.ok) {
    toast.success('Your password has been successfully changed!')
    router.push('/login');
  } else {
    toast.error('Error in the request')
  }
}

</script>