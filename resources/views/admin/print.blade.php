<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Iscrizioni – Giornata Regionale della Montagna</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; margin: 20px; color: #222; }
        h1 { font-size: 15px; margin-bottom: 4px; }
        .subtitle { font-size: 11px; color: #555; margin-bottom: 4px; }
        .generated { font-size: 10px; color: #888; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #e8e8e8; font-weight: bold; }
        tr:nth-child(even) td { background: #f7f7f7; }
        .footer { margin-top: 12px; font-size: 10px; color: #888; }
    </style>
</head>
<body>

<h1>Iscrizioni – Giornata Regionale della Montagna</h1>
@if($activity)
    <div class="subtitle">Attività: {{ $activity->name }}</div>
@else
    <div class="subtitle">Tutte le attività</div>
@endif
<div class="generated">Generato il {{ $generatedAt }}</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Cognome</th>
            <th>Email</th>
            <th>Telefono</th>
            <th>Attività</th>
            <th>Socio CAI</th>
            <th>Sezione</th>
            <th>Minori</th>
        </tr>
    </thead>
    <tbody>
        @forelse($registrations as $i => $reg)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $reg->first_name }}</td>
                <td>{{ $reg->last_name }}</td>
                <td>{{ $reg->email }}</td>
                <td>{{ $reg->phone }}</td>
                <td>{{ $reg->activity?->name ?? '—' }}</td>
                <td>{{ $reg->is_cai_member ? 'Sì' : 'No' }}</td>
                <td>{{ $reg->caiSection?->name ?? '—' }}</td>
                <td>{{ $reg->minors->map(fn ($m) => $m->first_name . ' ' . $m->last_name)->join(', ') ?: '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;color:#888;">Nessuna iscrizione</td>
            </tr>
        @endforelse
    </tbody>
</table>

<p class="footer">Totale iscritti: {{ $registrations->count() }}</p>

</body>
</html>
