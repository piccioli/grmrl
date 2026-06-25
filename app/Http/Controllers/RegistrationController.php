<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function showForm(): View
    {
        $activities = Activity::where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn (Activity $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'description' => $a->description,
                'meeting_time' => $a->meeting_time,
                'meeting_place' => $a->meeting_place,
                'available_spots' => $a->availableSpots(),
                'is_full' => $a->isFull(),
            ]);

        return view('registration.form', compact('activities'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Implemented in US-012
        return redirect()->back();
    }

    public function confirm(): View
    {
        // Implemented in US-012
        return view('registration.confirm');
    }
}
