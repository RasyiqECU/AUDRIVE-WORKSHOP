<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AUDrive') }} — @yield('title', 'Auth')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/lamborghini.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-nero text-silver font-body antialiased min-h-screen bg-carbon-fiber">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 relative">
        <!-- Background Effects -->
        <div class="absolute inset-0 hex-pattern opacity-20 pointer-events-none"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-giallo/5 rounded-full blur-[120px] pointer-events-none"></div>

        {{-- Logo --}}
        <div class="relative z-10 mb-8 text-center">
            <div class="inline-flex items-center gap-3 mb-4">
                <img src="{{ asset('images/AUDrive-logo.png') }}" alt="AUDrive Logo" class="w-14 h-14 object-contain drop-shadow-[0_0_12px_rgba(245,196,0,0.35)]" />
                <div class="text-left">
                    <h1 class="font-display text-2xl font-bold tracking-wider">
                        <span class="text-silver-bright">AU</span><span class="text-giallo">DRIVE</span>
                    </h1>
                    <p class="font-heading text-[10px] uppercase tracking-[0.3em] text-silver-dim">
                        Premium Automotive Dealer
                    </p>
                </div>
            </div>
        </div>

        {{-- Auth Card --}}
        <div class="relative z-10 w-full max-w-md">
            <div class="panel-chamfered bg-carbon border border-gunmetal p-8">
                @yield('content')
            </div>
        </div>

        {{-- Footer --}}
        <p class="relative z-10 mt-8 font-mono text-[10px] text-silver-dim uppercase tracking-wider">
            &copy; {{ date('Y') }} AUDrive. All rights reserved.
        </p>
    </div>

    @stack('scripts')
</body>
</html>