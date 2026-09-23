<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index()
    {
        $availability = auth()->user()->availability;

        return inertia('Availability/Index', [
            'availability' => $availability,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        auth()->user()->availability()->create($validated);

        return redirect()->route('availability.index')
            ->with('success', 'Availability added successfully.');
    }
}
