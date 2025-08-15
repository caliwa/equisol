<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? config('app.name', 'Page Title') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="overflow-hidden">
    <div class="flex h-screen">
        {{-- <livewire:layouts.sidebar-component /> --}}
        <div class="relative w-full flex flex-col dark:bg-gray-900">
            <livewire:layouts.navbar-component />
            
            <main class="grow flex flex-col overflow-y-auto">
                <div>
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    
    @livewireScripts
    @fluxScripts
    @persist('toast')
        <flux:toast />
    @endpersist
</body>
</html>