<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckEmailController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $email = $request->string('email')->trim()->value();
        $exists = Registration::where('email', $email)->exists();

        return response()->json(['exists' => $exists]);
    }
}
