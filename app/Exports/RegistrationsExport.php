<?php

namespace App\Exports;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegistrationsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(protected ?int $activityId = null) {}

    public function query(): Builder
    {
        return Registration::with(['activity', 'caiSection', 'minors.caiSection'])
            ->when($this->activityId, fn ($q) => $q->where('activity_id', $this->activityId))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Nome adulto', 'Cognome adulto', 'Email', 'Telefono', 'Data di nascita',
            'Socio CAI', 'Sezione CAI', 'Attività',
            'Minore 1 - Nome', 'Minore 1 - Cognome', 'Minore 1 - Data di nascita',
            'Minore 2 - Nome', 'Minore 2 - Cognome', 'Minore 2 - Data di nascita',
            'Minore 3 - Nome', 'Minore 3 - Cognome', 'Minore 3 - Data di nascita',
        ];
    }

    public function map($registration): array
    {
        $row = [
            $registration->first_name,
            $registration->last_name,
            $registration->email,
            $registration->phone,
            $registration->birth_date?->format('d/m/Y'),
            $registration->is_cai_member ? 'Sì' : 'No',
            $registration->caiSection?->name ?? 'Non socio',
            $registration->activity?->name,
        ];

        $minors = $registration->minors->values();
        for ($i = 0; $i < 3; $i++) {
            $minor = $minors->get($i);
            $row[] = $minor?->first_name ?? '';
            $row[] = $minor?->last_name ?? '';
            $row[] = $minor?->birth_date?->format('d/m/Y') ?? '';
        }

        return $row;
    }
}
