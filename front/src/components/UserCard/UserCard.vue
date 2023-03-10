<template>
  <div :class="!suggestions && 'border-[#4c5157] border-b py-2 px-3'">
    <div class="flex">
      <img
        v-if="user.profilePicture"
        :src="user.profilePicture"
        class="w-10 h-10 rounded-full object-cover"
        alt="avatar" />
      <div v-else class="w-10 h-10 rounded-full bg-gray-400" />
      <div class="flex-1 ml-1">
        <div class="flex flex-col">
          <div class="flex justify-between">
            <div class="flex flex-col items-baseline">
              <router-link
                :to="`/profile/${user.pseudo}`"
                class="font-bold text-white text-md hover:underline">
                {{ user.pseudo }}
              </router-link>
              <router-link
                :to="`/profile/${user.pseudo}`"
                class="text-base text-gray-400 -mt-1">
                @{{ user.pseudo }}
              </router-link>
            </div>
            <div v-if="!isMe" class="flex items-center gap-2">
              <button
                v-if="!user.followed"
                class="
                  bg-[#fff]
                  text-black
                  rounded-full
                  px-4
                  py-1
                  w-28
                  text-sm
                  lg:text-base
                  font-bold
                  hover:opacity-80
                  transition
                  duration-200
                  ease-in-out
                "
                @click="followUser">
                Follow
              </button>
              <button
                v-else
                class="
                  text-white
                  border-white border
                  rounded-full
                  px-4
                  py-1
                  w-28
                  text-sm
                  lg:text-base
                  font-bold
                  transition
                  duration-200
                  ease-in-out
                  hover:bg-opacity-50
                  hover:bg-red-800
                  hover:border-red-500
                  hover:text-red-500
                "
                @click="unfollowUser"
                @mouseover="showUnfollowOnHover = true"
                @mouseleave="showUnfollowOnHover = false">
                <template v-if="showUnfollowOnHover">Unfollow</template>
                <template v-else>Following</template>
              </button>
              <Menu
                v-if="router.currentRoute.value.params.pseudo === useUserStore().user.pseudo && router.currentRoute.value.params.tab === 'followers'"
                as="div"
                class="relative p-2 ml-5">
                <MenuButton
                  class="
                    absolute
                    -top-2
                    right-0
                    justify-center
                    rounded-full
                    bg-opacity-20
                    p-2
                    hover:bg-[#4c5157]
                    focus:outline-none
                    focus-visible:ring-2
                    focus-visible:ring-white
                    focus-visible:ring-opacity-95
                  ">
                  <EllipsisHorizontalIcon
                    class="h-5 w-5 text-violet-200 hover:text-violet-100"
                    aria-hidden="true" />
                </MenuButton>
                <transition
                  enter-active-class="transition duration-100 ease-out"
                  enter-from-class="transform scale-95 opacity-0"
                  enter-to-class="transform scale-100 opacity-100"
                  leave-active-class="transition duration-75 ease-in"
                  leave-from-class="transform scale-100 opacity-100"
                  leave-to-class="transform scale-95 opacity-0">
                  <MenuItems
                    class="
                      absolute
                      right-0
                      mt-2
                      w-56
                      origin-top-right
                      divide-y divide-gray-100
                      rounded-md
                      bg-white
                      shadow-lg
                      ring-1 ring-black ring-opacity-5
                      focus:outline-none
                    ">
                    <div class="px-1 py-1">
                      <MenuItem
                        v-slot="{ active }">
                        <button
                          :class="[
                            active
                              ? 'bg-primary-300 text-white'
                              : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                          ]"
                          :disabled="isLoadingMask"
                          @click="deleteMessageMutation">
                          <TrashIcon
                            :active="active"
                            class="mr-2 h-5 w-5"
                            :class="[
                              active ? 'text-white-400' : 'text-primary-300',
                            ]"
                            aria-hidden="true" />
                          Remove Follower
                        </button>
                      </MenuItem>
                    </div>
                  </MenuItems>
                </transition>
              </Menu>
            </div>
          </div>
          <span v-if="!suggestions" class="text-base font-medium text-white mt-1">{{
            user.bio
          }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { EllipsisHorizontalIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { useRouter } from 'vue-router';
import { toast } from 'vue3-toastify';
import { computed, ref, watch } from 'vue-demi';
import { useUserStore } from '../../store/user';

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  suggestions: {
    type: Boolean,
    default: false
  }
});

const user = ref(props.user);

watch(
  () => props.user,
  (newVal) => {
    user.value = newVal;
  },
);

const router = useRouter();
const { followUserById, unfollowUserById } = useUserStore();

const isMe = computed(() => {
  return useUserStore().user.id === user.value.id;
});
const showUnfollowOnHover = ref(false);

const emit = defineEmits(['updateFollowersList']);

const followUser = async () => {
  const res = await followUserById(user.value.id);
  if (res) {
    toast.success(`You are now following ${user.value.pseudo} ????`);
    emit('updateFollowersList', user.value.id, true);
  } else {
    toast.error(`Something went wrong ????`);
  }
};

const unfollowUser = async () => {
  const res = await unfollowUserById(user.value.id);
  if (res) {
    toast.success(`You are no longer following ${user.value.pseudo} ????`);
    emit('updateFollowersList', user.value.id, false);
  } else {
    toast.error(`Something went wrong ????`);
  }
};
</script>