<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giornata Regionale della Montagna – Regione Lombardia | GRMRL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <header class="bg-white shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-4">
            <img src="{{ asset('images/cai-logo.png') }}" alt="CAI Logo" class="h-16 w-auto">
            <div>
                <h1 class="text-lg font-semibold text-gray-900 leading-tight">
                    Giornata Regionale della Montagna – Regione Lombardia | GRMRL
                </h1>
            </div>
        </div>

        <div class="bg-blue-700 text-white text-sm">
            <div class="max-w-2xl mx-auto px-4 py-2 flex flex-wrap gap-x-6 gap-y-1">
                <span>
                    <span class="font-medium">Supporto:</span>
                    <a href="tel:{{ config('grmrl.support_phone') }}" class="hover:underline">{{ config('grmrl.support_phone') }}</a>
                </span>
                <span>
                    <span class="font-medium">Orari:</span> {{ config('grmrl.support_hours') }}
                </span>
                <span>
                    <span class="font-medium">Email:</span>
                    <a href="mailto:{{ config('grmrl.support_email') }}" class="hover:underline">{{ config('grmrl.support_email') }}</a>
                </span>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <div class="max-w-2xl mx-auto px-4 py-8">
            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t mt-12">
        <div class="max-w-2xl mx-auto px-4 py-6 flex flex-col sm:flex-row items-center gap-4">
            <img src="{{ asset('images/ms-logo.png') }}" alt="Montagna Servizi Logo" class="h-10 w-auto">
            <div class="text-sm text-gray-600 text-center sm:text-left">
                <p class="font-medium">Realizzato da Montagna Servizi</p>
                <p>CAI Lombardia – Via Petrella 19, Milano</p>
                <p>
                    <a href="mailto:{{ config('grmrl.support_email') }}" class="hover:underline">{{ config('grmrl.support_email') }}</a>
                    &nbsp;|&nbsp;
                    <a href="tel:{{ config('grmrl.support_phone') }}" class="hover:underline">Tel. {{ config('grmrl.support_phone') }}</a>
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
