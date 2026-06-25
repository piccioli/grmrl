<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function showForm(): View
    {
        return view('registration.form');
    }
}
