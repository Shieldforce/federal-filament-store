<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Loja' }}</title>

    {{-- Vite (se seu app usa) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Estilos do Filament --}}
    <x-filament::styles />

    {{-- Estilos do Livewire --}}
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen">

<div class="container mx-auto p-4">
    {{ $slot }}
</div>

{{-- Scripts do Filament (inclui Alpine.js) --}}
<x-filament::scripts />

{{-- Scripts do Livewire --}}
@livewireScripts
</body>
</html>
