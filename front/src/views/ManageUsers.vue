<template>
  <div class="flex flex-1 flex-col p-8">
    <h1 class="mb-4 text-4xl">Users</h1>
    <div class="relative h-0 flex-auto overflow-auto rounded-lg shadow-xl">
      <table class="w-full border-collapse text-left text-sm text-gray-500">
        <thead class="bg-row-table">
          <tr>
            <th scope="col" class="px-6 py-4 font-medium text-white">
              Pseudo
            </th>
            <th scope="col" class="px-6 py-4 font-medium text-white">
              Email
            </th>
            <th scope="col" class="px-6 py-4 font-medium text-white">
              Role
            </th>
            <th scope="col" class="px-6 py-4 font-medium text-white">
              Total tweets
            </th>
            <th scope="col" class="px-6 py-4 font-medium text-white"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 border-t border-gray-100">
          <span v-if="isLoading"> Loading...</span>
          <tr
            v-for="user in users"
            :key="user.id"
            class="bg-row-table hover:bg-row-table-hover">
            <th class="flex gap-3 px-6 py-4 font-normal text-white">
              <div class="relative h-10 w-10">
                <img
                  class="h-full w-full rounded-full object-cover object-center"
                  :src="user.profilePicture"
                  alt="" />
                <span
                  class="absolute right-0 bottom-0 h-2 w-2 rounded-full bg-green-400 ring ring-white"></span>
              </div>
              <div class="flex items-center text-sm">
                <div class="font-medium text-white">
                  {{ user.pseudo }}
                </div>
              </div>
            </th>
            <td class="px-6 py-4">
              <span
                class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600">
                {{ user.email }}
              </span>
            </td>
            <td class="px-6 py-4 text-white">
              {{ $filters.displayRole(user.roles[0]) }}
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <span
                  class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-600">
                  {{ user.messagesCount }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex justify-end gap-4">
                <DialogManageUser action-name="Edit" :user="user" @update-user-list="updateUserList" />
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
import ArrowLogo from '../assets/arrow.svg';
import { ref, watch } from 'vue';
import { onMounted } from 'vue';
import { useUserStore } from '../store/user';
import { useMutation, useQuery } from 'vue-query';
import DialogManageUser from '../components/Dialog/DialogManageUser.vue';
const { fetchUsersPaginated } = useUserStore();

const emit = defineEmits(['update:layout', 'update:classes']);

onMounted(() => {
  emit('update:layout', 'main');
  emit('update:classes', 'flex w-full');
});

const MAX_ITEM_PER_PAGE = 20;

const page = ref(1);
const users = ref([]);
const total = ref(0);
const nbPage = ref(0);

const { isLoading } = useQuery(
  'usersPaginated',
  () => fetchUsersPaginated(page.value),
  {
    onSuccess: (data) => {
      users.value = data.users;
      total.value = data.total;
      nbPage.value = Math.ceil(total.value / MAX_ITEM_PER_PAGE);
    },
  }
);

const mutation = useMutation((page) => fetchUsersPaginated(page));

watch(page, (newPage) => {
  mutation.mutate(newPage, {
    onSuccess: (data) => {
      users.value = data.users;
    },
  });
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
