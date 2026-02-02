<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso No Autorizado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center border-t-4 border-red-500">

        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
            <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                </path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-2">Acceso Denegado</h1>
        <p class="text-gray-500 mb-6">
            Lo sentimos, no tienes los permisos necesarios para acceder a esta sección del sistema.
        </p>

        <div class="bg-gray-50 p-4 rounded-md mb-6 text-sm text-gray-600">
            Intento de acceso a: <span class="font-mono text-red-500">{{ request()->path() }}</span>
        </div>

        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            Volver a mi Panel
        </a>
    </div>
</body>

</html>
