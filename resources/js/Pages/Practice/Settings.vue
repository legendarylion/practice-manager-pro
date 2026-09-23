<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

// Form state for availability
const form = useForm({
    date: '',
    start_time: '',
    end_time: '',
});

// Refs for controlling pickers
const showDatePicker = ref(false);
const showStartTimePicker = ref(false);
const showEndTimePicker = ref(false);

// Submit the form
const submit = () => {
    form.post(route('availability.store'));
};
</script>

<template>
    <AppLayout>
        <Head title="Set Availability" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Set Your Availability
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Date Picker -->
                        <div>
                            <InputLabel for="date" value="Select Date" />
                            <v-menu
                                v-model="showDatePicker"
                                close-on-content-click
                                transition="scale-transition"
                                max-width="290"
                                min-width="290"
                            >
                                <template #activator="{ props }">
                                    <v-text-field
                                        v-bind="props"
                                        v-model="form.date"
                                        label="Select Date"
                                        readonly
                                        :error-messages="form.errors.date"
                                        outlined
                                        dense
                                        class="mt-1"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.date"
                                    @change="showDatePicker = false"
                                    :min="new Date().toISOString().split('T')[0]"
                                />
                            </v-menu>
                            <InputError :message="form.errors.date" class="mt-2" />
                        </div>

                        <!-- Start Time Picker -->
                        <div>
                            <InputLabel for="start_time" value="Start Time" />
                            <v-menu
                                v-model="showStartTimePicker"
                                close-on-content-click
                                transition="scale-transition"
                                max-width="290"
                                min-width="290"
                            >
                                <template #activator="{ props }">
                                    <v-text-field
                                        v-bind="props"
                                        v-model="form.start_time"
                                        label="Start Time"
                                        readonly
                                        :error-messages="form.errors.start_time"
                                        outlined
                                        dense
                                        class="mt-1"
                                    />
                                </template>
                                <v-time-picker
                                    v-model="form.start_time"
                                    @change="showStartTimePicker = false"
                                    format="24hr"
                                />
                            </v-menu>
                            <InputError :message="form.errors.start_time" class="mt-2" />
                        </div>

                        <!-- End Time Picker -->
                        <div>
                            <InputLabel for="end_time" value="End Time" />
                            <v-menu
                                v-model="showEndTimePicker"
                                close-on-content-click
                                transition="scale-transition"
                                max-width="290"
                                min-width="290"
                            >
                                <template #activator="{ props }">
                                    <v-text-field
                                        v-bind="props"
                                        v-model="form.end_time"
                                        label="End Time"
                                        readonly
                                        :error-messages="form.errors.end_time"
                                        outlined
                                        dense
                                        class="mt-1"
                                    />
                                </template>
                                <v-time-picker
                                    v-model="form.end_time"
                                    @change="showEndTimePicker = false"
                                    format="24hr"
                                />
                            </v-menu>
                            <InputError :message="form.errors.end_time" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Save Availability
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
