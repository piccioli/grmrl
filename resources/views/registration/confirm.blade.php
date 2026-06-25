@extends('layouts.app')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-8 text-center">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Iscrizione completata con successo!</h2>
    <p class="text-gray-600">Riceverai una email di conferma a <strong>{{ session('email') }}</strong></p>
</div>
@endsection
