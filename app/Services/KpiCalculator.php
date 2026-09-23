<?php

namespace App\Services;

use App\Models\Clinician;
use App\Models\PerformanceLog;
use Illuminate\Support\Collection;

/**
 * Pure calculation layer. Takes Eloquent collections / models in,
 * returns plain arrays out. No DB queries — controller decides what to load.
 */
class KpiCalculator
{
    public function scorecard(?PerformanceLog $latest, Collection $clinicians): array
    {
        $financial = $this->financial($latest);
        $capacity = $this->capacity($clinicians);
        $clientValue = $this->clientValue($latest);

        return array_merge($financial, $capacity, $clientValue);
    }

    public function financial(?PerformanceLog $log): array
    {
        $revenue = (float) ($log->total_revenue ?? 0);
        $expenses = (float) ($log->total_expenses ?? 0);
        $sessions = (int) ($log->total_sessions ?? 0);
        $clients = (int) ($log->total_clients ?? 0);

        $profit = $revenue - $expenses;
        $margin = $revenue > 0 ? $profit / $revenue : 0.0;
        $avgRate = $sessions > 0 ? $revenue / $sessions : 0.0;
        $sessionsPerClient = $clients > 0 ? $sessions / $clients : 0.0;
        $revenuePerClient = $clients > 0 ? $revenue / $clients : 0.0;
        $profitPerClient = $clients > 0 ? $profit / $clients : 0.0;

        return [
            'total_revenue' => $revenue,
            'total_expenses' => $expenses,
            'profit' => $profit,
            'profit_margin' => $margin,
            'avg_billable_rate' => $avgRate,
            'avg_sessions_per_client' => $sessionsPerClient,
            'revenue_per_client' => $revenuePerClient,
            'profit_per_client' => $profitPerClient,
            'total_clients' => $clients,
            'total_sessions' => $sessions,
        ];
    }

    public function capacity(Collection $clinicians): array
    {
        $active = $clinicians->where('status', 'active');
        $current = (int) $active->sum('current_caseload');
        $max = (int) $active->sum('max_caseload');
        $utilization = $max > 0 ? $current / $max : 0.0;
        $remaining = max(0, $max - $current);

        return [
            'current_caseload' => $current,
            'max_capacity' => $max,
            'utilization' => $utilization,
            'remaining_capacity' => $remaining,
            'active_clinicians' => $active->count(),
        ];
    }

    public function clientValue(?PerformanceLog $log): array
    {
        $sessions = (int) ($log->total_sessions ?? 0);
        $clients = (int) ($log->total_clients ?? 0);
        $revenue = (float) ($log->total_revenue ?? 0);

        $avgRate = $sessions > 0 ? $revenue / $sessions : 0.0;
        $sessionsPerClient = $clients > 0 ? $sessions / $clients : 0.0;

        return [
            'client_lifetime_value' => $avgRate * $sessionsPerClient,
        ];
    }

    /**
     * Estimated additional revenue if remaining capacity were filled.
     * Uses each clinician's own billable rate and weekly session pace,
     * projected against a configurable horizon (default: one quarter / 13 weeks).
     */
    public function revenuePotential(Collection $clinicians, int $weeks = 13): float
    {
        return (float) $clinicians
            ->where('status', 'active')
            ->reduce(function (float $carry, Clinician $c) use ($weeks) {
                $slack = max(0, $c->max_caseload - $c->current_caseload);
                if ($slack === 0 || $c->max_caseload === 0) {
                    return $carry;
                }
                // Slack as a fraction of capacity, scaled by their weekly pace and rate.
                $slackRatio = $slack / $c->max_caseload;
                $extraSessions = $slackRatio * $c->average_sessions_per_week * $weeks;
                return $carry + ($extraSessions * $c->avg_billable_rate);
            }, 0.0);
    }

    /**
     * Per-clinician breakdown for the Clinician Performance section.
     */
    public function clinicianBreakdown(Collection $clinicians): array
    {
        return $clinicians->map(fn (Clinician $c) => [
            'id' => $c->id,
            'name' => $c->full_name,
            'specialty' => $c->specialty,
            'status' => $c->status,
            'current_caseload' => $c->current_caseload,
            'max_caseload' => $c->max_caseload,
            'utilization' => $c->utilization,
            'avg_sessions_per_week' => (float) $c->average_sessions_per_week,
            'avg_billable_rate' => (float) $c->avg_billable_rate,
        ])->values()->all();
    }

    /**
     * Quarter-over-quarter trend series for sparklines + delta display.
     */
    public function trendSeries(Collection $logs): array
    {
        $logs = $logs->sortBy('date_period')->values();

        $series = $logs->map(function (PerformanceLog $log) {
            $f = $this->financial($log);
            return [
                'period' => $log->date_period?->format('Y-m-d'),
                'label' => $this->periodLabel($log),
                'revenue' => $f['total_revenue'],
                'expenses' => $f['total_expenses'],
                'profit' => $f['profit'],
                'profit_margin' => $f['profit_margin'],
                'revenue_per_client' => $f['revenue_per_client'],
                'avg_sessions_per_client' => $f['avg_sessions_per_client'],
            ];
        })->all();

        return $series;
    }

    public function delta(Collection $logs, string $key): ?float
    {
        if ($logs->count() < 2) {
            return null;
        }
        $sorted = $logs->sortBy('date_period')->values();
        $current = $this->financial($sorted->last());
        $previous = $this->financial($sorted[$sorted->count() - 2]);

        $prev = $previous[$key] ?? 0;
        $curr = $current[$key] ?? 0;

        if ($prev == 0) {
            return null;
        }
        return ($curr - $prev) / abs($prev);
    }

    private function periodLabel(PerformanceLog $log): string
    {
        if (! $log->date_period) {
            return '';
        }
        if ($log->period_type === 'year') {
            return $log->date_period->format('Y');
        }
        $q = (int) ceil($log->date_period->month / 3);
        return "Q{$q} " . $log->date_period->format('Y');
    }
}
