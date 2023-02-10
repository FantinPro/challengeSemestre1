<template>
  <div class="w-full p-8">
    <h1 class="mb-4 text-4xl">Statistics</h1>
    <Select
      class="w-96"
      title="Select date range"
      :list="filtersDates"
      :selected-value="selectedDate"
      @update:model-value="updateDate" />
    <div class="mt-12 flex flex-wrap justify-center gap-4">
      <CardStats
        v-for="stat in stats"
        :key="stat"
        :unit="stat.unit"
        :value="stat.values.current"
        :diff="stat.values.diff"
        :title="stat.key" />

      <template v-if="isLoading">
        <div v-for="index in 4" :key="index" class="w-[160px]">
          <div
            class="relative h-28 space-y-5 overflow-hidden rounded-2xl bg-white/5 p-5 shadow-xl shadow-black/5 before:absolute before:inset-0 before:-translate-x-full before:-skew-x-12 before:animate-[shimmer_2s_infinite] before:border-t before:border-white/10 before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent">
            <div class="space-y-2">
              <div class="h-6 w-4/5 rounded-lg bg-white/10"></div>
              <div class="flex items-end gap-3">
                <div class="h-8 w-2/5 rounded-lg bg-white/5"></div>
                <div class="h-5 w-12 rounded-lg bg-white/5"></div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>
<script setup>
import { useQuery } from 'vue-query';
import CardStats from '../components/CardStats/CardStats.vue';
import { getStats } from '../services/service.stats';
import { sub, endOfDay, startOfDay } from 'date-fns';
import Select from '../components/Select/Select.vue';
import { ref } from 'vue';

const filtersDates = [
  {
    display: 'Today',
    value: {
      current: {
        startDate: startOfDay(new Date()),
        endDate: endOfDay(new Date()),
      },
      previous: {
        startDate: startOfDay(sub(new Date(), { days: 1 })),
        endDate: endOfDay(sub(new Date(), { days: 1 })),
      },
    },
  },
  {
    display: '1 week',
    value: {
      current: {
        startDate: startOfDay(sub(new Date(), { days: 7 })),
        endDate: endOfDay(new Date()),
      },
      previous: {
        startDate: startOfDay(sub(new Date(), { days: 14 })),
        endDate: endOfDay(sub(new Date(), { days: 7 })),
      },
    },
  },
  {
    display: '1 month',
    value: {
      current: {
        startDate: startOfDay(sub(new Date(), { days: 30 })),
        endDate: endOfDay(new Date()),
      },
      previous: {
        startDate: startOfDay(sub(new Date(), { days: 60 })),
        endDate: endOfDay(sub(new Date(), { days: 30 })),
      },
    },
  },
  {
    display: 'All',
    value: {
      current: {
        startDate: null,
        endDate: null
      },
      previous: {
        startDate: null,
        endDate: null,
      },
    },
  },
];

const selectedDate = ref(filtersDates[0]);

const updateDate = (value) => {
  selectedDate.value = value;
};

const stats = ref([]);

const { isLoading } = useQuery({
  queryKey: ['stats', selectedDate],
  queryFn: () =>
    Promise.all([
      getStats(selectedDate.value.value.current),
      getStats(selectedDate.value.value.previous),
    ]),
  keepPreviousData: true,
  onSuccess: (data) => {
    const [current, previous] = data;
    const res = [];
    for (const key of Object.keys(current)) {
      res.push({
        key: key,
        unit: key === 'amountEarned' ? '$' : '',
        values: {
          current: current[key],
          diff: current[key] - previous[key],
        },
      });
    }
    stats.value = res;
  },
});
</script>
