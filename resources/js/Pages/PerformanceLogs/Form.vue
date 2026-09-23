<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormField from '@/Components/Dashboard/FormField.vue';
import Card from '@/Components/Dashboard/Card.vue';

const props = defineProps({
    log: { type: Object, default: null },
    defaults: { type: Object, default: () => ({}) },
});

const isEdit = !!props.log;

const form = useForm({
    period_type: props.log?.period_type ?? props.defaults?.period_type ?? 'quarter',
    date_period: props.log?.date_period ?? props.defaults?.date_period ?? '',
    total_revenue: props.log?.total_revenue ?? 0,
    total_expenses: props.log?.total_expenses ?? 0,
    total_clients: props.log?.total_clients ?? 0,
    total_sessions: props.log?.total_sessions ?? 0,
    marketing_spend: props.log?.marketing_spend ?? null,
    payroll: props.log?.payroll ?? null,
    admin_costs: props.log?.admin_costs ?? null,
    cancellations: props.log?.cancellations ?? null,
    no_shows: props.log?.no_shows ?? null,
});

const showOptional = ref(
    !!(props.log?.marketing_spend || props.log?.payroll || props.log?.admin_costs || props.log?.cancellations || props.log?.no_shows)
);

const submit = () => {
    if (isEdit) {
        form.put(route('performance-logs.update', props.log.id));
    } else {
        form.post(route('performance-logs.store'));
    }
};

// Quick live preview of the headline KPIs as the user types.
const preview = computed(() => {
    const rev = Number(form.total_revenue) || 0;
    const exp = Number(form.total_expenses) || 0;
    const clients = Number(form.total_clients) || 0;
    const sessions = Number(form.total_sessions) || 0;
    const profit = rev - exp;
    return {
        profit,
        margin: rev > 0 ? profit / rev : 0,
        rate: sessions > 0 ? rev / sessions : 0,
        ltv: clients > 0 ? (sessions / clients) * (sessions > 0 ? rev / sessions : 0) : 0,
    };
});

const money = (v) => '$' + Math.round(v || 0).toLocaleString();
const pct = (v) => ((v || 0) * 100).toFixed(1) + '%';

const inputClass = 'w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-sky-400 focus:ring-sky-400 transition';
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <Card class="lg:col-span-2">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Period</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Quarterly snapshots are the recommended cadence.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="Period type" :error="form.errors.period_type">
                        <select v-model="form.period_type" :class="inputClass">
                            <option value="quarter">Quarter</option>
                            <option value="year">Year</option>
                        </select>
                    </FormField>
                    <FormField label="Period start date" :error="form.errors.date_period" hint="Use the first day of the quarter or year.">
                        <input v-model="form.date_period" type="date" :class="inputClass" required />
                    </FormField>
                </div>

                <div class="pt-2">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Core metrics</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">These four drive the dashboard.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="Total revenue ($)" :error="form.errors.total_revenue">
                        <input v-model.number="form.total_revenue" type="number" step="0.01" min="0" :class="inputClass" required />
                    </FormField>
                    <FormField label="Total expenses ($)" :error="form.errors.total_expenses">
                        <input v-model.number="form.total_expenses" type="number" step="0.01" min="0" :class="inputClass" required />
                    </FormField>
                    <FormField label="Total clients served" :error="form.errors.total_clients">
                        <input v-model.number="form.total_clients" type="number" min="0" :class="inputClass" required />
                    </FormField>
                    <FormField label="Total sessions completed" :error="form.errors.total_sessions">
                        <input v-model.number="form.total_sessions" type="number" min="0" :class="inputClass" required />
                    </FormField>
                </div>

                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button
                        type="button"
                        @click="showOptional = !showOptional"
                        class="text-xs font-medium text-sky-600 dark:text-sky-400 hover:underline"
                    >
                        {{ showOptional ? '− Hide optional details' : '+ Add optional details (payroll, marketing, no-shows…)' }}
                    </button>
                </div>

                <div v-if="showOptional" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField label="Marketing spend ($)" :error="form.errors.marketing_spend">
                        <input v-model.number="form.marketing_spend" type="number" step="0.01" min="0" :class="inputClass" />
                    </FormField>
                    <FormField label="Payroll ($)" :error="form.errors.payroll">
                        <input v-model.number="form.payroll" type="number" step="0.01" min="0" :class="inputClass" />
                    </FormField>
                    <FormField label="Admin costs ($)" :error="form.errors.admin_costs">
                        <input v-model.number="form.admin_costs" type="number" step="0.01" min="0" :class="inputClass" />
                    </FormField>
                    <FormField label="Cancellations" :error="form.errors.cancellations">
                        <input v-model.number="form.cancellations" type="number" min="0" :class="inputClass" />
                    </FormField>
                    <FormField label="No-shows" :error="form.errors.no_shows">
                        <input v-model.number="form.no_shows" type="number" min="0" :class="inputClass" />
                    </FormField>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <a :href="route('performance-logs.index')" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">Cancel</a>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium shadow-sm disabled:opacity-50"
                    >
                        {{ isEdit ? 'Save changes' : 'Save log' }}
                    </button>
                </div>
            </form>
        </Card>

        <!-- Live preview -->
        <Card>
            <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Live preview</h3>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 mb-4">Updates as you type.</p>
            <div class="space-y-4">
                <div>
                    <div class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Profit</div>
                    <div :class="['text-2xl font-semibold tabular-nums', preview.profit < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-gray-100']">
                        {{ money(preview.profit) }}
                    </div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Profit margin</div>
                    <div class="text-lg font-semibold text-gray-800 dark:text-gray-200 tabular-nums">{{ pct(preview.margin) }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Effective rate</div>
                    <div class="text-lg font-semibold text-gray-800 dark:text-gray-200 tabular-nums">{{ money(preview.rate) }} / session</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Implied client LTV</div>
                    <div class="text-lg font-semibold text-gray-800 dark:text-gray-200 tabular-nums">{{ money(preview.ltv) }}</div>
                </div>
            </div>
        </Card>
    </div>
</template>
