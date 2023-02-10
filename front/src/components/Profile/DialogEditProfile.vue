<template>
  <TransitionRoot appear :show="props.isOpen" as="template">
    <Dialog as="div" class="relative z-10" @close="closeModal">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0">
        <div class="fixed inset-0 bg-black bg-opacity-25" />
      </TransitionChild>

      <div class="fixed inset-0">
        <div
          class="flex min-h-full items-center justify-center p-4 text-center">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95">
            <DialogPanel
              class="
                absolute
                flex
                max-h-[50%]
                w-full
                max-w-2xl
                transform
                flex-col
                overflow-auto
                rounded-2xl
                bg-[#212529]
                text-left
                align-middle
                shadow-xl
                transition-all
              ">
              <DialogTitle
                as="div"
                class="
                  sticky
                  top-0
                  z-10
                  bg-[#212529]
                  opacity-95
                  flex
                  p-2
                  justify-between
                ">
                <div class="flex justify-start items-center gap-2">
                  <button
                    type="button"
                    class="
                      inline-flex
                      justify-center
                      rounded-full
                      border border-transparent
                      p-2
                      text-sm
                      font-medium
                      text-white
                      hover:bg-gray-700
                      focus:outline-none
                      focus-visible:ring-1
                      focus-visible:ring-gray-500
                      focus-visible:ring-offset-1
                    "
                    @click="closeModal">
                    <svg
                      class="h-6 w-6"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      aria-hidden="true">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                  <h3 class="text-xl font-bold text-white">Edit profile</h3>
                </div>
                <div class="flex justify-end items-center">
                  <button
                    type="button"
                    :disabled="isLoading"
                    class="
                      inline-flex
                      justify-center
                      gap-2
                      rounded-full
                      border border-transparent
                      font-bold
                      bg-white
                      px-4
                      py-1
                      text-md text-black
                      hover:bg-gray-200
                      focus:outline-none
                      focus-visible:ring-1
                      focus-visible:ring-gray-500
                      focus-visible:ring-offset-1
                    "
                    @click="saveMutation">
                    <span>Save</span>
                    <Spin :is-loading="isLoading" />
                  </button>
                </div>
              </DialogTitle>
              <div class="">
                <div
                  class="h-40 w-full relative"
                  :style="{
                    backgroundColor: `#${Math.floor(
                      Math.random() * 16777215
                    ).toString(16)}`,
                  }">
                  <div
                    class="
                      absolute
                      top-24
                      left-6
                      h-32
                      w-32
                      rounded-full
                      bg-gray-300
                      border-4 border-[#2f3336]
                      shadow-md
                    ">
                    <img
                      class="h-full w-full rounded-full object-cover"
                      :src="
                        profile?.avatar || 'https://i.pravatar.cc/160?img=40'
                      "
                      alt="" />
                    <button
                      class="
                        absolute
                        bottom-0
                        right-0
                        top-1/2
                        left-1/2
                        -translate-x-1/2 -translate-y-1/2
                        p-2
                      "
                      @click="openModalUploadAvatar">
                      <svg
                        class="
                          h-10
                          w-10
                          bg-black bg-opacity-40
                          rounded-full
                          p-2
                        "
                        viewBox="0 0 24 24">
                        <path
                          d="M22 11.5V14.6C22 16.8402 22 17.9603 21.564 18.816C21.1805 19.5686 20.5686 20.1805 19.816 20.564C18.9603 21 17.8402 21 15.6 21H8.4C6.15979 21 5.03969 21 4.18404 20.564C3.43139 20.1805 2.81947 19.5686 2.43597 18.816C2 17.9603 2 16.8402 2 14.6V9.4C2 7.15979 2 6.03969 2.43597 5.18404C2.81947 4.43139 3.43139 3.81947 4.18404 3.43597C5.03969 3 6.15979 3 8.4 3H12.5M19 8V2M16 5H22M16 12C16 14.2091 14.2091 16 12 16C9.79086 16 8 14.2091 8 12C8 9.79086 9.79086 8 12 8C14.2091 8 16 9.79086 16 12Z"
                          stroke="#fff"
                          stroke-width="1"
                          stroke-linecap="round"
                          stroke-linejoin="round" />
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="h-20" />
              </div>
              <div class="px-6 py-2">
                <FormKit
                  v-model="username"
                  type="text"
                  name="username"
                  label="Username"
                  :validation="[[ 'required' ], [ 'length:3,255' ], [ 'matches', /^[a-zA-Z0-9_]*$/ ]]"
                  class="w-full"
                  :wrapper-class="{ 'formkit-wrapper': false }"
                  :classes="{ input: '!text-white' }" />
                <FormKit
                  v-model="bio"
                  type="textarea"
                  name="bio"
                  label="Bio"
                  :value="props.profile.bio"
                  validation="length:0,255"
                  :wrapper-class="{ 'formkit-wrapper': false }"
                  :classes="{ input: '!text-white' }" />
              </div>

              <div class="my-4 overflow-auto text-black">
                <!-- {{ props.message.content }} -->
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue';
import { ref } from 'vue';
import { useMutation } from 'vue-query';
import { useRouter } from 'vue-router';
import { toast } from 'vue3-toastify';
import { useUserStore } from '../../store/user';
import Spin from '../Loader/Spin.vue';

const router = useRouter();
const { user, updateProfile } = useUserStore();
const emit = defineEmits(['close']);

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  profile: {
    type: Object,
    required: true,
  },
});

let username = ref(user?.pseudo);
let bio = ref(props.profile?.bio);
let avatar = ref(props.profile?.avatar);


function closeModal() {
  emit('close');
}

const { isLoading, mutate: saveMutation } = useMutation(
  () => updateProfile({
    userId: user.id,
    pseudo: username.value,
    bio: bio.value,
    avatar: avatar.value,
  }),
  {
    onSuccess: () => {
      toast.success('Profile updated !');
      closeModal();
      router.push(`/profile/${username.value}`);
    },
    onError: (err) => {
      if (err.message.match(/Access Denied/)) {
        toast.error('You are not allowed to do this !');
      } else if (err.message.match(/Unique/)) {
        toast.error('Username already taken !');
      } else {
        toast.error('Something went wrong !');
      }
    },
  }
);

const openModalUploadAvatar = () => {
  console.log('openModalUploadAvatar');
};
</script>
