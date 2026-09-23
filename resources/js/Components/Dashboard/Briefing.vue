<script setup>
import AiBadge from './AiBadge.vue';

defineProps({
    greetingName: { type: String, required: true },
    briefing: { type: Object, required: true },
});
</script>

<template>
    <section
        class="relative overflow-hidden rounded-2xl border border-violet-500/20 dark:border-violet-400/20
               bg-gradient-to-br from-white via-violet-50/40 to-sky-50/40
               dark:from-gray-800 dark:via-violet-950/30 dark:to-sky-950/30
               shadow-sm p-6 sm:p-8"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <AiBadge label="Daily briefing" />
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ briefing.date }}</span>
                </div>
                <h2 class="mt-3 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-gray-50 leading-tight">
                    Good morning, {{ greetingName }}.
                </h2>
                <p class="mt-3 text-base sm:text-lg text-gray-700 dark:text-gray-200 max-w-3xl leading-relaxed">
                    {{ briefing.headline }}
                </p>
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-600 dark:text-gray-300 max-w-3xl leading-relaxed">
            {{ briefing.narrative }}
        </p>

        <div class="mt-6">
            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium mb-3">
                Today's priorities
            </div>
            <ol class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <li
                    v-for="(p, i) in briefing.priorities"
                    :key="i"
                    class="bg-white/70 dark:bg-gray-900/40 backdrop-blur-sm border border-gray-200/60 dark:border-gray-700/60 rounded-xl p-4 flex flex-col gap-2"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-violet-100 dark:bg-violet-900/60 text-violet-700 dark:text-violet-200 text-xs font-semibold">
                            {{ i + 1 }}
                        </span>
                        <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/40 px-2 py-0.5 rounded-md">
                            {{ p.impact_label }}
                        </span>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-snug">{{ p.title }}</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ p.detail }}</p>
                    <div class="mt-1 flex items-center justify-between text-[10px] text-gray-400 dark:text-gray-500">
                        <span>Confidence {{ (p.confidence * 100).toFixed(0) }}%</span>
                        <button class="text-violet-600 dark:text-violet-300 hover:underline font-medium">
                            Take action →
                        </button>
                    </div>
                </li>
            </ol>
        </div>
    </section>
</template>
