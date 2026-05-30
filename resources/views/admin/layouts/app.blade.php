<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') &middot; {{ config('app.name', 'Laravel') }} CMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-800">
    @php
        $navLinks = [
            ['route' => 'admin.dashboard',        'pattern' => 'admin.dashboard',    'label' => 'Dashboard'],
            ['route' => 'admin.hero.edit',        'pattern' => 'admin.hero.*',       'label' => 'Hero'],
            ['route' => 'admin.about.edit',       'pattern' => 'admin.about.*',      'label' => 'About'],
            ['route' => 'admin.contact.edit',     'pattern' => 'admin.contact.*',    'label' => 'Contact'],
            ['route' => 'admin.projects.index',   'pattern' => 'admin.projects.*',   'label' => 'Projects'],
            ['route' => 'admin.filters.index',    'pattern' => 'admin.filters.*',    'label' => 'Filters'],
            ['route' => 'admin.education.index',  'pattern' => 'admin.education.*',   'label' => 'Education'],
            ['route' => 'admin.experience.index', 'pattern' => 'admin.experience.*', 'label' => 'Experience'],
        ];
    @endphp

    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        <aside class="w-60 shrink-0 bg-[#1a1a2e] text-gray-200 flex flex-col">
            <div class="px-6 py-5 border-b border-white/10">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-white">
                    Portfolio CMS
                </a>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                @foreach ($navLinks as $link)
                    @php $active = request()->routeIs($link['pattern']); @endphp
                    <a href="{{ route($link['route']) }}"
                       class="block px-3 py-2 rounded-md text-sm font-medium transition
                              {{ $active ? 'bg-white/15 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach

                {{-- CV upload has no GET page; it lives on the dashboard (Step 6 decision). --}}
                <a href="{{ route('admin.dashboard') }}#cv-upload"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition">
                    CV
                </a>
            </nav>

            <div class="px-6 py-4 border-t border-white/10 text-xs text-gray-400">
                Signed in as<br>
                <span class="text-gray-200">{{ auth()->user()?->name ?? auth()->user()?->email }}</span>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="bg-white border-b border-gray-200">
                <div class="px-8 py-4 flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-900">@yield('title', 'Admin')</h1>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-8">
                @if (session('success'))
                    <div class="mb-6 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                        <p class="font-medium mb-1">Please fix the following:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
