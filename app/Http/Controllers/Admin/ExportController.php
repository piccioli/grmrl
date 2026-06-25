<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RegistrationsExport;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $activityId = $request->integer('activity_id') ?: null;

        $filename = 'iscrizioni-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(new RegistrationsExport($activityId), $filename);
    }

    public function print(Request $request)
    {
        $activityId = $request->integer('activity_id') ?: null;
        $activity = $activityId ? Activity::find($activityId) : null;

        $registrations = Registration::with(['activity', 'caiSection', 'minors'])
            ->when($activityId, fn ($q) => $q->where('activity_id', $activityId))
            ->orderBy('last_name')
            ->get();

        $generatedAt = now()->format('d/m/Y');
        $filename = 'iscrizioni-'.now()->format('Y-m-d').'.pdf';

        $pdf = Pdf::loadView('admin.print', compact('activity', 'registrations', 'generatedAt'));

        return $pdf->download($filename);
    }
}
