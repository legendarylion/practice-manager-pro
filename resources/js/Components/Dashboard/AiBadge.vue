<script setup>
defineProps({
    label: { type: String, default: 'AI' },
});
</script>

<template>
    <span class="inline-flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded-md bg-gradient-to-r from-violet-500/15 to-sky-500/15 text-violet-700 dark:text-violet-300 ring-1 ring-inset ring-violet-500/20">
        <svg class="w-2.5 h-2.5" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
            <path d="M8 1l1.6 4.4L14 7l-4.4 1.6L8 13l-1.6-4.4L2 7l4.4-1.6L8 1z"/>
        </svg>
        {{ label }}
    </span>
</template>
