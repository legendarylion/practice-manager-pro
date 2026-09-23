<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Form from './Form.vue';

defineProps({
    defaults: { type: Object, default: () => ({}) },
});
</script>

<template>
    <AppLayout title="Log a period">
        <template #header>
            <div>
                <Link :href="route('performance-logs.index')" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Back to logs</Link>
                <h2 class="mt-1 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Log a new period</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <Form :defaults="defaults" />
            </div>
        </div>
    </AppLayout>
</template>
