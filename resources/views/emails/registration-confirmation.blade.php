<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma iscrizione – Respira la Montagna</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background: #f5f5f5; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: #1a3a5c; padding: 24px 32px; text-align: center; }
        .header img { height: 70px; display: inline-block; }
        .header h1 { color: #fff; font-size: 16px; margin: 12px 0 0; }
        .body { padding: 32px; }
        .section-title { font-size: 18px; font-weight: bold; color: #1a3a5c; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px; margin-top: 28px; margin-bottom: 14px; }
        .field { margin: 6px 0; }
        .field strong { display: inline-block; width: 160px; color: #555; }
        .activity-box { background: #f0f7ff; border-left: 4px solid #1a3a5c; padding: 14px 18px; border-radius: 4px; margin: 16px 0; }
        .activity-box .name { font-size: 17px; font-weight: bold; color: #1a3a5c; margin-bottom: 8px; }
        .equipment-list { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 14px 18px; border-radius: 4px; margin: 16px 0; }
        .equipment-list ul { margin: 8px 0 0; padding-left: 20px; }
        .equipment-list li { margin: 4px 0; }
        .footer { background: #f3f4f6; padding: 20px 32px; text-align: center; font-size: 12px; color: #6b7280; }
        .footer img { height: 36px; margin-bottom: 8px; }
        .support { margin-top: 16px; padding: 12px; background: #f0f7ff; border-radius: 4px; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    {{-- Header --}}
    <div class="header">
        <img src="data:image/png;base64,{{ $caiLogoBase64 }}" alt="Logo CAI">
        <h1>Giornata Regionale della Montagna – Regione Lombardia | GRMRL</h1>
    </div>

    <div class="body">
        <p style="font-size:18px;font-weight:bold;color:#1a3a5c;">Conferma iscrizione – Respira la Montagna – 5 luglio 2026</p>
        <p>Grazie per esserti iscritto/a! Di seguito il riepilogo della tua iscrizione.</p>

        {{-- Adult data --}}
        <div class="section-title">Dati del partecipante</div>
        <div class="field"><strong>Nome:</strong> {{ $registration->first_name }} {{ $registration->last_name }}</div>
        <div class="field"><strong>Data di nascita:</strong> {{ $registration->birth_date->format('d/m/Y') }}</div>
        <div class="field"><strong>Email:</strong> {{ $registration->email }}</div>
        <div class="field"><strong>Telefono:</strong> {{ $registration->phone }}</div>
        @if($registration->is_cai_member)
            <div class="field"><strong>Socio CAI:</strong> Sì – {{ $registration->caiSection?->name ?? '—' }}</div>
        @else
            <div class="field"><strong>Socio CAI:</strong> No</div>
            <div class="field"><strong>Codice Fiscale:</strong> {{ $registration->fiscal_code ?? '—' }}</div>
        @endif

        {{-- Minors --}}
        @if($registration->minors->isNotEmpty())
            <div class="section-title">Minori</div>
            @foreach($registration->minors as $minor)
                <div style="background:#f9fafb;border-radius:4px;padding:10px 14px;margin-bottom:8px;">
                    <div class="field"><strong>Nome:</strong> {{ $minor->first_name }} {{ $minor->last_name }}</div>
                    <div class="field"><strong>Data di nascita:</strong> {{ $minor->birth_date->format('d/m/Y') }}</div>
                    @if($minor->is_cai_member)
                        <div class="field"><strong>Socio CAI:</strong> Sì – {{ $minor->caiSection?->name ?? '—' }}</div>
                    @else
                        <div class="field"><strong>Socio CAI:</strong> No</div>
                        <div class="field"><strong>Codice Fiscale:</strong> {{ $minor->fiscal_code ?? '—' }}</div>
                    @endif
                </div>
            @endforeach
        @endif

        {{-- Activity --}}
        <div class="section-title">Attività selezionata</div>
        <div class="activity-box">
            <div class="name">{{ $registration->activity->name }}</div>
            <div class="field"><strong>Orario di ritrovo:</strong> {{ $registration->activity->meeting_time }}</div>
            <div class="field"><strong>Luogo di partenza:</strong> {{ $registration->activity->meeting_place }}</div>
        </div>

        {{-- Equipment reminder --}}
        <div class="equipment-list">
            <strong>Ti ricordiamo di portare:</strong>
            <ul>
                <li>Giacca antivento/pioggia o mantella</li>
                <li>Capo caldo</li>
                <li>Cappello</li>
                <li>Occhiali da sole</li>
                <li>Crema solare</li>
                <li>Acqua (minimo 1 litro)</li>
                <li>Pranzo al sacco</li>
                <li>Snack</li>
            </ul>
        </div>

        {{-- Support info --}}
        <div class="support">
            Per informazioni: <a href="mailto:{{ config('grmrl.support_email') }}">{{ config('grmrl.support_email') }}</a>
            – Tel. <strong>{{ config('grmrl.support_phone') }}</strong>
            ({{ config('grmrl.support_hours') }})
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div><img src="data:image/png;base64,{{ $msLogoBase64 }}" alt="Logo Montagna Servizi"></div>
        <div>Realizzato da Montagna Servizi SCPA</div>
        <div style="margin-top:6px;">Sede legale: Via Errico Petrella 19, 20124 Milano (MI) | P.IVA 11790660960 | SDI: M5UXCR1</div>
    </div>
</div>
</body>
</html>
