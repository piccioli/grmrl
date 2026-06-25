<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iscrizioni – {{ $activity?->name ?? 'Tutte le attività' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; margin: 20px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitle { font-size: 14px; color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) td { background: #fafafa; }
        .no-print { margin-bottom: 16px; }
        @media print {
            .no-print { display: none; }
            nav, aside, header.fi-topbar, .fi-sidebar { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">Stampa</button>
    <a href="{{ url()->previous() }}" style="margin-left:12px;">← Torna al pannello</a>
</div>

<h1>Iscrizioni – Respira la Montagna – 5 luglio 2026</h1>
@if($activity)
    <div class="subtitle">Attività: {{ $activity->name }} | Ritrovo: {{ $activity->meeting_time }} – {{ $activity->meeting_place }}</div>
@else
    <div class="subtitle">Tutte le attività</div>
@endif

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nome e Cognome</th>
            <th>Email</th>
            <th>Telefono</th>
            <th>Sezione CAI</th>
            <th>Minori</th>
        </tr>
    </thead>
    <tbody>
        @forelse($registrations as $i => $reg)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $reg->first_name }} {{ $reg->last_name }}</td>
                <td>{{ $reg->email }}</td>
                <td>{{ $reg->phone }}</td>
                <td>{{ $reg->caiSection?->name ?? 'Non socio' }}</td>
                <td>{{ $reg->minors->map(fn ($m) => $m->first_name.' '.$m->last_name)->join(', ') ?: '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#888;">Nessuna iscrizione</td>
            </tr>
        @endforelse
    </tbody>
</table>

<p style="margin-top:12px;font-size:11px;color:#888;">Totale iscritti: {{ $registrations->count() }}</p>

</body>
</html>
