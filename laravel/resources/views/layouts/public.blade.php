<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>@yield('title', 'AUDrive') — {{ config('app.name', 'AUDrive') }}</title>
    <meta name="description" content="@yield('meta_description', 'AUDrive — Dealer mobil premium dengan layanan bengkel dan teknologi AI.')">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    @vite(['resources/css/app.css', 'resources/css/lamborghini.css', 'resources/js/app.js'])
    @stack('styles')


    @hasSection('schema')
        @yield('schema')
    @endif
</head>
<body class="bg-nero text-silver font-body antialiased min-h-screen flex flex-col">
    {{-- Navbar --}}
    <x-partials.navbar-public />


    {{-- Flash Messages --}}
    <div class="fixed top-20 right-4 z-40 max-w-md w-full pointer-events-none [&>*]:pointer-events-auto px-4">
        @if(session('success'))
            <x-ui.alert type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-ui.alert type="danger" :message="session('error')" />
        @endif
    </div>


    {{-- Main Content --}}
    <main class="flex-1 {{ request()->routeIs(['home', 'marketplace.index', 'workshop.index', 'parts.index', 'about', 'contact']) ? '' : 'pt-16' }}">
        @yield('content')
    </main>


    {{-- Footer --}}
    <x-partials.footer />


    @stack('scripts')
</body>
</html>



