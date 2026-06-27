@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Iscrizione – Respira la Montagna</h2>
    <p class="text-gray-600">5 luglio 2026 – Giornata Regionale della Montagna, Regione Lombardia</p>
</div>

<form
    method="POST"
    action="{{ route('registrations.store', $activity) }}"
    novalidate
    x-data="{
        isCaiMember: {{ old('is_cai_member') ? 'true' : 'false' }},
        sectionQuery: {{ Js::from($preloadedSectionName) }},
        sectionResults: [],
        selectedSectionId: '{{ old('cai_section_id', '') }}',
        showDropdown: false,
        emailDuplicate: false,
        async searchSections() {
            if (this.sectionQuery.length < 2) {
                this.sectionResults = [];
                this.showDropdown = false;
                return;
            }
            const res = await fetch('/api/sections?q=' + encodeURIComponent(this.sectionQuery));
            this.sectionResults = await res.json();
            this.showDropdown = this.sectionResults.length > 0;
        },
        selectSection(section) {
            this.selectedSectionId = section.id;
            this.sectionQuery = section.name + (section.province ? ' (' + section.province + ')' : '');
            this.sectionResults = [];
            this.showDropdown = false;
        },
        async checkEmail(value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.emailDuplicate = false;
                return;
            }
            try {
                const res = await fetch('/api/check-email?email=' + encodeURIComponent(value));
                const data = await res.json();
                this.emailDuplicate = data.exists;
            } catch (e) {
                this.emailDuplicate = false;
            }
        }
    }"
>
    @csrf

    {{-- Dati adulto --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Dati personali</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        required
                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-500 @enderror"
                    >
                    @error('first_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Cognome <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        required
                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-500 @enderror"
                    >
                    @error('last_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    @blur="checkEmail($event.target.value)"
                    class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                >
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p x-show="emailDuplicate" x-cloak class="text-red-500 text-xs mt-1">Questo indirizzo email è già stato utilizzato per un'iscrizione.</p>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefono <span class="text-red-500">*</span></label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror"
                >
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Data di nascita <span class="text-red-500">*</span></label>
                <input
                    type="date"
                    id="birth_date"
                    name="birth_date"
                    value="{{ old('birth_date') }}"
                    required
                    class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('birth_date') border-red-500 @enderror"
                >
                @error('birth_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Checkbox socio CAI --}}
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_cai_member"
                        value="1"
                        x-model="isCaiMember"
                        class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="text-sm font-medium text-gray-700">Sono socio CAI</span>
                </label>
            </div>

            {{-- Sezione CAI (se socio) --}}
            <div x-show="isCaiMember" x-cloak>
                <label for="section_search" class="block text-sm font-medium text-gray-700 mb-1">Sezione CAI <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input
                        type="text"
                        id="section_search"
                        x-model="sectionQuery"
                        @input="searchSections()"
                        @blur="setTimeout(() => showDropdown = false, 150)"
                        autocomplete="off"
                        placeholder="Digita il nome della sezione..."
                        :required="isCaiMember"
                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cai_section_id') border-red-500 @enderror"
                    >
                    <input type="hidden" name="cai_section_id" x-model="selectedSectionId">

                    <div
                        x-show="showDropdown"
                        class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-56 overflow-y-auto"
                    >
                        <template x-for="section in sectionResults" :key="section.id">
                            <button
                                type="button"
                                @click="selectSection(section)"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-0"
                            >
                                <span class="font-medium" x-text="section.name"></span>
                                <span class="text-gray-500 ml-1" x-show="section.province" x-text="'(' + section.province + ')'"></span>
                            </button>
                        </template>
                    </div>
                </div>
                @error('cai_section_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Codice fiscale (se non socio) --}}
            <div x-show="!isCaiMember" x-cloak>
                <label for="fiscal_code" class="block text-sm font-medium text-gray-700 mb-1">Codice Fiscale <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    id="fiscal_code"
                    name="fiscal_code"
                    value="{{ old('fiscal_code') }}"
                    maxlength="16"
                    pattern="[A-Za-z0-9]{16}"
                    :required="!isCaiMember"
                    class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fiscal_code') border-red-500 @enderror"
                >
                @error('fiscal_code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-blue-700 bg-blue-50 rounded-lg px-3 py-2">
                    In quanto non socio, sarai assicurato con polizza Soccorso Alpino, RC e Infortuni Combinazione A a carico del GR Lombardia.
                </p>
            </div>

    </div>

    {{-- Sezione minori --}}
    <div
        x-data="{
            minors: {{ Js::from($preloadedMinors) }},
            addMinor() {
                if (this.minors.length >= 3) return;
                this.minors.push({
                    first_name: '',
                    last_name: '',
                    birth_date: '',
                    is_cai_member: false,
                    cai_section_id: '',
                    fiscal_code: '',
                    sectionQuery: '',
                    sectionResults: [],
                    showDropdown: false
                });
            },
            removeMinor(index) {
                this.minors.splice(index, 1);
            },
            async searchSections(index) {
                const minor = this.minors[index];
                if (minor.sectionQuery.length < 2) {
                    minor.sectionResults = [];
                    minor.showDropdown = false;
                    return;
                }
                const res = await fetch('/api/sections?q=' + encodeURIComponent(minor.sectionQuery));
                minor.sectionResults = await res.json();
                minor.showDropdown = minor.sectionResults.length > 0;
            },
            selectSection(index, section) {
                const minor = this.minors[index];
                minor.cai_section_id = section.id;
                minor.sectionQuery = section.name + (section.province ? ' (' + section.province + ')' : '');
                minor.sectionResults = [];
                minor.showDropdown = false;
            }
        }"
        class="mt-6 space-y-4"
    >
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Minori accompagnati</h3>
                <button
                    type="button"
                    @click="addMinor()"
                    :disabled="minors.length >= 3"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Aggiungi minore
                </button>
            </div>

            @php
                $minorFieldErrors = collect($errors->toArray())->filter(fn($v, $k) => preg_match('/^minors\.\d+\./', $k));
            @endphp
            @if($minorFieldErrors->isNotEmpty())
                <div class="mb-3 space-y-1">
                    @foreach($minorFieldErrors as $msgs)
                        @foreach($msgs as $msg)
                            <p class="text-red-600 text-sm">{{ $msg }}</p>
                        @endforeach
                    @endforeach
                </div>
            @endif

            <p x-show="minors.length === 0" class="text-sm text-gray-500 italic">Nessun minore aggiunto. Massimo 3 minori.</p>

            <template x-for="(minor, index) in minors" :key="index">
                <div class="border border-gray-200 rounded-lg p-4 space-y-4 mt-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700" x-text="'Minore ' + (index + 1)"></h4>
                        <button
                            type="button"
                            @click="removeMinor(index)"
                            class="text-sm text-red-600 hover:text-red-800 font-medium"
                        >Rimuovi</button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                :name="'minors[' + index + '][first_name]'"
                                x-model="minor.first_name"
                                required
                                class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cognome <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                :name="'minors[' + index + '][last_name]'"
                                x-model="minor.last_name"
                                required
                                class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data di nascita <span class="text-red-500">*</span></label>
                        <input
                            type="date"
                            :name="'minors[' + index + '][birth_date]'"
                            x-model="minor.birth_date"
                            required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <div class="pt-1">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                :name="'minors[' + index + '][is_cai_member]'"
                                value="1"
                                x-model="minor.is_cai_member"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm font-medium text-gray-700">Il minore è socio CAI</span>
                        </label>
                    </div>

                    {{-- Sezione CAI minore (se socio) --}}
                    <div x-show="minor.is_cai_member" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sezione CAI <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="minor.sectionQuery"
                                @input="searchSections(index)"
                                @blur="setTimeout(() => minor.showDropdown = false, 150)"
                                autocomplete="off"
                                placeholder="Digita il nome della sezione..."
                                :required="minor.is_cai_member"
                                class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <input
                                type="hidden"
                                :name="'minors[' + index + '][cai_section_id]'"
                                x-model="minor.cai_section_id"
                            >
                            <div
                                x-show="minor.showDropdown"
                                class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-56 overflow-y-auto"
                            >
                                <template x-for="section in minor.sectionResults" :key="section.id">
                                    <button
                                        type="button"
                                        @click="selectSection(index, section)"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-0"
                                    >
                                        <span class="font-medium" x-text="section.name"></span>
                                        <span class="text-gray-500 ml-1" x-show="section.province" x-text="'(' + section.province + ')'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Codice fiscale minore (se non socio) --}}
                    <div x-show="!minor.is_cai_member" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Codice Fiscale <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            :name="'minors[' + index + '][fiscal_code]'"
                            x-model="minor.fiscal_code"
                            maxlength="16"
                            pattern="[A-Za-z0-9]{16}"
                            :required="!minor.is_cai_member"
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <p class="mt-2 text-sm text-blue-700 bg-blue-50 rounded-lg px-3 py-2">
                            In quanto non socio, il minore sarà assicurato con polizza Soccorso Alpino, RC e Infortuni Combinazione A a carico del GR Lombardia.
                        </p>
                    </div>
                </div>
            </template>
        </div>

    </div>{{-- end x-data minori --}}

    {{-- Attività selezionata --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Attività selezionata</h3>

        @error('activity')
            <p class="text-red-500 text-xs mb-3">{{ $message }}</p>
        @enderror

        <input type="hidden" name="activity_id" value="{{ $activity->id }}">

        <div class="bg-gray-50 rounded-lg p-4">
            <p class="font-bold text-gray-900 text-base">{{ $activity->name }}</p>
            @if($activity->description)
                <p class="text-sm text-gray-600 mt-1">{{ $activity->description }}</p>
            @endif
            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-sm text-gray-500">
                <span><span class="font-medium">Orario ritrovo:</span> {{ $activity->meeting_time }}</span>
                <span><span class="font-medium">Partenza:</span> {{ $activity->meeting_place }}</span>
            </div>
            <p class="mt-2 text-sm font-medium text-green-600">{{ $activity->availableSpots() }} posti disponibili</p>
        </div>
    </div>

    {{-- Consensi obbligatori --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Consensi obbligatori</h3>

        {{-- 1. Privacy --}}
        <div>
            <label class="flex items-start gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    name="privacy_accepted"
                    value="1"
                    {{ old('privacy_accepted') ? 'checked' : '' }}
                    class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 shrink-0"
                >
                <span class="text-sm text-gray-700">
                    Accetto la politica sulla privacy (GDPR) per il trattamento dei miei dati personali da parte del GR CAI Lombardia. <span class="text-red-500">*</span>
                </span>
            </label>
            @error('privacy_accepted')
                <p class="text-red-500 text-xs mt-1 ml-7">{{ $message }}</p>
            @enderror
        </div>

        {{-- 2. Liberatoria foto/video --}}
        <div>
            <label class="flex items-start gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    name="photo_release_accepted"
                    value="1"
                    {{ old('photo_release_accepted') ? 'checked' : '' }}
                    class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 shrink-0"
                >
                <span class="text-sm text-gray-700">
                    Autorizzo l'utilizzo di foto e video che mi ritraggono nell'ambito della GRMRL per le comunicazioni del GR CAI Lombardia. <span class="text-red-500">*</span>
                </span>
            </label>
            @error('photo_release_accepted')
                <p class="text-red-500 text-xs mt-1 ml-7">{{ $message }}</p>
            @enderror
        </div>

        {{-- 3. Regolamento --}}
        <div>
            <label class="flex items-start gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    name="rules_accepted"
                    value="1"
                    {{ old('rules_accepted') ? 'checked' : '' }}
                    class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 shrink-0"
                >
                <span class="text-sm text-gray-700">
                    Ho letto la locandina, compreso l'itinerario, il dislivello e la durata. Dichiaro che l'escursione è alla mia portata. <span class="text-red-500">*</span>
                </span>
            </label>
            @error('rules_accepted')
                <p class="text-red-500 text-xs mt-1 ml-7">{{ $message }}</p>
            @enderror
        </div>

        {{-- 4. Maltempo --}}
        <div>
            <label class="flex items-start gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    name="weather_cancellation_accepted"
                    value="1"
                    {{ old('weather_cancellation_accepted') ? 'checked' : '' }}
                    class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 shrink-0"
                >
                <span class="text-sm text-gray-700">
                    Accetto che il Direttore di Escursione possa sospendere o interrompere l'escursione in caso di maltempo, rischio, agibilità del sentiero, ostacoli o imprevisti. <span class="text-red-500">*</span>
                </span>
            </label>
            @error('weather_cancellation_accepted')
                <p class="text-red-500 text-xs mt-1 ml-7">{{ $message }}</p>
            @enderror
        </div>

        {{-- 5. Attrezzatura --}}
        <div>
            <label class="flex items-start gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    name="equipment_check_accepted"
                    value="1"
                    {{ old('equipment_check_accepted') ? 'checked' : '' }}
                    class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 shrink-0"
                >
                <span class="text-sm text-gray-700">
                    Accetto che il Direttore di Escursione possa impedirmi la partecipazione se non ho l'attrezzatura adeguata. <span class="text-red-500">*</span>
                </span>
            </label>
            @error('equipment_check_accepted')
                <p class="text-red-500 text-xs mt-1 ml-7">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Submit --}}
    <div class="mt-6 flex justify-end">
        <button
            type="submit"
            :disabled="emailDuplicate"
            class="inline-flex items-center gap-2 px-8 py-3 text-base font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
            Invia iscrizione
        </button>
    </div>

</form>
@endsection
