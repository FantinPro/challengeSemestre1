<template>
  <form id="payment-form">
    <div v-if="isLoading">Loading...</div>
    <div v-else-if="isError">{{error}}</div>
    <div v-else id="payment-element">
      <!-- Elements will create form elements here -->
    </div>
    <button id="submit">Subscribe</button>
    <div id="error-message">
      <!-- Display error message to your customers here -->
    </div>
  </form>
</template>

<script setup>
  import {inject} from 'vue';

  import { useQuery } from 'vue-query'
  const $cookies = inject('$cookies');
  async function fetchPremiumPK() {
    const res = await fetch('http://localhost:8000/api/premium_subscription',{
      headers: {
        'Authorization': `Bearer ${$cookies.get('echo_user_token')}`
      }})
    if(!res.ok) {
      throw new Error('Failed to fetch premium payment key')
    }
    return await res.json()
  }

  const { isLoading, isError, data, error } = useQuery({
    queryKey: ['premium_subscription'],
    queryFn: fetchPremiumPK,
  })
  // create a stripe element and mount it to the cardElement ref
  const stripe = Stripe('pk_test_51MRbROEn0vLQztryssXBn4xuu0aBjp8V29G25dTOdxm87fJin3c2lUAFl88gWYPtls3v5BRLeHDgHc8JHwFQiBEF00RA84Agsq');

  if (data.privateKey) {
    console.log(data.privateKey)
    const elements = stripe.elements({
      clientSecret: data.privateKey,
    });

    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element')
  }


  // handle payment
</script>