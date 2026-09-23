<?php

namespace App\Http\Controllers;

use App\Models\Clinician;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ClinicianController extends Controller
{
    public function index()
    {
        $practice = $this->practice();

        $clinicians = $practice->clinicians()
            ->orderBy('status')
            ->orderBy('last_name')
            ->get()
            ->map(fn (Clinician $c) => [
                'id' => $c->id,
                'first_name' => $c->first_name,
                'last_name' => $c->last_name,
                'full_name' => $c->full_name,
                'specialty' => $c->specialty,
                'status' => $c->status,
                'current_caseload' => $c->current_caseload,
                'max_caseload' => $c->max_caseload,
                'utilization' => $c->utilization,
                'average_sessions_per_week' => (float) $c->average_sessions_per_week,
                'avg_billable_rate' => (float) $c->avg_billable_rate,
            ]);

        return Inertia::render('Clinicians/Index', [
            'clinicians' => $clinicians,
        ]);
    }

    public function create()
    {
        return Inertia::render('Clinicians/Create');
    }

    public function store(Request $request)
    {
        $practice = $this->practice();
        $data = $this->validated($request);

        $practice->clinicians()->create($data);

        return redirect()->route('clinicians.index')
            ->with('success', 'Clinician added.');
    }

    public function edit(Clinician $clinician)
    {
        $this->ensureBelongsToPractice($clinician);

        return Inertia::render('Clinicians/Edit', [
            'clinician' => $clinician,
        ]);
    }

    public function update(Request $request, Clinician $clinician)
    {
        $this->ensureBelongsToPractice($clinician);

        $clinician->update($this->validated($request));

        return redirect()->route('clinicians.index')
            ->with('success', 'Clinician updated.');
    }

    public function destroy(Clinician $clinician)
    {
        $this->ensureBelongsToPractice($clinician);

        $clinician->delete();

        return redirect()->route('clinicians.index')
            ->with('success', 'Clinician removed.');
    }

    private function practice()
    {
        $practice = auth()->user()?->practice;
        abort_unless($practice, Response::HTTP_FORBIDDEN, 'No practice associated with this account.');
        return $practice;
    }

    private function ensureBelongsToPractice(Clinician $clinician): void
    {
        abort_unless($clinician->practice_id === auth()->user()?->practice_id, Response::HTTP_NOT_FOUND);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'current_caseload' => ['required', 'integer', 'min:0', 'max:500'],
            'max_caseload' => ['required', 'integer', 'min:0', 'max:500'],
            'average_sessions_per_week' => ['required', 'numeric', 'min:0', 'max:80'],
            'avg_billable_rate' => ['required', 'numeric', 'min:0', 'max:10000'],
        ]);
    }
}
