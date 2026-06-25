@extends('layouts.app')

@section('content')
<div class="text-center py-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">
        Respira la Montagna – Giornata Regionale della Montagna
    </h2>
    <p class="text-lg text-gray-600 mb-1">
        <span class="font-semibold">5 luglio 2026</span>
    </p>
    <p class="text-gray-600 mb-8">
        Regione Lombardia
    </p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">L'iniziativa</h3>
    <p class="text-gray-700 leading-relaxed mb-4">
        La Giornata Regionale della Montagna è un evento organizzato dal Club Alpino Italiano – Gruppo Regionale Lombardia
        per avvicinare grandi e piccoli alla montagna attraverso escursioni guidate, attività all'aria aperta
        e momenti di condivisione nella natura.
    </p>
    <p class="text-gray-700 leading-relaxed mb-4">
        Partecipa con i tuoi figli a una delle attività disponibili: ogni esperienza è pensata per famiglie,
        con accompagnatori qualificati e percorsi adatti a tutte le età.
    </p>
    <p class="text-gray-700 leading-relaxed">
        L'iscrizione è gratuita. I posti sono limitati per garantire la sicurezza e la qualità dell'esperienza.
    </p>
</div>

<div class="text-center">
    <a href="{{ route('activities') }}"
       class="inline-block bg-blue-700 hover:bg-blue-800 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
        Inizia
    </a>
</div>
@endsection
