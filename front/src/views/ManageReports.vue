<template>
  <div class="flex flex-1 flex-col p-8">
    <h1 class="mb-4 text-4xl">Reports</h1>
    <div class="relative h-0 flex-auto overflow-auto rounded-lg shadow-xl">
      <table class="w-full border-collapse text-left text-sm text-gray-500">
        <thead class="bg-row-table">
          <tr>
            <th scope="col" class="px-6 py-4 font-medium text-white">Pseudo</th>
            <th scope="col" class="px-6 py-4 font-medium text-white">Role</th>
            <th scope="col" class="px-6 py-4 font-medium text-white">
              Message
            </th>
            <th scope="col" class="px-6 py-4 font-medium text-white">
              Total reports
            </th>
            <th scope="col" class="px-6 py-4 font-medium text-white"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 border-t border-gray-100">
          <span v-if="isLoading"> Loading...</span>
          <tr
            v-for="message in messagesWithReports"
            :key="message.id"
            class="bg-row-table hover:bg-row-table-hover">
            <th class="flex gap-3 px-6 py-4 font-normal text-white">
              <div class="relative h-10 w-10">
                <img
                  class="h-full w-full rounded-full object-cover object-center"
                  :src="message.creator.profilePicture"
                  alt="" />
                <span
                  class="absolute right-0 bottom-0 h-2 w-2 rounded-full bg-green-400 ring ring-white"></span>
              </div>
              <div class="flex items-center text-sm">
                <div class="font-medium text-white">
                  {{ message.creator.pseudo }}
                </div>
              </div>
            </th>
            <td class="px-6 py-4 text-white">
              {{ $filters.displayRole(message.creator.roles[0]) }}
            </td>
            <td class="px-6 py-4">
                <div class="font-medium text-white">
                {{ message.content }}
                </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <span
                  class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-600">
                  {{ message.reportsCount }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex justify-end gap-4">
                Edit
              </div>
            </td>
          </tr>
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
            <img class="h-4 rotate-180" :src="ArrowLogo" alt="" />
          </div>
          <div
            class="cursor-pointer rounded-[50%] p-3 hover:bg-row-table-hover"
            @click="nextPage">
            <img class="h-4" :src="ArrowLogo" alt="" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue';
import { useQuery } from 'vue-query';
import ArrowLogo from '../assets/arrow.svg';
import DialogManageUser from '../components/Dialog/DialogManageUser.vue';
import { getAllMessagesWithAtLeast2Reports } from '../services/service.reports';
import { useUserStore } from '../store/user';
const { fetchUsersPaginated } = useUserStore();

const MAX_ITEM_PER_PAGE = 20;

const page = ref(1);
const messagesWithReports = ref([]);
const total = ref(0);
const nbPage = ref(0);

const { isLoading, refetch: getAllMessagesWithAtLeast2ReportsQuery } = useQuery(
  'reports',
  () => getAllMessagesWithAtLeast2Reports(page.value),
  {
    onSuccess: (data) => {
      console.log('🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩');
      console.log(data);
      console.log('🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦');
      messagesWithReports.value = data;
    },
  }
);

watch(page, (newPage) => {
  getAllMessagesWithAtLeast2ReportsQuery(newPage);
});

const nextPage = () => {
  if (page.value === nbPage.value) return;
  page.value++;
};

const previousPage = () => {
  if (page.value === 1) return;
  page.value--;
};

const updateUserList = (user) => {
  const index = users.value.findIndex((u) => u.id === user.id);
  users.value[index] = user;
};
</script>
