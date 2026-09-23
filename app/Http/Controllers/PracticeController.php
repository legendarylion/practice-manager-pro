<?php

namespace App\Http\Controllers;

use App\Models\Practice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PracticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function settings()
    {
        $practice = auth()->user()->practice;
        
        // If no practice exists, we'll create one
        if (!$practice) {
            $practice = Practice::create([
                'name' => '',
                'email' => auth()->user()->email,
                'timezone' => config('app.timezone'),
                'settings' => ['setup_completed' => false]
            ]);
            
            auth()->user()->update(['practice_id' => $practice->id]);
        }

        return Inertia::render('Practice/Settings', [
            'practice' => $practice,
            'user' => auth()->user()->only(['name', 'email'])
        ]);
    }


    public function index()
    {
        return Inertia::render('Practices/Index', [
            'practices' => Practice::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Practice $practice)
    {
        return Inertia::render('Practices/Show', [
            'practice' => $practice
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Practice $practice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $practice = auth()->user()->practice;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:2'],
            'zip' => ['nullable', 'string', 'max:10'],
            'timezone' => ['required', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        $practice->update([
            ...$validated,
            'settings' => ['setup_completed' => true]
        ]);

        return back()->with('success', 'Practice settings updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Practice $practice)
    {
        //
    }
}
