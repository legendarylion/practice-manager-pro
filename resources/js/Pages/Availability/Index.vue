<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Availability from '@/Components/Availability.vue';

const availabilityData = ref([
    { day: 'Monday', enabled: true, intervals: [{ start: '08:00 AM', end: '09:00 AM' }] },
    { day: 'Tuesday', enabled: true, intervals: [{ start: '08:00 AM', end: '09:00 AM' }] },
    { day: 'Wednesday', enabled: true, intervals: [{ start: '08:00 AM', end: '09:00 AM' }] },
    { day: 'Thursday', enabled: true, intervals: [{ start: '08:00 AM', end: '09:00 AM' }] },
    { day: 'Friday', enabled: true, intervals: [{ start: '08:00 AM', end: '09:00 AM' }] },
    { day: 'Saturday', enabled: false, intervals: [] },
    { day: 'Sunday', enabled: false, intervals: [] },
]);
</script>

<template>
    <AppLayout>
        <Head title="Availability" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
                            Set Your Availability
                        </h2>
                        <Availability
                            
                            :availability-data="availabilityData"
                            @update:availability-data="(updated) => availabilityData = updated"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>