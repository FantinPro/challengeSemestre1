<template>
  <div class="flex justify-center items-center h-full">
    <div class="flex flex-col rounded-2xl shadow-2xl bg-neutral-800/75 py-10 px-16 w-[500px]">
      <h2 class="text-3xl font-bold mb-6 text-center">Connexion</h2>
      <FormKit type="form" @submit="submit" submit-label="Se connecter" :submit-attrs="{ outerClass: 'pt-4', inputClass: '!w-full !bg-primary-500' }" >
        <FormKit type="email" name="username" label="Email" validation="required" />
        <FormKit type="password" name="password" label="Mot de Passe" validation="required" />
      </FormKit>
      <span class="mt-3">
        Pas encore de compte ?
        <router-link to="/register" class="text-primary-400">Créer son compte</router-link>
      </span>
    </div>
  </div>
</template>


<script setup>

const submit = async (values) => {
  const response = await fetch('http://localhost:8000/api/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(values)
  })
  const user = await response.json()
}

</script>