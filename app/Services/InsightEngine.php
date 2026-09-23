<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Lightweight rules-based insight generator.
 *
 * Each rule returns either null (no insight) or an array:
 *   ['severity' => 'positive'|'warning'|'critical'|'info', 'title' => ..., 'body' => ...]
 *
 * Add new rules to the $rules array — they all receive the same context bag.
 */
class InsightEngine
{
    /** Tunable thresholds — pulled out so they're easy to expose as practice settings later. */
    public const HIGH_UTILIZATION = 0.90;
    public const LOW_UTILIZATION = 0.55;
    public const HEALTHY_MARGIN = 0.20;
    public const MARGIN_DROP_WARNING = -0.08;
    public const LOW_SESSIONS_PER_CLIENT = 4;
    public const UNEVEN_UTILIZATION_SPREAD = 0.40;

    public function generate(array $context): array
    {
        $insights = [];
        foreach ($this->rules() as $rule) {
            $result = $rule($context);
            if ($result) {
                $insights[] = $result;
            }
        }

        // Sort by severity weight, most urgent first.
        $weight = ['critical' => 0, 'warning' => 1, 'info' => 2, 'positive' => 3];
        usort($insights, fn ($a, $b) => ($weight[$a['severity']] ?? 9) <=> ($weight[$b['severity']] ?? 9));

        return $insights;
    }

    /** @return callable[] */
    private function rules(): array
    {
        return [
            $this->capacityRule(),
            $this->underutilizationRule(),
            $this->marginRule(),
            $this->marginTrendRule(),
            $this->revenuePerClientTrendRule(),
            $this->sessionsPerClientRule(),
            $this->unevenCaseloadRule(),
        ];
    }

    private function capacityRule(): callable
    {
        return function (array $ctx) {
            $u = $ctx['scorecard']['utilization'] ?? 0;
            if ($u >= self::HIGH_UTILIZATION) {
                return [
                    'severity' => 'warning',
                    'title' => 'Practice is near full capacity',
                    'body' => 'Your practice is operating at ' . $this->pct($u) . ' capacity. Consider hiring or expanding hours to absorb new demand.',
                ];
            }
            return null;
        };
    }

    private function underutilizationRule(): callable
    {
        return function (array $ctx) {
            $u = $ctx['scorecard']['utilization'] ?? 0;
            $potential = $ctx['revenue_potential'] ?? 0;
            if ($u > 0 && $u < self::LOW_UTILIZATION) {
                return [
                    'severity' => 'warning',
                    'title' => 'Underutilized capacity',
                    'body' => 'Practice utilization is ' . $this->pct($u) . '. Filling that slack could add ~' . $this->money($potential) . ' next quarter.',
                ];
            }
            return null;
        };
    }

    private function marginRule(): callable
    {
        return function (array $ctx) {
            $m = $ctx['scorecard']['profit_margin'] ?? 0;
            $revenue = $ctx['scorecard']['total_revenue'] ?? 0;
            if ($revenue <= 0) {
                return null;
            }
            if ($m < 0) {
                return [
                    'severity' => 'critical',
                    'title' => 'Practice is unprofitable this period',
                    'body' => 'Expenses exceeded revenue by ' . $this->money(abs($ctx['scorecard']['profit'] ?? 0)) . '. Review payroll, marketing spend, and rate structure.',
                ];
            }
            if ($m >= self::HEALTHY_MARGIN) {
                return [
                    'severity' => 'positive',
                    'title' => 'Healthy profit margin',
                    'body' => 'Profit margin sits at ' . $this->pct($m) . ' — strong room to reinvest in growth, marketing, or hiring.',
                ];
            }
            return null;
        };
    }

    private function marginTrendRule(): callable
    {
        return function (array $ctx) {
            $delta = $ctx['deltas']['profit_margin'] ?? null;
            if ($delta === null) {
                return null;
            }
            if ($delta <= self::MARGIN_DROP_WARNING) {
                return [
                    'severity' => 'warning',
                    'title' => 'Profit margin dropped',
                    'body' => 'Margin fell ' . $this->pct(abs($delta)) . ' vs. last period. Check expense growth and session pricing.',
                ];
            }
            if ($delta >= 0.05) {
                return [
                    'severity' => 'positive',
                    'title' => 'Margin trending up',
                    'body' => 'Profit margin improved ' . $this->pct($delta) . ' vs. last period.',
                ];
            }
            return null;
        };
    }

    private function revenuePerClientTrendRule(): callable
    {
        return function (array $ctx) {
            $delta = $ctx['deltas']['revenue_per_client'] ?? null;
            if ($delta === null) {
                return null;
            }
            if ($delta >= 0.05) {
                return [
                    'severity' => 'positive',
                    'title' => 'Revenue per client increased',
                    'body' => 'Average revenue per client is up ' . $this->pct($delta) . ' vs. last period.',
                ];
            }
            if ($delta <= -0.05) {
                return [
                    'severity' => 'info',
                    'title' => 'Revenue per client softening',
                    'body' => 'Revenue per client dropped ' . $this->pct(abs($delta)) . '. Could signal rate compression or insurance mix shift.',
                ];
            }
            return null;
        };
    }

    private function sessionsPerClientRule(): callable
    {
        return function (array $ctx) {
            $avg = $ctx['scorecard']['avg_sessions_per_client'] ?? 0;
            if ($avg > 0 && $avg < self::LOW_SESSIONS_PER_CLIENT) {
                return [
                    'severity' => 'info',
                    'title' => 'Possible early client churn',
                    'body' => 'Clients average only ' . number_format($avg, 1) . ' sessions. That may indicate intake friction or early drop-off.',
                ];
            }
            return null;
        };
    }

    private function unevenCaseloadRule(): callable
    {
        return function (array $ctx) {
            $clinicians = $ctx['clinicians'] ?? collect();
            $clinicians = $clinicians instanceof Collection ? $clinicians : collect($clinicians);
            $active = $clinicians->filter(fn ($c) => ($c['status'] ?? null) === 'active' && ($c['max_caseload'] ?? 0) > 0);
            if ($active->count() < 2) {
                return null;
            }
            $utilizations = $active->map(fn ($c) => $c['utilization'] ?? 0);
            $spread = $utilizations->max() - $utilizations->min();
            if ($spread >= self::UNEVEN_UTILIZATION_SPREAD) {
                return [
                    'severity' => 'info',
                    'title' => 'Clinician utilization is uneven',
                    'body' => 'The gap between your busiest and slowest clinician is ' . $this->pct($spread) . '. Rebalancing referrals could lift overall revenue.',
                ];
            }
            return null;
        };
    }

    private function pct(float $v): string
    {
        return number_format($v * 100, 1) . '%';
    }

    private function money(float $v): string
    {
        return '$' . number_format($v, 0);
    }
}
