<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    sub: { type: String, default: null },
    tone: { type: String, default: null }, // 'positive' | 'warning' | 'critical'
});

const toneClass = {
    positive: 'text-emerald-600 dark:text-emerald-400',
    warning: 'text-amber-600 dark:text-amber-400',
    critical: 'text-rose-600 dark:text-rose-400',
};
</script>

<template>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 px-4 py-3 flex flex-col gap-0.5">
        <div class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-medium">{{ label }}</div>
        <div :class="['text-xl font-semibold tabular-nums', toneClass[tone] || 'text-gray-900 dark:text-gray-100']">{{ value }}</div>
        <div v-if="sub" class="text-[11px] text-gray-500 dark:text-gray-400">{{ sub }}</div>
    </div>
</template>
