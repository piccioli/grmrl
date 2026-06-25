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
    <div class="grid gap-4">
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
                    <div class="shrink-0">
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
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
