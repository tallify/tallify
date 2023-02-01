<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
    @stack('meta')

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()
    @stack('styles')
</head>

<body class="relative min-h-screen">
    <main>
        {{ $slot }}
    </main>


    @stack('modals')
    @livewireScripts
    @stack('scripts')
</body>

</html>
