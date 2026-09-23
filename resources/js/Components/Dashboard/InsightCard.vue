<script setup>
import { computed } from 'vue';

const props = defineProps({
    insight: { type: Object, required: true },
});

const styles = computed(() => {
    switch (props.insight.severity) {
        case 'critical': return {
            wrap: 'bg-rose-50 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/60',
            badge: 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-200',
            title: 'text-rose-900 dark:text-rose-200',
            body: 'text-gray-700 dark:text-rose-100/80',
            label: 'Critical',
        };
        case 'warning': return {
            wrap: 'bg-amber-50 border-amber-200 dark:bg-amber-950/40 dark:border-amber-900/60',
            badge: 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-200',
            title: 'text-amber-900 dark:text-amber-200',
            body: 'text-gray-700 dark:text-amber-100/80',
            label: 'Warning',
        };
        case 'positive': return {
            wrap: 'bg-emerald-50 border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-900/60',
            badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-200',
            title: 'text-emerald-900 dark:text-emerald-200',
            body: 'text-gray-700 dark:text-emerald-100/80',
            label: 'Good',
        };
        default: return {
            wrap: 'bg-sky-50 border-sky-200 dark:bg-sky-950/40 dark:border-sky-900/60',
            badge: 'bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-200',
            title: 'text-sky-900 dark:text-sky-200',
            body: 'text-gray-700 dark:text-sky-100/80',
            label: 'Info',
        };
    }
});
</script>

<template>
    <div :class="['rounded-xl border p-4 flex flex-col gap-2', styles.wrap]">
        <span :class="['text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full self-start', styles.badge]">
            {{ styles.label }}
        </span>
        <h4 :class="['text-sm font-semibold', styles.title]">{{ insight.title }}</h4>
        <p :class="['text-sm leading-relaxed', styles.body]">{{ insight.body }}</p>
    </div>
</template>
