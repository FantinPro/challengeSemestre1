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
              class="flex w-full max-w-md transform flex-col overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
              <DialogTitle
                as="h3"
                class="mb-2 text-lg font-medium leading-6 text-gray-900">
                {{ props.ad ? 'Edit Ad' : 'Create Ad' }}
              </DialogTitle>

              <v-date-picker
                :min-date="new Date()"
                is-expanded
                mode="date"
                :attributes="attributes"
                @dayclick="onDayClick" />

              <div class="mt-4 flex flex-col gap-2 text-black">
                <div class="flex items-center">
                  <div>
                    from :
                    <strong>{{ getStartDate().toLocaleDateString() }}</strong>
                  </div>
                  <ArrowLongRightIcon class="mx-4 h-4 w-4" />
                  <div>
                    to :
                    <strong>{{ getEndDate().toLocaleDateString() }}</strong>
                  </div>
                </div>

                <FormKit
                  v-slot="{ state: { valid } }"
                  type="form"
                  :actions="false"
                  :submit-attrs="{
                    inputClass: 'im-on-the-button',
                    wrapperClass: 'im-on-the-wrapper',
                    outerClass: 'im-on-the-outer-wrapper',
                  }"
                  @submit="handleSubmit">
                  <FormKit
                    type="textarea"
                    placeholder="Content"
                    name="message"
                    label="Content of the Ad"
                    :value="props.ad ? props.ad.message : ''"
                    validation="required|length:1,255"
                    :validation-messages="{
                      length: 'Content must be between 1 and 255 characters',
                    }"
                    help="Maximum 255 characters" />
                  <FormKit
                    type="text"
                    placeholder="Link"
                    name="link"
                    label="Add a link to your ad"
                    :value="props.ad ? props.ad.link : ''"
                    validation="required|length:1,255|url"
                    :validation-messages="{
                      length: 'Content must be between 1 and 255 characters',
                      url: 'Please enter a valid URL',
                    }"
                    help="Add a valid URL, your link will be attached to the 'sponsored' button" />
                  <FormKit
                    type="number"
                    name="price"
                    :value="props.ad ? props.ad.price : 5"
                    placeholder="???"
                    :max="1000"
                    :min="1"
                    validation="required|max:1000|min:1"
                    :validation-messages="{
                      max: 'Maximum 1000???',
                      min: 'Minimum 1???',
                    }"
                    label="Price of the Ad"
                    help="The higher your offer, the more likely it is to appear, (max 1000???)" />

                  <div class="my-3 text-xs text-primary-400">
                    Once created, your ad will be pending validation. Your ad
                    will be processed as soon as possible by our teams.
                  </div>

                  <div class="mt-auto flex justify-end gap-2">
                    <button
                      type="button"
                      class="inline-flex justify-center rounded-md border border-transparent bg-slate-100 px-4 py-2 text-sm font-medium text-slate-900 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                      @click="closeModal">
                      Cancel
                    </button>
                    <button
                      type="submit"
                      :disabled="!valid"
                      class="inline-flex justify-center gap-2 rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                      <span>
                        {{ props.ad ? 'Edit' : 'Create' }}
                      </span>
                      <Spin :is-loading="isLoading" />
                    </button>
                  </div>
                </FormKit>
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
import { ArrowLongRightIcon } from '@heroicons/vue/20/solid';
import { ref } from 'vue';
import { useMutation, useQueryClient } from 'vue-query';
import { toast } from 'vue3-toastify';
import { createAd, updateAd } from '../../services/service.ads';
import { useUserStore } from '../../store/user';
import Spin from '../Loader/Spin.vue';

const userStore = useUserStore();
const { user } = userStore;

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  ad: {
    type: Object,
    default: null,
  },
});

const attributes = ref([
  {
    highlight: true,
    dates: props.ad
      ? [{ start: new Date(props.ad.startDate), span: 7 }]
      : [{ start: new Date(), span: 7 }],
  },
]);

function getStartDate() {
  return new Date(attributes.value[0].dates[0].start);
}

function getEndDate() {
  const date = getStartDate();
  date.setDate(date.getDate() + 7);
  return date;
}

function onDayClick(day) {
  const clickedDate = new Date(day.id);
  const today = new Date();
  if (
    clickedDate.getDate() < today.getDate() &&
    clickedDate.getMonth() <= today.getMonth() &&
    clickedDate.getFullYear() <= today.getFullYear()
  ) {
    return;
  }
  attributes.value[0].dates = [{ start: new Date(day.id), span: 7 }];
}

const emit = defineEmits(['close']);
const client = useQueryClient();
function closeModal() {
  emit('close');
}

const { isLoading, mutate: createAdMutation } = useMutation(
  (newAd) => createAd(newAd),
  {
    onSuccess: async () => {
      toast.success('Ad created');
      await client.invalidateQueries('ads');
      closeModal();
    },
    onError: (error) => {
      toast.error('Something went wrong');
    },
  }
);

const { mutate: updateAdMutation } = useMutation((newAd) => updateAd(newAd), {
  onSuccess: async () => {
    toast.success('Updated Ad');
    await client.invalidateQueries('ads');
    closeModal();
  },
  onError: (error) => {
    toast.error('Something went wrong');
  },
});

const handleSubmit = (values) => {
  if (!props.ad) {
    createAdMutation({
      ...values,
      price: +values.price,
      startDate: getStartDate(),
      endDate: getEndDate(),
      owner: `/api/users/${user.id}`,
    });
  } else {
    updateAdMutation({
      ...values,
      price: +values.price,
      startDate: getStartDate(),
      endDate: getEndDate(),
      adId: props.ad.id,
    });
  }
};
</script>
