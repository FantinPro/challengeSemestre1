<template>
  <div class="flex flex-1 flex-col p-8">
    <h1 class="mb-4 text-4xl">Ads</h1>
    <div class="flex w-full justify-between">
      <Select
        class="w-52"
        title="Select ad status"
        :list="adStatus"
        :selected-value="selectedStatus"
        @update:model-value="updateStatus" />
      <div class="mt-auto w-fit">
        <button
          type="button"
          class="inline-flex justify-center gap-2 rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
          @click="openDialog">
          <span>Create ad</span>
        </button>
      </div>
    </div>

    <span v-if="isLoading" class="mt-4">Loading...</span>
    <span v-else-if="isError" class="mt-4">Error fetching ads</span>
    <!-- We can assume by this point that `isSuccess === true` -->
    <template v-else-if="data && data?.ads?.length">
      <div class="mt-4 h-0 flex-auto overflow-auto shadow sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
          <li v-for="ad in data.ads" :key="ad.id">
            <a href="#" class="block bg-white hover:bg-gray-50">
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="flex flex-col">
                    <p class="text-indigo-600 text-sm">
                      <strong>Pseudo: </strong>
                      {{ ad.owner.pseudo }}
                    </p>
                    <p
                      class="truncate text-ellipsis text-sm font-medium text-indigo-600">
                      <strong>Content: </strong>
                      {{ $filters.shortText(ad.message, 120) }}
                    </p>
                  </div>

                  <div class="flex gap-2">
                    <div class="ml-2 flex flex-shrink-0">
                      <p
                        class="my-auto h-fit"
                        :class="`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${getBgColor(
                          ad.status
                        )}`">
                        {{ ad.status }}
                      </p>
                    </div>
                    <button
                      v-if="ad.status === 'pending'"
                      type="button"
                      class="inline-flex justify-center gap-2 rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                      @click="editAd(ad)">
                      <span>Edit</span>
                    </button>
                    <DialogYesNo
                      :ad="ad"
                      action-name="Delete"
                      @action="deleteAdAction" />
                  </div>
                </div>
                <div class="mt-2 sm:flex sm:justify-between">
                  <div class="gap-3 sm:flex">
                    <p
                      class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                      <CreditCardIcon
                        class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"
                        aria-hidden="true" />
                      {{ ad.price }}€
                    </p>

                    <a
                      :href="ad.link"
                      target="_blank"
                      class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                      <LinkIcon
                        class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"
                        aria-hidden="true" />
                      {{ ad.link }}
                    </a>

                    <p
                      v-if="ad.status === 'payed'"
                      class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                      <EyeIcon
                        class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"
                        aria-hidden="true" />
                      Impression(s) :
                      {{ ad.impressions }}
                    </p>
                  </div>
                  <div
                    class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                    <CalendarIcon
                      class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"
                      aria-hidden="true" />
                    <p>
                      <time :datetime="ad.startDate">{{
                        new Date(ad.startDate).toLocaleDateString()
                      }}</time>
                      {{ '-' }}
                      <time :datetime="ad.endDate">{{
                        new Date(ad.endDate).toLocaleDateString()
                      }}</time>
                    </p>
                  </div>
                </div>
              </div>
            </a>
          </li>
        </ul>
      </div>
    </template>
    <span v-else class="mt-4">No ads</span>

    <DialogCreateAd
      :is-open="isCreateAdDialogOpen"
      :ad="selectedAd"
      @close="closeDialog" />
  </div>
</template>
<script setup>
import {
  CalendarIcon,
  CreditCardIcon,
  EyeIcon,
  LinkIcon,
} from '@heroicons/vue/24/solid/index.js';
import { ref } from 'vue';
import { useQuery, useQueryClient } from 'vue-query';
import { toast } from 'vue3-toastify';
import DialogCreateAd from '../components/Dialog/DialogCreateAd.vue';
import DialogYesNo from '../components/Dialog/DialogYesNo.vue';
import Select from '../components/Select/Select.vue';
import { deleteAd, getAds } from '../services/service.ads';
import { AD_STATUS } from '../utils/constants';

const queryClient = useQueryClient();

const adStatus = AD_STATUS;

const updateStatus = (state) => {
  selectedStatus.value = state;
};

const selectedStatus = ref(adStatus[0]);

const selectedAd = ref(null);

const isCreateAdDialogOpen = ref(false);

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

const { isLoading, isError, data } = useQuery({
  queryKey: ['ads', selectedStatus],
  queryFn: () =>
    getAds(1, selectedStatus.value.value, { orderBy: 'created', order: 'asc' }),
});

const openDialog = () => {
  isCreateAdDialogOpen.value = true;
};

const closeDialog = () => {
  isCreateAdDialogOpen.value = false;
  selectedAd.value = null;
};

const deleteAdAction = async (ad) => {
  try {
    await deleteAd(ad);
    toast.success('Ad deleted successfully');
    queryClient.invalidateQueries('ads');
  } catch (error) {
    console.error(error);
    toast.error('Error deleting ad');
  }
};

const editAd = (ad) => {
  selectedAd.value = ad;
  openDialog();
};
</script>
