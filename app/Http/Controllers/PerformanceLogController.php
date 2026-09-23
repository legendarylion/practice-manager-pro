<?php

namespace App\Http\Controllers;

use App\Models\PerformanceLog;
use App\Services\KpiCalculator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class PerformanceLogController extends Controller
{
    public function index(KpiCalculator $kpi)
    {
        $practice = $this->practice();

        $logs = $practice->performanceLogs()->ordered()->get();

        $rows = $logs->map(function (PerformanceLog $log) use ($kpi) {
            $f = $kpi->financial($log);
            return [
                'id' => $log->id,
                'date_period' => $log->date_period?->format('Y-m-d'),
                'period_type' => $log->period_type,
                'label' => $this->periodLabel($log),
                'total_revenue' => $f['total_revenue'],
                'total_expenses' => $f['total_expenses'],
                'profit' => $f['profit'],
                'profit_margin' => $f['profit_margin'],
                'total_clients' => $f['total_clients'],
                'total_sessions' => $f['total_sessions'],
            ];
        })->reverse()->values();

        return Inertia::render('PerformanceLogs/Index', [
            'logs' => $rows,
        ]);
    }

    public function create()
    {
        return Inertia::render('PerformanceLogs/Create', [
            'defaults' => [
                'period_type' => 'quarter',
                'date_period' => now()->startOfQuarter()->format('Y-m-d'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $practice = $this->practice();
        $data = $this->validated($request);

        $practice->performanceLogs()->updateOrCreate(
            [
                'period_type' => $data['period_type'],
                'date_period' => $data['date_period'],
            ],
            $data
        );

        return redirect()->route('performance-logs.index')
            ->with('success', 'Performance log saved.');
    }

    public function edit(PerformanceLog $performanceLog)
    {
        $this->ensureBelongsToPractice($performanceLog);

        return Inertia::render('PerformanceLogs/Edit', [
            'log' => [
                ...$performanceLog->toArray(),
                'date_period' => $performanceLog->date_period?->format('Y-m-d'),
            ],
        ]);
    }

    public function update(Request $request, PerformanceLog $performanceLog)
    {
        $this->ensureBelongsToPractice($performanceLog);

        $performanceLog->update($this->validated($request));

        return redirect()->route('performance-logs.index')
            ->with('success', 'Performance log updated.');
    }

    public function destroy(PerformanceLog $performanceLog)
    {
        $this->ensureBelongsToPractice($performanceLog);

        $performanceLog->delete();

        return redirect()->route('performance-logs.index')
            ->with('success', 'Performance log deleted.');
    }

    private function practice()
    {
        $practice = auth()->user()?->practice;
        abort_unless($practice, Response::HTTP_FORBIDDEN, 'No practice associated with this account.');
        return $practice;
    }

    private function ensureBelongsToPractice(PerformanceLog $log): void
    {
        abort_unless($log->practice_id === auth()->user()?->practice_id, Response::HTTP_NOT_FOUND);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'period_type' => ['required', Rule::in(['quarter', 'year'])],
            'date_period' => ['required', 'date'],
            'total_revenue' => ['required', 'numeric', 'min:0'],
            'total_expenses' => ['required', 'numeric', 'min:0'],
            'total_clients' => ['required', 'integer', 'min:0'],
            'total_sessions' => ['required', 'integer', 'min:0'],
            'marketing_spend' => ['nullable', 'numeric', 'min:0'],
            'payroll' => ['nullable', 'numeric', 'min:0'],
            'admin_costs' => ['nullable', 'numeric', 'min:0'],
            'cancellations' => ['nullable', 'integer', 'min:0'],
            'no_shows' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function periodLabel(PerformanceLog $log): string
    {
        if ($log->period_type === 'year') {
            return $log->date_period->format('Y');
        }
        $q = (int) ceil($log->date_period->month / 3);
        return "Q{$q} " . $log->date_period->format('Y');
    }
}
