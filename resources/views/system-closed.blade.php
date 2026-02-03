<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Cerrado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 h-screen flex flex-col items-center justify-center text-white">

    <div class="text-center p-8 max-w-lg">
        <div class="mb-6 flex justify-center">
            <div
                class="h-24 w-24 bg-indigo-600 rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(79,70,229,0.5)]">
                <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                    </path>
                </svg>
            </div>
        </div>

        <h1 class="text-4xl font-bold mb-2">Sistema Cerrado</h1>
        <p class="text-gray-400 text-lg mb-8">
            El horario de operación ha finalizado por hoy.
        </p>

        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
            <p class="text-sm text-gray-400 uppercase tracking-widest mb-2">Horario de Atención</p>
            <p class="text-3xl font-mono font-bold text-indigo-400">08:00 - 23:59</p>
            <p class="text-xs text-gray-500 mt-2">Hora actual servidor:
                {{ \Carbon\Carbon::now('America/Argentina/Buenos_Aires')->format('H:i') }}</p>
        </div>

        <p class="mt-8 text-sm text-gray-500">
            Por favor, regrese mañana a partir de las 08:00 hs para realizar cargas.
        </p>
    </div>

</body>

</html>
