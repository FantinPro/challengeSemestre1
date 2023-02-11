<template>
  <div class="flex flex-col w-full p-4 gap-8 max-h-screen overflow-hidden">
    <div class="w-full flex flex-col justify-center">

      <div class="flex-[0.5] mb-2">
        <v-date-picker :min-date="new Date()" is-expanded mode="date" :attributes="attributes" @dayclick="onDayClick"/>
      </div>

      <button
        type="button"
        class="inline-flex justify-center self-end w-1/4 gap-2 rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
        @click="openDialog">
        <span>Create ad</span>
      </button>

      <DialogCreateAd :is-open="isCreateAdDialogOpen" :start-date="getStartDate()" :end-date="getEndDate()" @close="closeDialog" />
    </div>

    <div class="overflow-x-auto bg-white shadow sm:rounded-md">
      <ul role="list" class="divide-y divide-gray-200">
        <li v-for="ad in ads.data.value" :key="ad.id">
          <a href="#" class="block hover:bg-gray-50">
            <div class="px-4 py-4 sm:px-6">
              <div class="flex items-center justify-between">
                <p class="truncate text-sm font-medium text-indigo-600 w-1/2 text-ellipsis">{{ ad.message }}</p>
                <div class="ml-2 flex flex-shrink-0">
                  <p :class="`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${getBgColor(ad.status)}`">{{ ad.status }}</p>
                </div>
              </div>
              <div class="mt-2 sm:flex sm:justify-between">
                <div class="sm:flex">
                  <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                    <CreditCardIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                    {{ ad.price }}€
                  </p>
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">

                  <CalendarIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                  <p>
                    <time :datetime="ad.startDate">{{ new Date(ad.startDate).toLocaleDateString()}}</time>
                    {{ '-' }}
                    <time :datetime="ad.endDate">{{ new Date(ad.endDate).toLocaleDateString()}}</time>
                  </p>
                </div>
              </div>
            </div>
          </a>
        </li>
      </ul>
    </div>

  </div>

</template>
<script setup>
import {inject, reactive, ref, watch} from 'vue';
import DialogCreateAd from '../components/Dialog/DialogCreateAd.vue';
import {useQuery} from "vue-query";

const $cookies = inject('$cookies');

const date = ref(null);

const isCreateAdDialogOpen = ref(false);
import {CreditCardIcon, CalendarIcon} from "@heroicons/vue/24/solid/index.js";

const attributes = ref([{
  highlight: true,
  dates: [{start: new Date(), span: 7}],
}]);


function getBgColor(status) {
  switch (status) {
    case 'pending':
      return 'bg-yellow-100 text-yellow-800';
    case 'accepted':
      return 'bg-green-100 text-green-800';
    case 'rejected':
      return 'bg-red-100 text-red-800';
    case 'payed':
      return 'bg-blue-100 text-blue-800';
  }
}

function getStartDate() {
  console.log('getStart', new Date(attributes.value[0].dates[0].start));
  return new Date(attributes.value[0].dates[0].start);
}

function getEndDate() {

  const date = getStartDate();
  date.setDate(date.getDate() + 7);
  console.log('getEnd', date);
  return date;
}

async function fetchAds() {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/api/pubs`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
    },
  });

  if (response.ok) {
    return await response.json();
  }

  throw new Error('Failed to fetch ads');
}

const ads = useQuery('ads', fetchAds);

function onDayClick(day) {


  const clickedDate = new Date(day.id);
  const today = new Date();

  if(clickedDate.getDate() < today.getDate() && clickedDate.getMonth() <= today.getMonth() && clickedDate.getFullYear() <= today.getFullYear()) {
    return;
  }

  attributes.value[0].dates = [{start: new Date(day.id), span: 7}];
}

const openDialog = () => {
  isCreateAdDialogOpen.value = true;
};

const closeDialog = () => {
  isCreateAdDialogOpen.value = false;
};

</script>
