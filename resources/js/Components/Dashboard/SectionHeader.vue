<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: null },
});
</script>

<template>
    <div class="mb-4 flex items-end justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h3>
            <p v-if="subtitle" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ subtitle }}</p>
        </div>
        <slot name="aside" />
    </div>
</template>
