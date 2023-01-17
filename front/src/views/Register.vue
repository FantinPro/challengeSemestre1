<template>
  <div class="flex flex-col text-black rounded-2xl bg-white py-5 px-10">
    <h2 class="text-3xl mb-6">Inscription</h2>
    <FormKit type="form" @submit="submit" submit-label="S'inscrire" :submit-attrs="{ outerClass: 'pt-4' }" >
      <FormKit type="text" name="email" validation="required" label="Email" />
      <FormKit type="text" name="username" validation="required" label="Username" />
      <FormKit type="password" name="password" validation="required" label="Mot de Passe" />
      <FormKit type="password" name="password_confirm" validation="required|confirm" label="Confirmer le mot de passe" />
    </FormKit>
  </div>
</template>


<script setup>

const submit = async (values) => {
  const response = await fetch('http://localhost:8000/api/users', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(values)
  })
  const newUser = await response.json()
  console.log(newUser)
}

</script>