<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancellazione iscrizione – Respira la Montagna</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background: #f5f5f5; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: #1a3a5c; padding: 24px 32px; text-align: center; }
        .header .logos { display: inline-flex; align-items: center; gap: 24px; }
        .header img { height: 70px; display: inline-block; }
        .header h1 { color: #fff; font-size: 16px; margin: 12px 0 0; }
        .body { padding: 32px; }
        .section-title { font-size: 18px; font-weight: bold; color: #1a3a5c; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px; margin-top: 28px; margin-bottom: 14px; }
        .field { margin: 6px 0; }
        .field strong { display: inline-block; width: 160px; color: #555; }
        .activity-box { background: #f0f7ff; border-left: 4px solid #1a3a5c; padding: 14px 18px; border-radius: 4px; margin: 16px 0; }
        .activity-box .name { font-size: 17px; font-weight: bold; color: #1a3a5c; margin-bottom: 8px; }
        .notice-box { background: #fff3cd; border-left: 4px solid #f59e0b; padding: 14px 18px; border-radius: 4px; margin: 16px 0; }
        .footer { background: #f3f4f6; padding: 20px 32px; text-align: center; font-size: 12px; color: #6b7280; }
        .footer img { height: 36px; margin-bottom: 8px; }
        .support { margin-top: 16px; padding: 12px; background: #f0f7ff; border-radius: 4px; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    {{-- Header --}}
    <div class="header">
        <div class="logos">
            <span style="display:inline-flex;flex-direction:column;align-items:center;gap:4px;">
                <img src="data:image/png;base64,{{ $caiLogoBase64 }}" alt="Logo CAI">
                <span style="color:#ffffff;font-size:11px;font-weight:600;letter-spacing:0.3px;">Gruppo Regionale Lombardia</span>
            </span>
            <span style="display:inline-block;background:#ffffff;border-radius:8px;padding:6px 10px;">
                <img src="data:image/png;base64,{{ $rlLogoBase64 }}" alt="Regione Lombardia – Il Consiglio" style="height:58px;display:block;">
            </span>
        </div>
        <h1>Giornata Regionale della Montagna – Regione Lombardia | GRMRL</h1>
    </div>

    <div class="body">
        <p style="font-size:18px;font-weight:bold;color:#c0392b;">Cancellazione iscrizione – Respira la Montagna – 5 luglio 2026</p>
        <p>Gentile {{ $registration->first_name }} {{ $registration->last_name }},</p>
        <p>ti informiamo che la tua iscrizione all'attività indicata di seguito è stata cancellata dall'amministrazione.</p>

        {{-- Activity --}}
        <div class="section-title">Attività cancellata</div>
        <div class="activity-box">
            <div class="name">{{ $registration->activity->name }}</div>
            <div class="field"><strong>Orario di ritrovo:</strong> {{ $registration->activity->meeting_time }}</div>
            <div class="field"><strong>Luogo di partenza:</strong> {{ $registration->activity->meeting_place }}</div>
        </div>

        {{-- Re-register notice --}}
        <div class="notice-box">
            <strong>Vuoi partecipare?</strong>
            <p style="margin: 8px 0 0;">Se ci sono ancora posti disponibili, puoi re-iscriverti accedendo al portale di iscrizioni. Per informazioni sui posti disponibili, contatta il nostro supporto.</p>
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
