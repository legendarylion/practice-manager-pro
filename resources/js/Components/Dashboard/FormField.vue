<script setup>
defineProps({
    label: { type: String, required: true },
    error: { type: String, default: null },
    hint: { type: String, default: null },
    for: { type: String, default: null },
});
</script>

<template>
    <div class="flex flex-col gap-1">
        <label :for="props?.for" class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ label }}</label>
        <slot />
        <p v-if="hint && !error" class="text-xs text-gray-400 dark:text-gray-500">{{ hint }}</p>
        <p v-if="error" class="text-xs text-rose-600 dark:text-rose-400">{{ error }}</p>
    </div>
</template>
