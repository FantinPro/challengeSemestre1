<template>
  <div class="flex justify-center items-center h-full">
    <div class="flex flex-col rounded-2xl shadow-2xl bg-neutral-800/75 py-10 px-16 w-[500px]">
      <h2 class="text-3xl font-bold mb-6 text-center">Inscription</h2>
      <FormKit type="form" @submit="submit" submit-label="S'inscrire" :submit-attrs="{ outerClass: 'pt-4', inputClass: '!w-full !bg-primary-500' }" >
        <FormKit type="text" name="email" validation="required" label="Email" />
        <FormKit type="text" name="pseudo" validation="required" label="Pseudo" />
        <FormKit type="password" name="password" validation="required" label="Mot de Passe" />
        <FormKit type="password" name="password_confirm" validation="required|confirm" label="Confirmer le mot de passe" />
      </FormKit>
      <span class="mt-3">
        Déjà un compte ?
        <router-link to="/login" class="text-primary-400">Se connecter</router-link>
      </span>
    </div>
  </div>
</template>


<script setup>

import { useUserStore } from "../store/user";

const userStore = useUserStore();

const submit = async (values) => {
  await userStore.signUp(values);
}

</script>