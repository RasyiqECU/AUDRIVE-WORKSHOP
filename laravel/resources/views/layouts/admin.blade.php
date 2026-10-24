<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin — {{ config('app.name', 'AUDrive') }} — @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/lamborghini.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-nero text-silver font-body antialiased min-h-screen">
    <div class="flex min-h-screen">
        {{-- Admin Sidebar --}}
        <x-layout.admin-sidebar />

        {{-- Main Content --}}
        <main class="flex-1 lg:ml-72">
            {{-- Admin Topbar --}}
            <x-layout.admin-topbar />

            <div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto">
                {{-- Breadcrumb --}}
                @hasSection('breadcrumb')
                    <nav class="mb-4 flex items-center gap-2 text-xs">
                        <a href="{{ route('admin.dashboard') }}" class="text-silver-dim hover:text-giallo transition-premium font-heading uppercase tracking-wider">
                            Admin
                        </a>
                        <span class="text-gunmetal">/</span>
                        @yield('breadcrumb')
                    </nav>
                @endif

                {{-- Flash Messages --}}
                @if(session('success'))
                    <x-ui.alert type="success" :message="session('success')" class="mb-4" />
                @endif
                @if(session('error'))
                    <x-ui.alert type="danger" :message="session('error')" class="mb-4" />
                @endif
                @if($errors->any())
                    <x-ui.alert type="danger" class="mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-ui.alert>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>