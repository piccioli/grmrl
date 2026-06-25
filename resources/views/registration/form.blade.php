@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Iscrizione – Respira la Montagna</h2>
    <p class="text-gray-600">5 luglio 2026 – Giornata Regionale della Montagna, Regione Lombardia</p>
</div>

<form method="POST" action="{{ route('registrations.store') }}" novalidate>
    @csrf

    {{-- Dati adulto --}}
    <div
        x-data="{
            isCaiMember: {{ old('is_cai_member') ? 'true' : 'false' }},
            sectionQuery: '{{ old('_section_name', '') }}',
            sectionResults: [],
            selectedSectionId: '{{ old('cai_section_id', '') }}',
            showDropdown: false,
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
            }
        }"
        class="space-y-6"
    >
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
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
                    class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                >
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
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

        {{-- Placeholder per US-010 (minori) e US-011 (attività) --}}

    </div>{{-- end x-data --}}

</form>
@endsection
