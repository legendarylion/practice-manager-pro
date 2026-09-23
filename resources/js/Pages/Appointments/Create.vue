<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    eventTypes: Array,
});

const form = useForm({
    event_type_id: '',
    start_time: '',
    end_time: '',
});

const submit = () => {
    form.post(route('appointments.store'));
};
</script>

<template>
    <AppLayout>
        <Head title="Create Appointment" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create Appointment
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <!-- Event Type -->
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="event_type_id" value="Event Type" />
                                <select
                                    id="event_type_id"
                                    v-model="form.event_type_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="" disabled>Select an event type</option>
                                    <option v-for="event in eventTypes" :key="event.id" :value="event.id">
                                        {{ event.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.event_type_id" class="mt-2" />
                            </div>

                            <!-- Start Time -->
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="start_time" value="Start Time" />
                                <TextInput
                                    id="start_time"
                                    v-model="form.start_time"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.start_time" class="mt-2" />
                            </div>

                            <!-- End Time -->
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel for="end_time" value="End Time" />
                                <TextInput
                                    id="end_time"
                                    v-model="form.end_time"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.end_time" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Create Appointment
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
