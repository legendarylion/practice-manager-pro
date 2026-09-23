<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventTypeController extends Controller
{
    public function index()
    {
        $practice = auth()->user()->practice;
        
        if (!$practice) {
            // Handle the case where user doesn't have a practice
            return redirect()->route('practice.setup')
                ->with('error', 'Please set up your practice first.');
        }

        $eventTypes = $practice->eventTypes()
            ->with('user')
            ->latest()
            ->get();

        return Inertia::render('EventTypes/Index', [
            'eventTypes' => $eventTypes
        ]);
    }

    public function create()
    {
        return Inertia::render('EventTypes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration' => ['required', 'integer', 'min:5'],
            'color' => ['required', 'string', 'max:7'],
            'location_type' => ['required', 'string', 'in:virtual,phone,in-person'],
            'location_details' => ['nullable', 'string'],
            'custom_questions' => ['nullable', 'array']
        ]);

        $eventType = auth()->user()->practice->eventTypes()->create([
            ...$validated,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('event-types.index')
            ->with('success', 'Event type created successfully.');
    }

    public function edit(EventType $eventType)
    {
        $this->authorize('update', $eventType);

        return Inertia::render('EventTypes/Edit', [
            'eventType' => $eventType
        ]);
    }

    public function update(Request $request, EventType $eventType)
    {
        $this->authorize('update', $eventType);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration' => ['required', 'integer', 'min:5'],
            'color' => ['required', 'string', 'max:7'],
            'location_type' => ['required', 'string', 'in:virtual,phone,in-person'],
            'location_details' => ['nullable', 'string'],
            'custom_questions' => ['nullable', 'array']
        ]);

        $eventType->update($validated);

        return redirect()->route('event-types.index')
            ->with('success', 'Event type updated successfully.');
    }

    public function destroy(EventType $eventType)
    {
        $this->authorize('delete', $eventType);

        $eventType->delete();

        return redirect()->route('event-types.index')
            ->with('success', 'Event type deleted successfully.');
    }
}