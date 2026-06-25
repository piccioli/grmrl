<?php

use App\Http\Controllers\Api\CheckEmailController;
use App\Http\Controllers\Api\SectionController;
use Illuminate\Support\Facades\Route;

Route::get('/sections', SectionController::class);
Route::middleware(['throttle:10,1'])->get('/check-email', CheckEmailController::class);
