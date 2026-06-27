@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Scegli un'attività</h2>
    <p class="text-gray-600">5 luglio 2026 – Giornata Regionale della Montagna, Regione Lombardia</p>
</div>

@if($activities->isEmpty() || $activities->every(fn($a) => $a['is_full']))
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <p class="text-gray-600 text-lg">Tutte le iniziative sono al completo.</p>
    </div>
@else
    <div class="grid gap-4" x-data="{ openModal: null }">
        @foreach($activities as $activity)
            @php
                $spots = $activity['available_spots'];
                $isFull = $activity['is_full'];
            @endphp
            <div class="bg-white rounded-lg shadow-sm border {{ $isFull ? 'border-gray-200 opacity-60' : 'border-gray-200 hover:border-blue-400' }} p-6 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $activity['name'] }}</h3>
                        <p class="text-sm text-gray-600 mb-1">
                            <span class="font-medium">Luogo:</span>
                            @if($activity['latitude'] !== null && $activity['longitude'] !== null)
                                <a href="https://www.google.com/maps?q={{ $activity['latitude'] }},{{ $activity['longitude'] }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="text-blue-700 hover:underline">{{ $activity['meeting_place'] }}</a>
                            @else
                                {{ $activity['meeting_place'] }}
                            @endif
                            &nbsp;|&nbsp;
                            <span class="font-medium">Orario:</span> {{ $activity['meeting_time'] }}
                        </p>
                        @if($activity['description'])
                            <p class="text-gray-700 text-sm mb-3">{{ $activity['description'] }}</p>
                        @endif
                        <p class="text-sm {{ $spots <= 0 ? 'text-red-600' : ($spots <= 5 ? 'text-orange-600' : 'text-green-600') }} font-medium">
                            @if($isFull)
                                Esaurita
                            @else
                                {{ $spots }} {{ $spots === 1 ? 'posto disponibile' : 'posti disponibili' }}
                            @endif
                        </p>
                    </div>
                    <div class="shrink-0 flex flex-col gap-2 items-end">
                        @if($isFull)
                            <span class="inline-block bg-gray-200 text-gray-500 font-semibold px-6 py-3 rounded-lg cursor-not-allowed">
                                Esaurita
                            </span>
                        @else
                            <a href="{{ route('registrations.form', $activity['id']) }}"
                               class="inline-block bg-blue-700 hover:bg-blue-800 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                                Scegli
                            </a>
                        @endif
                        <button
                            type="button"
                            @click="openModal = {{ $activity['id'] }}"
                            class="inline-block border border-blue-600 text-blue-700 hover:bg-blue-50 font-semibold px-6 py-2 rounded-lg transition-colors text-sm">
                            INFO
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal per questa attività --}}
            <div
                x-show="openModal === {{ $activity['id'] }}"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                style="background: rgba(0,0,0,0.5);"
                @click.self="openModal = null">
                <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative">
                    <button
                        type="button"
                        @click="openModal = null"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-xl font-bold leading-none">
                        &times;
                    </button>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $activity['name'] }}</h3>
                    <dl class="space-y-2 text-sm">
                        @if($activity['difficulty'])
                            <div class="flex gap-2">
                                <dt class="font-medium text-gray-600 w-36 shrink-0">Difficoltà</dt>
                                <dd class="text-gray-900">{{ $activity['difficulty'] }}</dd>
                            </div>
                        @endif
                        @if($activity['elevation_gain'])
                            <div class="flex gap-2">
                                <dt class="font-medium text-gray-600 w-36 shrink-0">Dislivello</dt>
                                <dd class="text-gray-900">{{ $activity['elevation_gain'] }}</dd>
                            </div>
                        @endif
                        @if($activity['trail_length'])
                            <div class="flex gap-2">
                                <dt class="font-medium text-gray-600 w-36 shrink-0">Lunghezza / Durata</dt>
                                <dd class="text-gray-900">{{ $activity['trail_length'] }}</dd>
                            </div>
                        @endif
                        @if($activity['water_description'])
                            <div class="flex gap-2">
                                <dt class="font-medium text-gray-600 w-36 shrink-0">Acqua</dt>
                                <dd class="text-gray-900">{{ $activity['water_description'] }}</dd>
                            </div>
                        @endif
                        @if($activity['itinerary_description'])
                            <div class="pt-2">
                                <dt class="font-medium text-gray-600 mb-1">Descrizione itinerario</dt>
                                <dd class="text-gray-900 leading-relaxed">{{ $activity['itinerary_description'] }}</dd>
                            </div>
                        @endif
                    </dl>
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <a href="https://organizzazione.cai.it/gr-lombardia/progetti-regione-lom/attivita/"
                           target="_blank" rel="noopener noreferrer"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Scopri di più sul sito CAI →
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
