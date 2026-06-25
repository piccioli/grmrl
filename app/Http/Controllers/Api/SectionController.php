<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaiSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->string('q')->trim()->value();

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $sections = CaiSection::query()
            ->where('name', 'like', '%'.$query.'%')
            ->orWhere('code', 'like', '%'.$query.'%')
            ->limit(15)
            ->get(['id', 'code', 'name', 'region', 'province']);

        return response()->json($sections);
    }
}
