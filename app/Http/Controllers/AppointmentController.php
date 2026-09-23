<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\EventType;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = auth()->user()->practice->appointments()->with('eventType')->latest()->get();

        return inertia('Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    public function create()
    {
        $eventTypes = auth()->user()->practice->eventTypes()->where('active', true)->get();

        return inertia('Appointments/Create', [
            'eventTypes' => $eventTypes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_type_id' => ['required', 'exists:event_types,id'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
        ]);

        $appointment = Appointment::create([
            ...$validated,
            'practice_id' => auth()->user()->practice_id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment created successfully.');
    }
}
