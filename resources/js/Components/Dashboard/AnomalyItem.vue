<script setup>
import { computed } from 'vue';

const props = defineProps({
    anomaly: { type: Object, required: true },
});

const dot = computed(() => ({
    critical: 'bg-rose-500',
    warning: 'bg-amber-500',
    info: 'bg-sky-500',
    positive: 'bg-emerald-500',
}[props.anomaly.severity] || 'bg-gray-400'));
</script>

<template>
    <div class="flex items-start gap-3 py-3 first:pt-0 last:pb-0 border-b last:border-b-0 border-gray-100 dark:border-gray-700">
        <span :class="['mt-1.5 w-2 h-2 rounded-full flex-shrink-0', dot]" />
        <div class="flex-1 min-w-0">
            <div class="flex items-baseline justify-between gap-2">
                <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ anomaly.title }}</h5>
                <span class="text-[11px] text-gray-400 dark:text-gray-500 flex-shrink-0">{{ anomaly.when }}</span>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">{{ anomaly.detail }}</p>
        </div>
    </div>
</template>
