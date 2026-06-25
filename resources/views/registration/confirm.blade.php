@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Success banner --}}
    <div class="bg-green-50 border border-green-200 rounded-xl p-8 text-center">
        <div class="flex justify-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#22c55e">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Iscrizione completata con successo!</h2>
        @if(session('email'))
            <p class="text-gray-600">Riceverai una email di conferma a <strong>{{ session('email') }}</strong></p>
        @endif
    </div>

    @if($registration)

        {{-- Dati adulto --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Dati personali</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-500">Nome</dt>
                    <dd class="text-gray-900">{{ $registration->first_name }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Cognome</dt>
                    <dd class="text-gray-900">{{ $registration->last_name }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Email</dt>
                    <dd class="text-gray-900">{{ $registration->email }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Telefono</dt>
                    <dd class="text-gray-900">{{ $registration->phone }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Sezione CAI</dt>
                    <dd class="text-gray-900">
                        {{ $registration->caiSection?->name ?? 'Non socio CAI' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Attività --}}
        @if($registration->activity)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Attività</h3>
            <div class="bg-blue-50 rounded-lg p-4 text-sm">
                <p class="font-bold text-gray-900 text-base">{{ $registration->activity->name }}</p>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-gray-600">
                    <span><span class="font-medium">Orario ritrovo:</span> {{ $registration->activity->meeting_time }}</span>
                    <span><span class="font-medium">Luogo:</span> {{ $registration->activity->meeting_place }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Minori --}}
        @if($registration->minors->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Minori accompagnati</h3>
            <div class="space-y-4">
                @foreach($registration->minors as $index => $minor)
                <div class="border border-gray-200 rounded-lg p-4 text-sm">
                    <p class="font-semibold text-gray-800 mb-2">Minore {{ $index + 1 }}</p>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <dt class="font-medium text-gray-500">Nome</dt>
                            <dd class="text-gray-900">{{ $minor->first_name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Cognome</dt>
                            <dd class="text-gray-900">{{ $minor->last_name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Data di nascita</dt>
                            <dd class="text-gray-900">{{ $minor->birth_date->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Sezione CAI</dt>
                            <dd class="text-gray-900">{{ $minor->caiSection?->name ?? 'Non socio CAI' }}</dd>
                        </div>
                    </dl>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif

</div>
@endsection
