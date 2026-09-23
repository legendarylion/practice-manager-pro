<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    logs: { type: Array, required: true },
});

const flash = computed(() => usePage().props.flash);

const money = (v) => '$' + Math.round(v || 0).toLocaleString();
const pct = (v) => ((v || 0) * 100).toFixed(1) + '%';

const remove = (log) => {
    if (!confirm(`Delete the performance log for ${log.label}?`)) return;
    router.delete(route('performance-logs.destroy', log.id), { preserveScroll: true });
};
</script>

<template>
    <AppLayout title="Performance logs">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Performance Logs</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Quarterly and annual snapshots that feed the dashboard.</p>
                </div>
                <Link
                    :href="route('performance-logs.create')"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium shadow-sm"
                >
                    + New log
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <div v-if="flash?.success" class="rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 px-4 py-3 text-sm">
                    {{ flash.success }}
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/40 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="text-left font-medium px-5 py-3">Period</th>
                                <th class="text-right font-medium px-5 py-3">Revenue</th>
                                <th class="text-right font-medium px-5 py-3">Expenses</th>
                                <th class="text-right font-medium px-5 py-3">Profit</th>
                                <th class="text-right font-medium px-5 py-3">Margin</th>
                                <th class="text-right font-medium px-5 py-3">Clients</th>
                                <th class="text-right font-medium px-5 py-3">Sessions</th>
                                <th class="text-right font-medium px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ log.label }}</td>
                                <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ money(log.total_revenue) }}</td>
                                <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ money(log.total_expenses) }}</td>
                                <td
                                    :class="['px-5 py-3 text-right tabular-nums font-medium',
                                        log.profit < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400']"
                                >
                                    {{ money(log.profit) }}
                                </td>
                                <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ pct(log.profit_margin) }}</td>
                                <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ log.total_clients }}</td>
                                <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ log.total_sessions }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <Link :href="route('performance-logs.edit', log.id)" class="text-sm text-sky-600 dark:text-sky-400 hover:underline">Edit</Link>
                                    <button @click="remove(log)" class="ml-3 text-sm text-rose-600 dark:text-rose-400 hover:underline">Delete</button>
                                </td>
                            </tr>
                            <tr v-if="!logs.length">
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No performance logs yet. <Link :href="route('performance-logs.create')" class="text-sky-600 dark:text-sky-400 hover:underline">Log your first quarter</Link>.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
