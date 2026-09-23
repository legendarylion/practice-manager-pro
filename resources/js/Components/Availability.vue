<script setup>
import { reactive, toRaw } from 'vue';

const emit = defineEmits(['update:availability-data']);

const props = defineProps({
    availabilityData: {
        type: Array,
        required: true,
        default: () => ([]) // Add a default value
    },
});

// Create a watcher to update localAvailabilityData when props change
const localAvailabilityData = reactive(props.availabilityData || []);

const emitUpdates = () => {
    emit('update:availability-data', toRaw(localAvailabilityData));
};

const generateTimeOptions = () => {
    return Array.from({ length: 24 * 4 }, (_, i) => {
        const hours = String(Math.floor(i / 4)).padStart(2, '0');
        const minutes = String((i % 4) * 15).padStart(2, '0');
        const ampm = +hours < 12 ? 'AM' : 'PM';
        const formattedHours = String((+hours % 12) || 12).padStart(2, '0');
        return `${formattedHours}:${minutes} ${ampm}`;
    });
};

const addInterval = (dayIndex) => {
    const day = localAvailabilityData[dayIndex];
    if (day.enabled) {
        const timeOptions = generateTimeOptions();
        const lastInterval = day.intervals[day.intervals.length - 1] || { end: '08:00 AM' };
        const nextStartIndex = Math.max(0, timeOptions.indexOf(lastInterval.end) + 1);
        const nextEndIndex = Math.min(nextStartIndex + 1, timeOptions.length - 1);

        day.intervals.push({
            start: timeOptions[nextStartIndex] || '08:00 AM',
            end: timeOptions[nextEndIndex] || '09:00 AM',
        });
        emitUpdates();
    }
};

const removeInterval = (dayIndex, intervalIndex) => {
    localAvailabilityData[dayIndex].intervals.splice(intervalIndex, 1);
    emitUpdates();
};

const toggleDay = (dayIndex) => {
    const day = localAvailabilityData[dayIndex];
    day.enabled = !day.enabled;
    if (!day.enabled) {
        day.intervals = [];
    }
    emitUpdates();
};
</script>

<template>
    <div class="space-y-6">
        <div
            v-for="(day, dayIndex) in localAvailabilityData"
            :key="dayIndex"
            class="space-y-4 border-b border-gray-300 dark:border-gray-700 pb-4"
        >
            <div
                class="flex items-center space-x-4 cursor-pointer"
                @click="toggleDay(dayIndex)"
            >
                <div class="w-1/5 flex items-center space-x-2">
                    <input
                        type="checkbox"
                        v-model="day.enabled"
                        class="form-checkbox h-5 w-5 text-indigo-600 dark:bg-gray-700 dark:border-gray-600"
                        @click.stop
                    />
                    <span class="text-gray-700 dark:text-gray-300">{{ day.day }}</span>
                </div>
            </div>

            <div v-if="day.enabled" class="flex flex-col space-y-4">
                <div
                    v-for="(interval, intervalIndex) in day.intervals"
                    :key="intervalIndex"
                    class="flex items-center space-x-4"
                >
                    <select
                        v-model="interval.start"
                        class="form-select block w-32 dark:bg-gray-700 dark:border-gray-600 text-white"
                    >
                        <option
                            v-for="option in generateTimeOptions()"
                            :key="option"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                    <span class="text-gray-700 dark:text-gray-300">to</span>
                    <select
                        v-model="interval.end"
                        class="form-select block w-32 dark:bg-gray-700 dark:border-gray-600 text-white"
                    >
                        <option
                            v-for="option in generateTimeOptions()"
                            :key="option"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                    <button
                        type="button"
                        class="py-2 px-4 w-24 text-sm font-semibold text-red-600 dark:text-red-400 hover:text-red-800 bg-red-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded shadow-sm"
                        @click.stop="removeInterval(dayIndex, intervalIndex)"
                    >
                        Remove
                    </button>
                </div>

                <div class="flex justify-start">
                    <button
                        type="button"
                        class="py-2 px-4 w-32 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 bg-indigo-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded shadow-sm"
                        @click.stop="addInterval(dayIndex)"
                    >
                        + Add Interval
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>