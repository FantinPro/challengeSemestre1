<template>
  <button type="button" @click="pay">Pay</button>
</template>




<script setup>
import {inject, onMounted, ref} from 'vue';

const $cookies = inject('$cookies');

  const href = ref('');

  async function fetchPremiumPK() {
    const res = await fetch('http://localhost:8000/api/premium_subscription',{
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${$cookies.get('echo_user_token')}`
      }})
    if(!res.ok) {
      throw new Error('Failed to fetch premium payment key')
    }
    return await res.json()
  }

  onMounted(async () => {
    console.log($cookies.get('echo_user_token'))
    href.value = await fetchPremiumPK()
  })

  const pay = async () => {
    if (href) location.href = href.value
  }


  // handle payment
</script>