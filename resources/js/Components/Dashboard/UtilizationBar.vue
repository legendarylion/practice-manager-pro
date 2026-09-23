<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: { type: Number, required: true }, // 0..1
    label: { type: String, default: null },
    showPct: { type: Boolean, default: true },
});

const pct = computed(() => Math.max(0, Math.min(1, props.value)) * 100);
const tone = computed(() => {
    if (props.value >= 0.9) return 'bg-rose-400 dark:bg-rose-500';
    if (props.value >= 0.75) return 'bg-amber-400 dark:bg-amber-500';
    if (props.value >= 0.5) return 'bg-emerald-400 dark:bg-emerald-500';
    return 'bg-sky-400 dark:bg-sky-500';
});
</script>

<template>
    <div class="w-full">
        <div v-if="label || showPct" class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
            <span>{{ label }}</span>
            <span v-if="showPct" class="tabular-nums font-medium">{{ pct.toFixed(0) }}%</span>
        </div>
        <div class="h-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
            <div :class="['h-full rounded-full transition-all', tone]" :style="{ width: pct + '%' }" />
        </div>
    </div>
</template>
