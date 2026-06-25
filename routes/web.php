<?php

use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistrationController::class, 'index'])->name('home');
Route::get('/attivita', [RegistrationController::class, 'showActivities'])->name('activities');
Route::get('/iscriviti/{activity}', [RegistrationController::class, 'showForm'])->name('registrations.form');
Route::post('/iscriviti/{activity}', [RegistrationController::class, 'store'])->name('registrations.store');
Route::get('/conferma', [RegistrationController::class, 'confirm'])->name('registrations.confirm');
