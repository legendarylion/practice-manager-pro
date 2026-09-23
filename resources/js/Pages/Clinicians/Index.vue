<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import UtilizationBar from '@/Components/Dashboard/UtilizationBar.vue';

const props = defineProps({
    clinicians: { type: Array, required: true },
});

const flash = computed(() => usePage().props.flash);

const money = (v) => '$' + Math.round(v || 0).toLocaleString();
const num = (v) => (v || 0).toFixed(1);

const totals = computed(() => {
    const active = props.clinicians.filter(c => c.status === 'active');
    const current = active.reduce((s, c) => s + c.current_caseload, 0);
    const max = active.reduce((s, c) => s + c.max_caseload, 0);
    return { active: active.length, current, max, util: max > 0 ? current / max : 0 };
});

const remove = (clinician) => {
    if (!confirm(`Remove ${clinician.full_name}? This cannot be undone.`)) return;
    router.delete(route('clinicians.destroy', clinician.id), { preserveScroll: true });
};
</script>

<template>
    <AppLayout title="Clinicians">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Clinicians</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ totals.active }} active · capacity {{ totals.current }}/{{ totals.max }} ({{ (totals.util * 100).toFixed(0) }}%)
                    </p>
                </div>
                <Link
                    :href="route('clinicians.create')"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium shadow-sm"
                >
                    + Add clinician
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
                                <th class="text-left font-medium px-5 py-3">Clinician</th>
                                <th class="text-left font-medium px-5 py-3">Specialty</th>
                                <th class="text-left font-medium px-5 py-3 w-1/3">Utilization</th>
                                <th class="text-right font-medium px-5 py-3">Sessions/wk</th>
                                <th class="text-right font-medium px-5 py-3">Rate</th>
                                <th class="text-right font-medium px-5 py-3">Status</th>
                                <th class="text-right font-medium px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="c in clinicians" :key="c.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ c.full_name }}</td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ c.specialty || '—' }}</td>
                                <td class="px-5 py-3">
                                    <UtilizationBar :value="c.utilization" :label="`${c.current_caseload}/${c.max_caseload}`" />
                                </td>
                                <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ num(c.average_sessions_per_week) }}</td>
                                <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ money(c.avg_billable_rate) }}</td>
                                <td class="px-5 py-3 text-right">
                                    <span
                                        :class="['text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full',
                                            c.status === 'active'
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-200'
                                                : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400']"
                                    >
                                        {{ c.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <Link :href="route('clinicians.edit', c.id)" class="text-sm text-sky-600 dark:text-sky-400 hover:underline">Edit</Link>
                                    <button @click="remove(c)" class="ml-3 text-sm text-rose-600 dark:text-rose-400 hover:underline">Remove</button>
                                </td>
                            </tr>
                            <tr v-if="!clinicians.length">
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No clinicians yet. <Link :href="route('clinicians.create')" class="text-sky-600 dark:text-sky-400 hover:underline">Add your first one</Link>.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
