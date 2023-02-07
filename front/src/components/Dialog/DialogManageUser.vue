<template>
  <div class="justify-center">
    <button
      type="button"
      class="rounded-md bg-black bg-opacity-20 px-4 py-2 text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
      @click="openModal">
      {{ props.actionName }}
    </button>
  </div>
  <TransitionRoot appear :show="isOpen" as="template">
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

      <div class="fixed inset-0 overflow-y-auto">
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
              class="flex h-96 w-full max-w-md transform flex-col overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-gray-900">
                Change role of <strong>{{ props.user.pseudo }}</strong>
              </DialogTitle>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Your are about to change the role of
                  <strong>{{ props.user.pseudo }}</strong> from
                  <strong>{{
                    $filters.displayRole(props.user.roles?.[0])
                  }}</strong>
                  to <strong>{{ selectedRole.display }}</strong
                  >.
                </p>
                <Listbox v-model="selectedRole" as="div" class="mt-4">
                  <div class="relative mt-1">
                    <ListboxButton
                      class="relative w-full cursor-default rounded-lg bg-white py-2 pl-3 pr-10 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm">
                      <span class="block truncate text-black">{{
                        selectedRole.display
                      }}</span>
                      <span
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                        <ChevronUpDownIcon
                          class="h-5 w-5 text-gray-400"
                          aria-hidden="true" />
                      </span>
                    </ListboxButton>

                    <transition
                      leave-active-class="transition duration-100 ease-in"
                      leave-from-class="opacity-100"
                      leave-to-class="opacity-0">
                      <ListboxOptions
                        class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                        <ListboxOption
                          v-for="role in roles"
                          v-slot="{ active, selected }"
                          :key="role.value"
                          :value="role"
                          as="template">
                          <li
                            :class="[
                              active
                                ? 'bg-amber-100 text-amber-900'
                                : 'text-gray-900',
                              'relative cursor-default select-none py-2 pl-10 pr-4',
                            ]">
                            <span
                              :class="[
                                selected ? 'font-medium' : 'font-normal',
                                'block truncate',
                              ]"
                              >{{ role.display }}</span
                            >
                            <span
                              v-if="selected"
                              class="absolute inset-y-0 left-0 flex items-center pl-3 text-amber-600">
                              <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                          </li>
                        </ListboxOption>
                      </ListboxOptions>
                    </transition>
                  </div>
                </Listbox>
              </div>
              <div class="my-auto flex justify-center text-black">
                <img
                  :src="props.user.profilePicture"
                  class="h-20 rounded-[50%] border border-blue-300" />
              </div>


              <div class="mt-auto flex justify-end gap-2">
                <button
                  type="button"
                  class="inline-flex justify-center rounded-md border border-transparent bg-slate-100 px-4 py-2 text-sm font-medium text-slate-900 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                  @click="closeModal">
                  Cancel
                </button>
                <button
                  type="button"
                  :disabled="isLoading"
                  class="inline-flex gap-2 justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                  @click="confirm">
                  <span >Confirm</span>
                  <Spin :is-loading="isLoading" />
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref } from 'vue';
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
  Listbox,
  ListboxButton,
  ListboxOptions,
  ListboxOption,
} from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';
import { ROLES } from '../../utils/constants';
import { changeRoleByUserId } from '../../services/service.users';
import { useMutation } from 'vue-query';
import Spin from '../Loader/Spin.vue';
import { toast } from 'vue3-toastify';

const props = defineProps({
  actionName: {
    type: String,
    default: 'Action',
  },
  user: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['updateUserList']);

const roles = Object.values(ROLES).map((role) => {
  const mapped = role.replace('ROLE_', '');
  const roleCapitalized =
    mapped.charAt(0).toUpperCase() + mapped.slice(1).toLowerCase();
  return {
    value: role,
    display: roleCapitalized,
  };
});
const selectedRole = ref(
  roles.find((role) => role.value === props.user.roles?.[0])
);
const isOpen = ref(false);

function closeModal() {
  isOpen.value = false;
}
function openModal() {
  isOpen.value = true;
}

const { isLoading, mutate: changeRoleByUserIdMutation } = useMutation(
  (data) => changeRoleByUserId(data),
  {
    onSuccess: () => {
      toast.success('Role changed successfully');
      emit('updateUserList', {
        ...props.user,
        roles: [selectedRole.value.value],
      });
      closeModal();
    },
    onError: () => {
      toast.error('Something went wrong');
    },
  }
);

const confirm = () => {
  changeRoleByUserIdMutation({
    userId: props.user.id,
    role: selectedRole.value.value,
  });
};
</script>
