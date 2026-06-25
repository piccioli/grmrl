<?php

use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistrationController::class, 'showForm'])->name('registrations.form');
Route::post('/', [RegistrationController::class, 'store'])->name('registrations.store');
Route::get('/conferma', [RegistrationController::class, 'confirm'])->name('registrations.confirm');
