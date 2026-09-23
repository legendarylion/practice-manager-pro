<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Briefing from '@/Components/Dashboard/Briefing.vue';
import PulseStat from '@/Components/Dashboard/PulseStat.vue';
import FunnelStage from '@/Components/Dashboard/FunnelStage.vue';
import RecommendationCard from '@/Components/Dashboard/RecommendationCard.vue';
import AnomalyItem from '@/Components/Dashboard/AnomalyItem.vue';
import Sparkline from '@/Components/Dashboard/Sparkline.vue';
import UtilizationBar from '@/Components/Dashboard/UtilizationBar.vue';
import SectionHeader from '@/Components/Dashboard/SectionHeader.vue';
import AiBadge from '@/Components/Dashboard/AiBadge.vue';

const props = defineProps({
    user: { type: Object, required: true },
    briefing: { type: Object, required: true },
    pulse: { type: Object, required: true },
    goal: { type: Object, required: true },
    cashflow: { type: Object, required: true },
    funnel: { type: Array, required: true },
    clinicians_pl: { type: Array, required: true },
    payers: { type: Array, required: true },
    retention: { type: Object, required: true },
    no_shows: { type: Object, required: true },
    recommendations: { type: Array, required: true },
    anomalies: { type: Array, required: true },
    hire_analysis: { type: Object, required: true },
    is_prototype: { type: Boolean, default: false },
});

const money = (v) => '$' + Math.round(v || 0).toLocaleString();
const moneyK = (v) => '$' + (Math.round((v || 0) / 100) / 10).toFixed(1) + 'K';
const pct = (v, d = 0) => ((v || 0) * 100).toFixed(d) + '%';

const funnelMax = computed(() => Math.max(...props.funnel.map(s => s.count)));

const arBucketColor = (tone) => ({
    positive: 'bg-emerald-400 dark:bg-emerald-500',
    neutral: 'bg-sky-400 dark:bg-sky-500',
    warning: 'bg-amber-400 dark:bg-amber-500',
    critical: 'bg-rose-400 dark:bg-rose-500',
}[tone] || 'bg-gray-400');

const flagPill = (flag) => ({
    positive: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-200',
    warning: 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-200',
    opportunity: 'bg-violet-100 text-violet-700 dark:bg-violet-900/60 dark:text-violet-200',
}[flag] || 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300');

const flagLabel = (flag) => ({
    positive: 'Healthy',
    warning: 'Watch',
    opportunity: 'Lever',
}[flag] || '—');

const payerToneText = (tone) => ({
    positive: 'text-emerald-600 dark:text-emerald-400',
    warning: 'text-amber-600 dark:text-amber-400',
    critical: 'text-rose-600 dark:text-rose-400',
}[tone] || 'text-gray-700 dark:text-gray-200');

const cohortColor = (v) => {
    if (v === null || v === undefined) return 'bg-gray-100 dark:bg-gray-700/40 text-gray-400 dark:text-gray-500';
    if (v >= 0.7) return 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200';
    if (v >= 0.5) return 'bg-sky-100 dark:bg-sky-900/50 text-sky-800 dark:text-sky-200';
    if (v >= 0.35) return 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200';
    return 'bg-rose-100 dark:bg-rose-900/50 text-rose-800 dark:text-rose-200';
};
</script>

<template>
    <AppLayout title="Practice Health">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Practice Health</h2>
                <span v-if="is_prototype" class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-md bg-violet-100 text-violet-700 dark:bg-violet-900/60 dark:text-violet-200">
                    Prototype · mock data
                </span>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- 1. AI Briefing -->
                <Briefing :greeting-name="user.first_name" :briefing="briefing" />

                <!-- 2. Operational Pulse -->
                <section>
                    <SectionHeader title="Today's pulse" subtitle="Live operational signals across the practice." />
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <PulseStat label="Sessions today" :value="pulse.sessions_today.value" :sub="pulse.sessions_today.sub" />
                        <PulseStat label="No-shows yesterday" :value="pulse.no_shows_yesterday.value" :sub="pulse.no_shows_yesterday.sub" tone="warning" />
                        <PulseStat label="Cash arriving (7d)" :value="moneyK(pulse.cash_arriving_7d.value)" :sub="pulse.cash_arriving_7d.sub" />
                        <PulseStat label="At-risk clients" :value="pulse.at_risk_clients.value" :sub="pulse.at_risk_clients.sub" tone="warning" />
                        <PulseStat label="New inquiries" :value="pulse.new_inquiries_today.value" :sub="pulse.new_inquiries_today.sub" tone="positive" />
                    </div>
                </section>

                <!-- 3. Goal pacing -->
                <section class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                    <div class="flex flex-wrap items-end justify-between gap-4 mb-4">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">{{ goal.label }}</div>
                            <div class="mt-1 flex items-baseline gap-3">
                                <span class="text-3xl font-semibold text-gray-900 dark:text-gray-100 tabular-nums">{{ moneyK(goal.ytd) }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">of {{ moneyK(goal.target) }} target</span>
                                <span :class="['text-sm font-medium tabular-nums', goal.variance < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400']">
                                    {{ goal.variance < 0 ? '−' : '+' }}{{ moneyK(Math.abs(goal.variance)) }} vs. pace
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Required to hit goal</div>
                            <div class="text-lg font-semibold text-gray-800 dark:text-gray-200 tabular-nums mt-1">
                                {{ moneyK(goal.required_weekly) }}<span class="text-xs font-normal text-gray-500 dark:text-gray-400">/wk</span>
                            </div>
                            <div class="text-[11px] text-gray-500 dark:text-gray-400">Currently {{ moneyK(goal.current_weekly) }}/wk · {{ goal.days_remaining }} days left</div>
                        </div>
                    </div>
                    <div class="relative h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-sky-400 to-violet-400 dark:from-sky-500 dark:to-violet-500" :style="{ width: (goal.pct_complete * 100) + '%' }" />
                        <div class="absolute inset-y-0 w-px bg-gray-400 dark:bg-gray-300" :style="{ left: ((goal.on_pace / goal.target) * 100) + '%' }" :title="`On-pace marker: ${moneyK(goal.on_pace)}`" />
                    </div>
                    <div class="mt-2 flex justify-between text-[11px] text-gray-400 dark:text-gray-500">
                        <span>Jan</span><span>Apr</span><span>Jul</span><span>Oct</span><span>Dec</span>
                    </div>
                </section>

                <!-- 4. Cashflow + Funnel -->
                <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                        <SectionHeader title="Cash flow" subtitle="What's collected, what's outstanding, what's coming." />
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Collected (30d)</div>
                                <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tabular-nums mt-1">{{ money(cashflow.collected_30d) }}</div>
                                <div class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">▲ {{ pct(cashflow.collected_30d_delta, 1) }} vs. last 30d</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Outstanding (AR)</div>
                                <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tabular-nums mt-1">{{ money(cashflow.ar_total) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Across 4 aging buckets</div>
                            </div>
                        </div>
                        <div class="space-y-2 mb-5">
                            <div v-for="(bucket, i) in cashflow.ar_aging" :key="i" class="flex items-center gap-3 text-xs">
                                <span class="w-24 text-gray-500 dark:text-gray-400">{{ bucket.bucket }}</span>
                                <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div :class="['h-full rounded-full', arBucketColor(bucket.tone)]" :style="{ width: (bucket.pct * 100) + '%' }" />
                                </div>
                                <span class="w-16 text-right tabular-nums text-gray-700 dark:text-gray-200">{{ money(bucket.amount) }}</span>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">8-week projected inflows</div>
                                <div class="flex items-center gap-1">
                                    <AiBadge label="Forecast" />
                                </div>
                            </div>
                            <Sparkline :values="cashflow.projection_8w" :width="500" :height="60" stroke="#a78bfa" fill="rgba(167,139,250,0.12)" />
                            <div class="mt-2 flex justify-between text-[10px] text-gray-400 dark:text-gray-500">
                                <span>W1</span><span>W2</span><span>W3</span><span>W4</span><span>W5</span><span>W6</span><span>W7</span><span>W8</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                        <SectionHeader title="Client lifecycle" subtitle="Where prospective clients drop off this month.">
                            <template #aside>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Inquiry → retained: <span class="font-medium text-gray-700 dark:text-gray-200">{{ pct((funnel[funnel.length-1].count / funnel[0].count)) }}</span></span>
                            </template>
                        </SectionHeader>
                        <div class="space-y-4">
                            <FunnelStage v-for="(s, i) in funnel" :key="i" :stage="s" :max="funnelMax" />
                        </div>
                        <p class="mt-5 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            <AiBadge label="Insight" /> Biggest drop is consultation → first session (25% loss). Consider a 24-hour booking confirmation SMS — competitors who do this see 8–12 point conversion lifts.
                        </p>
                    </div>
                </section>

                <!-- 5. Clinician P&L -->
                <section>
                    <SectionHeader title="Clinician P&L" subtitle="Per-clinician contribution, not just utilization." />
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900/40 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="text-left font-medium px-5 py-3">Clinician</th>
                                    <th class="text-left font-medium px-5 py-3 w-1/5">Utilization</th>
                                    <th class="text-right font-medium px-5 py-3">Sessions</th>
                                    <th class="text-right font-medium px-5 py-3">Revenue</th>
                                    <th class="text-right font-medium px-5 py-3">Contribution</th>
                                    <th class="text-right font-medium px-5 py-3">No-show</th>
                                    <th class="text-left font-medium px-5 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="c in clinicians_pl" :key="c.name" class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="px-5 py-3">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ c.name }}</div>
                                        <div v-if="c.ai_note" class="text-[11px] text-violet-600 dark:text-violet-300 mt-0.5 flex items-center gap-1">
                                            <AiBadge label="AI" />
                                            <span>{{ c.ai_note }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3"><UtilizationBar :value="c.utilization" /></td>
                                    <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ c.sessions }}</td>
                                    <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ money(c.revenue) }}</td>
                                    <td class="px-5 py-3 text-right tabular-nums">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ money(c.contribution) }}</div>
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400">{{ pct(c.contribution_pct) }} margin</div>
                                    </td>
                                    <td :class="['px-5 py-3 text-right tabular-nums', c.no_show_rate > 0.12 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-700 dark:text-gray-200']">
                                        {{ pct(c.no_show_rate, 1) }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <span :class="['text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full', flagPill(c.flag)]">
                                            {{ flagLabel(c.flag) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 6. Payer mix + Retention/No-shows -->
                <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Payer mix -->
                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="p-6 pb-3">
                            <SectionHeader title="Payer mix profitability" subtitle="Net per session after collection rate." />
                        </div>
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900/40 text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="text-left font-medium px-6 py-2">Payer</th>
                                    <th class="text-right font-medium px-3 py-2">Sessions</th>
                                    <th class="text-right font-medium px-3 py-2">Collected</th>
                                    <th class="text-right font-medium px-3 py-2">Coll. %</th>
                                    <th class="text-right font-medium px-6 py-2">Net/Sess</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="p in payers" :key="p.name">
                                    <td class="px-6 py-2.5 font-medium text-gray-900 dark:text-gray-100">{{ p.name }}</td>
                                    <td class="px-3 py-2.5 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ p.sessions }}</td>
                                    <td class="px-3 py-2.5 text-right text-gray-700 dark:text-gray-200 tabular-nums">{{ money(p.collected) }}</td>
                                    <td :class="['px-3 py-2.5 text-right tabular-nums', payerToneText(p.tone)]">{{ pct(p.collection_rate, 0) }}</td>
                                    <td :class="['px-6 py-2.5 text-right tabular-nums font-medium', payerToneText(p.tone)]">{{ money(p.net_per_session) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            <AiBadge label="Flag" /> Aetna nets below your $115 break-even — see recommendation below.
                        </div>
                    </div>

                    <!-- Retention + No-shows stacked -->
                    <div class="space-y-6">
                        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                            <SectionHeader title="Retention cohorts" :subtitle="`Avg ${retention.avg_sessions_per_client} sessions/client · target ${retention.target_sessions_per_client}`" />
                            <table class="min-w-full text-xs">
                                <thead class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    <tr>
                                        <th class="text-left font-medium py-1.5">Cohort</th>
                                        <th class="text-center font-medium py-1.5">Wk 4</th>
                                        <th class="text-center font-medium py-1.5">Wk 8</th>
                                        <th class="text-center font-medium py-1.5">Wk 12</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="c in retention.cohorts" :key="c.label">
                                        <td class="py-1.5 font-medium text-gray-700 dark:text-gray-200">{{ c.label }}</td>
                                        <td class="py-1 text-center">
                                            <span :class="['inline-block w-14 py-1 rounded font-medium tabular-nums', cohortColor(c.wk4)]">{{ c.wk4 !== null ? pct(c.wk4) : '—' }}</span>
                                        </td>
                                        <td class="py-1 text-center">
                                            <span :class="['inline-block w-14 py-1 rounded font-medium tabular-nums', cohortColor(c.wk8)]">{{ c.wk8 !== null ? pct(c.wk8) : '—' }}</span>
                                        </td>
                                        <td class="py-1 text-center">
                                            <span :class="['inline-block w-14 py-1 rounded font-medium tabular-nums', cohortColor(c.wk12)]">{{ c.wk12 !== null ? pct(c.wk12) : '—' }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                            <SectionHeader title="No-show pulse" :subtitle="`Team rate ${pct(no_shows.rate, 1)} · target ${pct(no_shows.rate_target, 0)}`">
                                <template #aside>
                                    <span class="text-xs text-rose-600 dark:text-rose-400 font-medium tabular-nums">
                                        −{{ money(no_shows.lost_revenue_quarter) }} lost this qtr
                                    </span>
                                </template>
                            </SectionHeader>
                            <div class="space-y-2.5">
                                <div v-for="c in no_shows.by_clinician" :key="c.name" class="flex items-center gap-3 text-xs">
                                    <span class="w-20 text-gray-700 dark:text-gray-200">{{ c.name }}</span>
                                    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div :class="['h-full rounded-full', c.rate > 0.12 ? 'bg-rose-400 dark:bg-rose-500' : c.rate > 0.08 ? 'bg-amber-400 dark:bg-amber-500' : 'bg-emerald-400 dark:bg-emerald-500']" :style="{ width: Math.min(100, c.rate * 400) + '%' }" />
                                    </div>
                                    <span :class="['w-12 text-right tabular-nums', c.rate > 0.12 ? 'text-rose-600 dark:text-rose-400 font-medium' : 'text-gray-700 dark:text-gray-200']">
                                        {{ pct(c.rate, 1) }}
                                    </span>
                                </div>
                            </div>
                            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400 leading-relaxed flex items-start gap-1.5">
                                <AiBadge label="Pattern" /> <span>{{ no_shows.pattern }}</span>
                            </p>
                        </div>
                    </div>
                </section>

                <!-- 7. AI Recommendations -->
                <section>
                    <SectionHeader title="Recommendations" subtitle="Ranked by estimated impact and confidence.">
                        <template #aside>
                            <div class="flex items-center gap-2">
                                <AiBadge />
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ recommendations.length }} active</span>
                            </div>
                        </template>
                    </SectionHeader>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <RecommendationCard v-for="(r, i) in recommendations" :key="i" :rec="r" />
                    </div>
                </section>

                <!-- 8. Hire analysis + Anomalies -->
                <section class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <!-- Hire decision -->
                    <div class="lg:col-span-3 rounded-2xl border border-violet-500/20 dark:border-violet-400/20 bg-gradient-to-br from-white via-violet-50/30 to-sky-50/30 dark:from-gray-800 dark:via-violet-950/20 dark:to-sky-950/20 p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <AiBadge label="Hire model" />
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Updated daily</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Should you hire?</h3>
                                <p class="text-xl font-semibold text-violet-700 dark:text-violet-300 mt-2">{{ hire_analysis.recommendation }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed mb-5">{{ hire_analysis.rationale }}</p>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="rounded-xl bg-white/70 dark:bg-gray-900/40 border border-gray-200/60 dark:border-gray-700/60 p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Breakeven</div>
                                <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tabular-nums mt-1">{{ hire_analysis.breakeven_months }} mo</div>
                            </div>
                            <div class="rounded-xl bg-white/70 dark:bg-gray-900/40 border border-gray-200/60 dark:border-gray-700/60 p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Year-1 contribution</div>
                                <div class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400 tabular-nums mt-1">{{ money(hire_analysis.year_one_contribution) }}</div>
                            </div>
                        </div>
                        <div class="rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 px-3 py-2 text-xs text-amber-800 dark:text-amber-200 leading-relaxed">
                            <span class="font-semibold">Heads up:</span> {{ hire_analysis.risk }}
                        </div>
                    </div>

                    <!-- Anomalies -->
                    <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                        <SectionHeader title="Anomalies" subtitle="Worth a 5-minute look." />
                        <div>
                            <AnomalyItem v-for="(a, i) in anomalies" :key="i" :anomaly="a" />
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </AppLayout>
</template>
