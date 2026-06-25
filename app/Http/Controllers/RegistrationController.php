<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function showForm(): View
    {
        return view('registration.form');
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
