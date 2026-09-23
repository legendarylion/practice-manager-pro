<script setup>
import AiBadge from './AiBadge.vue';

defineProps({
    rec: { type: Object, required: true },
});
</script>

<template>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 flex flex-col gap-3 hover:border-violet-300 dark:hover:border-violet-700 transition">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <AiBadge />
                <span class="text-[10px] uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">
                    {{ rec.category }}
                </span>
            </div>
            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/40 px-2 py-0.5 rounded-md tabular-nums">
                {{ rec.impact }}
            </span>
        </div>
        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-snug">{{ rec.title }}</h4>
        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ rec.body }}</p>
        <div class="flex items-center justify-between text-[11px] text-gray-400 dark:text-gray-500 pt-2 border-t border-gray-100 dark:border-gray-700">
            <span>Confidence {{ (rec.confidence * 100).toFixed(0) }}%</span>
            <div class="flex items-center gap-3">
                <button class="hover:text-gray-700 dark:hover:text-gray-200">Dismiss</button>
                <button class="text-violet-600 dark:text-violet-300 hover:underline font-medium">Take action →</button>
            </div>
        </div>
    </div>
</template>
