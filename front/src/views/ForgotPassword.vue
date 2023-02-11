<template>
  <div class="flex w-full justify-center items-center h-full">
    <div class="flex flex-col rounded-2xl shadow-2xl bg-neutral-800/75 py-10 px-16 w-[500px]">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold mb-6">Forgot Password</h1>
        <div class="flex mb-6 items-center justify-center gap-3">
          <h2 class="text-2xl font-medium">Echo</h2>
          <img src="/logo.svg" alt="Logo" class="w-10 h-10 invert" />
        </div>
      </div>
      <FormKit type="form" submit-label="Send email" :submit-attrs="{ outerClass: 'pt-4', inputClass: '!w-full !bg-primary-500 !font-medium' }" @submit="submit" >
        <FormKit type="email" name="email" label="E-mail" validation="required" :classes="{ input: '!text-white' }" />
      </FormKit>
    </div>
  </div>
</template>


<script setup>

import { onMounted } from 'vue';
import { useUserStore } from "../store/user";
import { toast } from 'vue3-toastify';

const userStore = useUserStore();

const emit = defineEmits(["update:layout"]);
onMounted(() => {
  emit('update:layout', 'div')
});

const submit = async (values) => {
  const response = await userStore.forgotPassword(values);

  if (response.ok) {
    toast.success('Check your email!')
  } else {
    toast.error('Error in the request')
  }
}

</script>