<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    sublabel: { type: String, default: null },
    delta: { type: Number, default: null },
    invertDelta: { type: Boolean, default: false },
    accent: { type: String, default: null }, // 'positive' | 'warning' | 'critical'
});

const deltaPct = computed(() => {
    if (props.delta === null || props.delta === undefined) return null;
    return (props.delta * 100).toFixed(1);
});

const deltaTone = computed(() => {
    if (props.delta === null || props.delta === undefined) return 'neutral';
    const positive = props.invertDelta ? props.delta < 0 : props.delta > 0;
    if (Math.abs(props.delta) < 0.005) return 'neutral';
    return positive ? 'positive' : 'negative';
});

const toneClasses = {
    positive: 'text-emerald-600 bg-emerald-50 dark:text-emerald-300 dark:bg-emerald-900/40',
    negative: 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-rose-900/40',
    neutral: 'text-gray-500 bg-gray-50 dark:text-gray-400 dark:bg-gray-700/60',
};

const accentBorder = computed(() => {
    switch (props.accent) {
        case 'positive': return 'border-l-4 border-emerald-400 dark:border-emerald-500';
        case 'warning': return 'border-l-4 border-amber-400 dark:border-amber-500';
        case 'critical': return 'border-l-4 border-rose-400 dark:border-rose-500';
        default: return '';
    }
});
</script>

<template>
    <div
        :class="[
            'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col gap-2',
            accentBorder
        ]"
    >
        <div class="flex items-start justify-between">
            <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">{{ label }}</span>
            <span
                v-if="deltaPct !== null"
                :class="['text-xs font-medium px-1.5 py-0.5 rounded', toneClasses[deltaTone]]"
            >
                {{ deltaTone === 'positive' ? '▲' : deltaTone === 'negative' ? '▼' : '–' }}
                {{ Math.abs(deltaPct) }}%
            </span>
        </div>
        <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tabular-nums">{{ value }}</div>
        <div v-if="sublabel" class="text-xs text-gray-500 dark:text-gray-400">{{ sublabel }}</div>
    </div>
</template>
