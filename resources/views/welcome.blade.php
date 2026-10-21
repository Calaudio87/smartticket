<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartTicket - Soporte Técnico</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
<div class="min-h-screen flex flex-col items-center justify-center p-6">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">SmartTicket Triage</h1>
                <p class="text-sm text-gray-500">Centro de reporte de incidentes</p>
            </div>
            <!-- Enlace al panel de agentes -->
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 bg-indigo-50 px-3 py-1.5 rounded-md transition">
                Acceso Agentes &rarr;
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <form action="/tickets" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Asunto del problema</label>
                <input type="text" name="subject" id="subject" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Ej. Problema con la facturación de este mes">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción detallada</label>
                <textarea name="description" id="description" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Describe el incidente con el mayor detalle posible..."></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    Enviar Ticket para Análisis
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
