<template>
  <div class="flex flex-1 flex-col p-8">
    <h1 class="mb-4 text-4xl">Manage Ads</h1>
    <Select
      class="w-52"
      title="Select ad status"
      :list="adStatus"
      :selected-value="selectedStatus"
      @update:model-value="updateStatus" />
    <div
      class="relative mt-6 flex h-0 flex-auto flex-col justify-between overflow-auto rounded-lg shadow-xl">
      <table class="w-full border-collapse text-left text-sm text-gray-500">
        <thead class="bg-row-table">
          <tr>
            <th scope="col" class="px-6 py-4 font-medium text-white">Pseudo</th>
            <th scope="col" class="px-6 py-4 font-medium text-white">Status</th>
            <th scope="col" class="px-6 py-4 font-medium text-white">
              Ad Content
            </th>
            <th scope="col" class="px-6 py-4 font-medium text-white">Price</th>
            <th scope="col" class="px-6 py-4 font-medium text-white">Start</th>
            <th scope="col" class="px-6 py-4 font-medium text-white">End</th>
            <th scope="col" class="px-6 py-4 font-medium text-white"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 border-t border-gray-100">
          <span v-if="isLoading"> Loading...</span>
          <template v-if="ads.length">
            <tr
              v-for="ad in ads"
              :key="ad.id"
              class="bg-row-table hover:bg-row-table-hover">
              <th class="flex gap-3 px-6 py-4 font-normal text-white">
                <div class="relative h-10 w-10">
                  <img
                    class="h-full w-full rounded-full object-cover object-center"
                    :src="ad.owner.profilePicture"
                    alt="" />
                  <span
                    class="absolute right-0 bottom-0 h-2 w-2 rounded-full bg-green-400 ring ring-white"></span>
                </div>
                <div class="flex items-center text-sm">
                  <div class="font-medium text-white">
                    {{ ad.owner.pseudo }}
                  </div>
                </div>
              </th>
              <td class="px-6 py-4 text-white">
                <span
                  class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold"
                  :class="[
                    ad.status === 'pending'
                      ? 'bg-yellow-50 text-yellow-600'
                      : '',
                    ad.status === 'accepted'
                      ? 'bg-green-50 text-green-600'
                      : '',
                    ad.status === 'rejected' ? 'bg-red-50 text-red-600' : '',
                    ad.status === 'payed' ? 'bg-blue-50 text-blue-600' : '',
                  ]">
                  {{ ad.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="font-medium text-white">
                  {{ $filters.shortText(ad.message, 50) }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex gap-2">
                  <span
                    class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600">
                    {{ ad.price }}€
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="font-medium text-white">
                  {{ new Date(ad.startDate).toLocaleDateString() }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="font-medium text-white">
                  {{ new Date(ad.endDate).toLocaleDateString() }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-end gap-4">
                  <DialogManageAd
                    :ad="ad"
                    action-name="Manage"
                    @update-ad-list="
                      updateAdList
                    " />
                </div>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td class="px-6 py-4 text-center" colspan="7">
                <span class="text-gray-500">No ads found</span>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
      <div
        v-show="!isLoading"
        class="sticky bottom-0 flex w-full justify-end gap-4 rounded-lg bg-row-table p-4 text-white shadow-inner">
        <div class="flex items-center">
          <strong>Total :&nbsp;</strong>
          <strong>{{ total }}</strong>
        </div>
        <div class="flex items-center">
          <strong>Page :&nbsp;</strong>
          <strong>{{ page }} of {{ nbPage }}</strong>
        </div>
        <div class="flex gap-2">
          <div
            class="cursor-pointer rounded-[50%] p-3 hover:bg-row-table-hover"
            @click="previousPage">
            <img class="h-4 rotate-180" src="/arrow.svg" alt="" />
          </div>
          <div
            class="cursor-pointer rounded-[50%] p-3 hover:bg-row-table-hover"
            @click="nextPage">
            <img class="h-4" src="/arrow.svg" alt="" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { useQuery, useQueryClient } from 'vue-query';
import Select from '../components/Select/Select.vue';
import { getAds } from '../services/service.ads';
import DialogManageAd from '../components/Dialog/DialogManageAd.vue';
import { AD_STATUS } from '../utils/constants';

const adStatus = AD_STATUS;

const updateStatus = (state) => {
  selectedStatus.value = state;
};

const selectedStatus = ref(adStatus[0]);

const queryClient = useQueryClient();

const MAX_ITEM_PER_PAGE = 20;

const page = ref(1);
const ads = ref([]);
const total = ref(0);
const nbPage = ref(0);

const { isLoading } = useQuery({
  queryKey: ['manageAds', page, selectedStatus],
  queryFn: () => getAds(page.value, selectedStatus.value.value),
  keepPreviousData: true,
  onSuccess: (data) => {
    ads.value = data.ads;
    total.value = data.total;
    nbPage.value = Math.ceil(data.total / MAX_ITEM_PER_PAGE);
  },
});

const nextPage = () => {
  if (page.value === nbPage.value) return;
  page.value++;
};

const previousPage = () => {
  if (page.value === 1) return;
  page.value--;
};

const updateAdList = (message) => {
  queryClient.invalidateQueries('manageAds');
};
</script>
