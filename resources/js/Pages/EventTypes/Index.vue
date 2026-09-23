<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    eventTypes: Array
});
</script>

<template>
    <AppLayout>
        <Head title="Appointment Types" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Appointment Types
                </h2>
                <Link 
                    :href="route('event-types.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <PlusCircle class="w-4 h-4 mr-2" />
                    New Appointment Type
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div v-if="eventTypes.length === 0" class="p-6 text-center text-gray-500">
                        <h3 class="text-lg font-medium mb-2">No appointment types yet</h3>
                        <p class="mb-4">Create your first appointment type to start accepting bookings.</p>
                        <Link 
                            :href="route('event-types.create')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            <PlusCircle class="w-4 h-4 mr-2" />
                            Create Appointment Type
                        </Link>
                    </div>

                    <div v-else class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="type in eventTypes" :key="type.id" 
                                 class="border rounded-lg p-6 hover:shadow-lg transition-shadow"
                                 :class="{'opacity-50': !type.active}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div :style="{ backgroundColor: type.color }" 
                                             class="w-4 h-4 rounded-full mr-3">
                                        </div>
                                        <h3 class="font-semibold text-lg">{{ type.name }}</h3>
                                    </div>
                                    <Link 
                                        :href="route('event-types.edit', type.id)"
                                        class="text-gray-500 hover:text-gray-700"
                                    >
                                        Edit
                                    </Link>
                                </div>
                                
                                <p v-if="type.description" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ type.description }}
                                </p>
                                
                                <div class="mt-4 flex items-center text-sm text-gray-500">
                                    <Clock class="w-4 h-4 mr-2" />
                                    {{ type.duration }} minutes
                                </div>
                                
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <MapPin class="w-4 h-4 mr-2" />
                                    {{ type.location_type }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>