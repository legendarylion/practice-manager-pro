<script setup>
defineProps({
    padded: { type: Boolean, default: true },
});
</script>

<template>
    <div
        :class="[
            'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700',
            padded ? 'p-6' : '',
        ]"
    >
        <slot />
    </div>
</template>
