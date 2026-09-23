<script setup>
import { computed } from 'vue';

const props = defineProps({
    stage: { type: Object, required: true },
    max: { type: Number, required: true },
});

const width = computed(() => `${Math.max(8, (props.stage.count / props.max) * 100)}%`);
</script>

<template>
    <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between text-xs">
            <span class="font-medium text-gray-800 dark:text-gray-200">{{ stage.stage }}</span>
            <span class="text-gray-500 dark:text-gray-400 tabular-nums">
                {{ stage.count }}
                <span v-if="stage.conversion !== null" class="ml-1 text-[10px] text-gray-400 dark:text-gray-500">
                    ({{ (stage.conversion * 100).toFixed(0) }}% conv)
                </span>
            </span>
        </div>
        <div class="h-7 bg-gray-100 dark:bg-gray-700/40 rounded-md overflow-hidden">
            <div
                class="h-full rounded-md bg-gradient-to-r from-sky-400 to-violet-400 dark:from-sky-500 dark:to-violet-500 transition-all"
                :style="{ width }"
            />
        </div>
        <p v-if="stage.source_top" class="text-[10px] text-gray-400 dark:text-gray-500">Top source: {{ stage.source_top }}</p>
    </div>
</template>
