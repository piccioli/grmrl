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
                class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5"
                style="background: rgba(0,0,0,0.55);"
                @click.self="openModal = null">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[88vh] flex flex-col">

                    {{-- Header --}}
                    <div class="flex items-start justify-between px-4 py-3 border-b border-gray-100 shrink-0">
                        <h3 class="text-base font-bold text-gray-900 leading-snug pr-4">{{ $activity['name'] }}</h3>
                        <button
                            type="button"
                            @click="openModal = null"
                            class="shrink-0 text-gray-400 hover:text-gray-700 text-lg font-bold leading-none mt-0.5">
                            &times;
                        </button>
                    </div>

                    {{-- Body scrollabile --}}
                    <div class="overflow-y-auto px-4 py-4 flex-1">
                        <div class="flex flex-col md:flex-row gap-4">

                            {{-- Colonna sinistra: info + testi --}}
                            <div class="flex-1 min-w-0">

                                {{-- Info tecniche --}}
                                @php $hasTech = $activity['difficulty'] || $activity['elevation_gain'] || $activity['trail_length'] || $activity['water_description']; @endphp
                                @if($hasTech)
                                    <dl class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs mb-4 bg-gray-50 rounded-lg p-3">
                                        @if($activity['difficulty'])
                                            <dt class="font-semibold text-gray-500 uppercase tracking-wide">Difficoltà</dt>
                                            <dd class="text-gray-800">{{ $activity['difficulty'] }}</dd>
                                        @endif
                                        @if($activity['elevation_gain'])
                                            <dt class="font-semibold text-gray-500 uppercase tracking-wide">Dislivello</dt>
                                            <dd class="text-gray-800">{{ $activity['elevation_gain'] }}</dd>
                                        @endif
                                        @if($activity['trail_length'])
                                            <dt class="font-semibold text-gray-500 uppercase tracking-wide">Percorso</dt>
                                            <dd class="text-gray-800">{{ $activity['trail_length'] }}</dd>
                                        @endif
                                        @if($activity['water_description'])
                                            <dt class="font-semibold text-gray-500 uppercase tracking-wide">Acqua</dt>
                                            <dd class="text-gray-800">{{ $activity['water_description'] }}</dd>
                                        @endif
                                    </dl>
                                @endif

                                {{-- Descrizione dettagliata --}}
                                @if($activity['detailed_description'])
                                    <p class="text-sm text-gray-700 leading-relaxed mb-3">{{ $activity['detailed_description'] }}</p>
                                @endif

                                {{-- Descrizione itinerario --}}
                                @if($activity['itinerary_description'])
                                    <div class="{{ $activity['detailed_description'] ? 'border-t border-gray-100 pt-3' : '' }}">
                                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Itinerario</p>
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $activity['itinerary_description'] }}</p>
                                    </div>
                                @endif

                                {{-- Link CAI --}}
                                <div class="mt-4 pt-3 border-t border-gray-100">
                                    <a href="https://organizzazione.cai.it/gr-lombardia/progetti-regione-lom/attivita/"
                                       target="_blank" rel="noopener noreferrer"
                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                        Scopri di più sul sito CAI →
                                    </a>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
