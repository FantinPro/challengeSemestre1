<template>

  <button
      v-if="!isPremium && !premiumLink.isLoading.value"
      type="button"
      @click="pay"
      class="
          hover:bg-[#2f3336]
          text-white
          font-semibold
          px-3
          py-2
          rounded-full
          border border-[#3b4043]
          transition-all
          duration-300
          ">
    Become Premium
  </button>

  <button
      v-if="isPremium && !premiumLink.isLoading.value"
      type="button"
      @click="cancel.mutateAsync"
      :disabled="cancel.isLoading.value"
      class="
          hover:bg-[#2f3336]
          text-white
          font-semibold
          px-3
          py-2
          rounded-full
          border border-[#3b4043]
          transition-all
          duration-300
          ">
    Cancel Premium
  </button>
</template>




<script setup>
import {inject, onMounted, ref} from 'vue';
import {useMutation, useQuery} from "vue-query";
import {toast} from "vue3-toastify";

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

  async function cancelSubscribtion() {
    const res = await fetch('http://localhost:8000/api/premium_subscription',{
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${$cookies.get('echo_user_token')}`
      }})
    if(!res.ok) {
      throw new Error('Failed to cancel premium subscription')
    }
    return await res.json()
  }

  const isPremium = ref(false)

  const pay = async () => {
    if (href) location.href = href.value
  }

  onMounted(() => {
    // get query params
    const urlParams = new URLSearchParams(window.location.search);
    const premiumSubscription = urlParams.get('premium_subscription');

    if(premiumSubscription && premiumSubscription === "success" ) {
      toast.success('Premium subscription successful')
    }
  })

  const premiumLink = useQuery(['premiumLink'], fetchPremiumPK, {
    onSuccess: (data) => {
      if (data?.premium === "active") {
        isPremium.value = true
      }
      if (data?.premium === "link") {
        isPremium.value = false
        href.value = data.url
      }
    }
  })

  const cancel = useMutation(cancelSubscribtion, {
    onSuccess: async () => {
      await premiumLink.refetch.value()
      toast.success('Premium subscription canceled')
    },
    onError: async () => {
      toast.error('Failed to cancel premium subscription')
    }
  })
  // handle payment
</script>