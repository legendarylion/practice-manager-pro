<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * PROTOTYPE DASHBOARD.
 *
 * This controller returns hard-coded mock data so we can prototype what the
 * dashboard would look like if the application captured intake funnel data,
 * payer mix, AR aging, no-shows, retention cohorts, and had an AI layer
 * generating briefings and recommendations.
 *
 * It intentionally does NOT read from the database. Once the real underlying
 * data exists (see roadmap), this controller should be replaced with the
 * service-driven version we had previously.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Dashboard', [
            'user' => [
                'first_name' => explode(' ', $request->user()->name)[0] ?? 'there',
            ],
            'briefing' => $this->briefing(),
            'pulse' => $this->pulse(),
            'goal' => $this->goal(),
            'cashflow' => $this->cashflow(),
            'funnel' => $this->funnel(),
            'clinicians_pl' => $this->cliniciansPl(),
            'payers' => $this->payers(),
            'retention' => $this->retention(),
            'no_shows' => $this->noShows(),
            'recommendations' => $this->recommendations(),
            'anomalies' => $this->anomalies(),
            'hire_analysis' => $this->hireAnalysis(),
            'is_prototype' => true,
        ]);
    }

    private function briefing(): array
    {
        return [
            'date' => now()->format('l, F j'),
            'headline' => "You're $14K behind Q2 pace — but margin compression is the bigger story.",
            'narrative' => "Revenue is tracking within 4% of plan, but profit margin slipped from 24% to 17% this quarter. The driver is payer mix: BCBS now makes up 38% of sessions (up from 22%), and your effective rate dropped from \$172 to \$158. Casey is your underutilized lever — 45% utilization with the highest private-pay mix on the team. Three concrete moves below could close most of the gap before quarter end.",
            'priorities' => [
                [
                    'title' => "Rebalance Casey's caseload toward private pay",
                    'detail' => "Move 4 BCBS clients from Morgan (98% utilization, burnout risk) to Casey (45% utilization). Casey converts BCBS to private pay at 2x your team average.",
                    'impact_label' => '+$2,800/mo',
                    'confidence' => 0.86,
                ],
                [
                    'title' => 'Re-engage 12 at-risk clients this week',
                    'detail' => '12 clients missed 2+ sessions and have no future booking. AI-drafted re-engagement messages are ready in your inbox — historical reactivation rate on similar segments is 38%.',
                    'impact_label' => '+$5.4K likely recovery',
                    'confidence' => 0.72,
                ],
                [
                    'title' => "Decline Aetna's proposed 4% rate cut",
                    'detail' => "Aetna sessions already net \$89 — below your $115 break-even. Accepting their proposal makes Aetna actively unprofitable. Three of your four Aetna clients have private-pay flexibility per intake notes.",
                    'impact_label' => 'Avoid −$1,200/mo erosion',
                    'confidence' => 0.91,
                ],
            ],
        ];
    }

    private function pulse(): array
    {
        return [
            'sessions_today' => ['value' => 28, 'sub' => 'of 34 scheduled'],
            'no_shows_yesterday' => ['value' => 3, 'sub' => '11% rate · above 8% target'],
            'cash_arriving_7d' => ['value' => 18400, 'sub' => 'across 6 payers'],
            'at_risk_clients' => ['value' => 12, 'sub' => 'missed 2+ sessions'],
            'new_inquiries_today' => ['value' => 4, 'sub' => '2 above daily avg'],
        ];
    }

    private function goal(): array
    {
        return [
            'label' => '2026 Revenue Target',
            'target' => 850000,
            'ytd' => 312000,
            'on_pace' => 326000,
            'variance' => -14000,
            'pct_complete' => 312000 / 850000,
            'days_remaining' => 224,
            'required_weekly' => 24000,
            'current_weekly' => 22300,
        ];
    }

    private function cashflow(): array
    {
        return [
            'collected_30d' => 67200,
            'collected_30d_delta' => 0.08,
            'ar_total' => 42100,
            'ar_aging' => [
                ['bucket' => '0–30 days', 'amount' => 22400, 'pct' => 0.53, 'tone' => 'positive'],
                ['bucket' => '31–60 days', 'amount' => 12800, 'pct' => 0.30, 'tone' => 'neutral'],
                ['bucket' => '61–90 days', 'amount' => 4900, 'pct' => 0.12, 'tone' => 'warning'],
                ['bucket' => '90+ days', 'amount' => 2000, 'pct' => 0.05, 'tone' => 'critical'],
            ],
            // Weekly projected inflows for next 8 weeks (in thousands for sparkline).
            'projection_8w' => [16.8, 18.4, 22.1, 19.7, 21.5, 24.0, 23.2, 25.6],
        ];
    }

    private function funnel(): array
    {
        return [
            ['stage' => 'Inquiries', 'count' => 42, 'conversion' => null, 'source_top' => 'Psychology Today (38%)'],
            ['stage' => 'Consultations booked', 'count' => 28, 'conversion' => 0.67, 'source_top' => null],
            ['stage' => 'First sessions', 'count' => 21, 'conversion' => 0.75, 'source_top' => null],
            ['stage' => 'Retained (4+ sessions)', 'count' => 14, 'conversion' => 0.67, 'source_top' => null],
        ];
    }

    private function cliniciansPl(): array
    {
        return [
            [
                'name' => 'Alex Rivera',
                'sessions' => 280,
                'revenue' => 46200,
                'comp_paid' => 32340,
                'contribution' => 13860,
                'contribution_pct' => 0.30,
                'utilization' => 0.88,
                'no_show_rate' => 0.18,
                'flag' => 'warning',
                'ai_note' => 'No-show rate climbing — investigate Friday slots',
            ],
            [
                'name' => 'Jordan Kim',
                'sessions' => 224,
                'revenue' => 40320,
                'comp_paid' => 24192,
                'contribution' => 16128,
                'contribution_pct' => 0.40,
                'utilization' => 0.67,
                'no_show_rate' => 0.09,
                'flag' => 'positive',
                'ai_note' => null,
            ],
            [
                'name' => 'Morgan Patel',
                'sessions' => 304,
                'revenue' => 60800,
                'comp_paid' => 36480,
                'contribution' => 24320,
                'contribution_pct' => 0.40,
                'utilization' => 0.96,
                'no_show_rate' => 0.06,
                'flag' => 'warning',
                'ai_note' => 'Sustained >95% for 11 weeks — burnout risk',
            ],
            [
                'name' => 'Casey Nguyen',
                'sessions' => 122,
                'revenue' => 24400,
                'comp_paid' => 14640,
                'contribution' => 9760,
                'contribution_pct' => 0.40,
                'utilization' => 0.45,
                'no_show_rate' => 0.07,
                'flag' => 'opportunity',
                'ai_note' => 'Highest private-pay conversion on team — fill capacity',
            ],
        ];
    }

    private function payers(): array
    {
        return [
            ['name' => 'Private Pay', 'sessions' => 312, 'billed' => 62400, 'collected' => 62400, 'collection_rate' => 1.00, 'net_per_session' => 200, 'tone' => 'positive'],
            ['name' => 'BCBS', 'sessions' => 348, 'billed' => 69600, 'collected' => 41760, 'collection_rate' => 0.60, 'net_per_session' => 120, 'tone' => 'neutral'],
            ['name' => 'Cigna', 'sessions' => 96, 'billed' => 19200, 'collected' => 13440, 'collection_rate' => 0.70, 'net_per_session' => 140, 'tone' => 'neutral'],
            ['name' => 'Aetna', 'sessions' => 88, 'billed' => 17600, 'collected' => 7832, 'collection_rate' => 0.445, 'net_per_session' => 89, 'tone' => 'critical'],
            ['name' => 'United', 'sessions' => 56, 'billed' => 11200, 'collected' => 6720, 'collection_rate' => 0.60, 'net_per_session' => 120, 'tone' => 'neutral'],
            ['name' => 'EAP / Other', 'sessions' => 30, 'billed' => 4500, 'collected' => 3825, 'collection_rate' => 0.85, 'net_per_session' => 128, 'tone' => 'neutral'],
        ];
    }

    private function retention(): array
    {
        return [
            'avg_sessions_per_client' => 8.4,
            'target_sessions_per_client' => 12,
            'cohorts' => [
                ['label' => 'Jan', 'wk4' => 0.82, 'wk8' => 0.61, 'wk12' => 0.44],
                ['label' => 'Feb', 'wk4' => 0.78, 'wk8' => 0.58, 'wk12' => 0.41],
                ['label' => 'Mar', 'wk4' => 0.84, 'wk8' => 0.63, 'wk12' => 0.47],
                ['label' => 'Apr', 'wk4' => 0.80, 'wk8' => 0.55, 'wk12' => null],
                ['label' => 'May', 'wk4' => 0.86, 'wk8' => null, 'wk12' => null],
            ],
        ];
    }

    private function noShows(): array
    {
        return [
            'rate' => 0.12,
            'rate_target' => 0.08,
            'rate_delta' => 0.03,
            'lost_revenue_quarter' => 8400,
            'by_clinician' => [
                ['name' => 'Morgan', 'rate' => 0.06, 'sessions' => 304],
                ['name' => 'Casey', 'rate' => 0.07, 'sessions' => 122],
                ['name' => 'Jordan', 'rate' => 0.09, 'sessions' => 224],
                ['name' => 'Alex', 'rate' => 0.18, 'sessions' => 280],
            ],
            'pattern' => 'Friday afternoons account for 47% of no-shows team-wide.',
        ];
    }

    private function recommendations(): array
    {
        return [
            [
                'category' => 'Pricing',
                'title' => 'Raise private-pay rate from $200 to $215',
                'body' => 'Your private-pay clients have 84% retention vs. 51% for insurance — they are not rate-sensitive. A $15 increase applied to existing clients would add an estimated $4,680/quarter with minimal expected churn.',
                'impact' => '+$4.7K/qtr',
                'confidence' => 0.78,
            ],
            [
                'category' => 'Operations',
                'title' => 'Move Friday-afternoon sessions to teletherapy by default',
                'body' => '47% of no-shows happen Friday afternoons in-office. Cohort data shows teletherapy no-show rate is 4 points lower. Defaulting Friday afternoons to virtual could recover ~$1,800/quarter.',
                'impact' => '+$1.8K/qtr',
                'confidence' => 0.69,
            ],
            [
                'category' => 'Capacity',
                'title' => 'Fill 4 of Casey’s open slots from your waitlist',
                'body' => 'Your waitlist has 11 names, 7 of whom marked "private pay" on intake. Casey has 12 open slots and your team’s highest private-pay conversion rate.',
                'impact' => '+$6.4K/qtr',
                'confidence' => 0.88,
            ],
            [
                'category' => 'Payer',
                'title' => 'Drop Aetna at next contract renewal (Sept 2026)',
                'body' => 'Aetna nets $89/session against your $115 break-even. Only 4 of 88 Aetna sessions came from clients without an alternative. Net-net release frees capacity for higher-yield payers.',
                'impact' => '+$2.1K/qtr',
                'confidence' => 0.83,
            ],
        ];
    }

    private function anomalies(): array
    {
        return [
            ['severity' => 'warning', 'title' => "Alex's no-show rate doubled in 4 weeks", 'detail' => 'From 9% to 18%. Concentrated on Friday afternoons.', 'when' => '4 weeks'],
            ['severity' => 'critical', 'title' => 'Aetna collections dropped 22% MoM', 'detail' => 'Three claims denied for credentialing issue — your NPI expired April 30.', 'when' => '2 weeks'],
            ['severity' => 'info', 'title' => 'Psychology Today referrals up 31%', 'detail' => 'May ad spend bump appears to be paying off — quality of leads is also up.', 'when' => 'this month'],
            ['severity' => 'warning', 'title' => '12 clients without next booking', 'detail' => '2+ weeks since last session, no upcoming. AI re-engagement queue ready.', 'when' => 'this week'],
        ];
    }

    private function hireAnalysis(): array
    {
        return [
            'recommendation' => 'Hire 1 part-time clinician (16–20 sessions/wk)',
            'rationale' => "You have 23 names on your waitlist and Morgan is at 96% utilization for 11 straight weeks. A part-time hire at 70/30 split, given current private-pay mix, breaks even in ~4 months and contributes an estimated $28,500 in year-one margin.",
            'breakeven_months' => 4.2,
            'year_one_contribution' => 28500,
            'risk' => 'Credentialing for BCBS averages 90 days — start now even if hire is deferred.',
        ];
    }
}
