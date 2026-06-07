<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/asterisk.svg') }}" type="image/x-icon">

    <title>@yield('title', 'Admin') &middot; {{ config('app.name', 'Laravel') }} CMS</title>

    <!-- Fonts — Bai Jamjuree, matching the public frontend -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-cream text-ink">
    @php
        // Grouped nav: Dashboard stands alone (no heading); the rest are split
        // into "Site content" (singletons) and "Portfolio" (collections).
        // Projects also owns the Filters routes — they share one merged page.
        $navGroups = [
            ['title' => null, 'links' => [
                ['route' => 'admin.dashboard', 'pattern' => ['admin.dashboard'], 'label' => 'Dashboard'],
                ['route' => 'admin.media.index', 'pattern' => ['admin.media.*'], 'label' => 'Media'],
            ]],
            ['title' => 'Site content', 'links' => [
                ['route' => 'admin.hero.edit',    'pattern' => ['admin.hero.*'],    'label' => 'Hero'],
                ['route' => 'admin.about.edit',   'pattern' => ['admin.about.*'],   'label' => 'About'],
                ['route' => 'admin.contact.edit', 'pattern' => ['admin.contact.*'], 'label' => 'Contact'],
            ]],
            ['title' => 'Portfolio', 'links' => [
                ['route' => 'admin.projects.index',   'pattern' => ['admin.projects.*', 'admin.filters.*'], 'label' => 'Projects'],
                ['route' => 'admin.education.index',  'pattern' => ['admin.education.*'],  'label' => 'Education'],
                ['route' => 'admin.experience.index', 'pattern' => ['admin.experience.*'], 'label' => 'Experience'],
            ]],
        ];
    @endphp

    <div class="min-h-screen flex" x-data="{ open: false }" @keydown.escape.window="open = false">
        {{-- Backdrop — only on mobile when the drawer is open. --}}
        <div x-show="open" x-cloak x-transition.opacity @click="open = false"
             class="fixed inset-0 z-30 bg-ink/50 lg:hidden"></div>

        {{-- Sidebar — fixed off-canvas drawer below lg, static column at lg+. --}}
        <aside class="fixed inset-y-0 left-0 z-40 w-60 shrink-0 bg-ink text-cream/70 flex flex-col
                      transform transition-transform duration-300 lg:static lg:z-auto lg:translate-x-0"
               :class="open ? 'translate-x-0' : '-translate-x-full'">
            <div class="px-6 py-5 border-b border-white/10">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-lg font-bold text-white">
                    <img src="{{ asset('assets/star.svg') }}" alt="" width="24" height="24" aria-hidden="true">
                    Portfolio CMS
                </a>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto" @click="open = false">
                @foreach ($navGroups as $group)
                    <div class="space-y-1">
                        @if ($group['title'])
                            <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-cream/40">
                                {{ $group['title'] }}
                            </p>
                        @endif
                        @foreach ($group['links'] as $link)
                            @php $active = request()->routeIs(...$link['pattern']); @endphp
                            <a href="{{ route($link['route']) }}"
                               class="block px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-300
                                      {{ $active ? 'bg-brand-500 text-white' : 'text-cream/70 hover:bg-white/10 hover:text-white' }}">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            {{-- Account + logout, anchored bottom-left. --}}
            <div class="px-3 py-4 border-t border-white/10 space-y-3">
                <div class="px-3 text-xs text-cream/50">
                    Signed in as
                    <span class="block text-cream font-semibold truncate">{{ auth()->user()?->name ?? auth()->user()?->email }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold text-cream/70 hover:bg-white/10 hover:text-white transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                        </svg>
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="bg-white border-b border-ink/10">
                <div class="px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Hamburger — opens the drawer on mobile only. --}}
                        <button type="button" @click="open = true"
                                class="lg:hidden -ml-1 p-2 rounded-lg text-ink/70 hover:bg-ink/5 hover:text-ink transition-colors"
                                aria-label="Open menu">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <line x1="3" y1="12" x2="21" y2="12"/>
                                <line x1="3" y1="18" x2="21" y2="18"/>
                            </svg>
                        </button>
                        <h1 class="text-lg sm:text-xl font-bold text-ink truncate">@yield('title', 'Admin')</h1>
                    </div>

                    <a href="{{ route('home') }}" target="_blank"
                       class="shrink-0 text-sm font-semibold text-ink/60 hover:text-brand-700 transition-colors">
                        View live site &rarr;
                    </a>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-error">
                        <p class="font-semibold mb-1">Please fix the following:</p>
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
