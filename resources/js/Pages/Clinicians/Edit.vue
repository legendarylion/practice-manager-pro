<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Form from './Form.vue';

defineProps({
    clinician: { type: Object, required: true },
});
</script>

<template>
    <AppLayout title="Edit clinician">
        <template #header>
            <div>
                <Link :href="route('clinicians.index')" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Back to clinicians</Link>
                <h2 class="mt-1 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Edit {{ clinician.first_name }} {{ clinician.last_name }}</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <Form :clinician="clinician" />
            </div>
        </div>
    </AppLayout>
</template>
