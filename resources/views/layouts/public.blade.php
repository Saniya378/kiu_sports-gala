<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'KIU Sports Gala | Karakoram International University')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|cinzel:600,700,900" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 font-sans antialiased selection:bg-amber-400 selection:text-emerald-950">

    <!-- Top Announcement Ticker Bar (Dark Green & Gold) -->
    <div class="bg-[#022116] border-b border-emerald-900/70 text-slate-300 text-xs py-1.5 px-4 hidden sm:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-400/20 text-amber-300 border border-amber-400/40">
                    Official Portal
                </span>
                <span class="text-slate-300 font-medium">
                    Karakoram International University &bull; Annual Sports Gala
                </span>
            </div>
            <div class="flex items-center space-x-4 text-[11px] text-slate-400">
                <span class="text-amber-400/90 font-medium flex items-center">
                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Sports Coverage
                </span>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-amber-300 transition-colors">
                        Logged in as <strong class="text-slate-200">{{ auth()->user()->name }}</strong> ({{ ucfirst(auth()->user()->role) }})
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-amber-300 transition-colors">
                        Official Login &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Public Header / Navbar (Dark Green & Gold Theme) -->
    <header class="sticky top-0 z-50 bg-[#042f20] border-b-2 border-amber-400/80 shadow-lg shadow-black/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <!-- Brand Logo -->
                <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-300 via-amber-400 to-amber-600 p-0.5 shadow-md shadow-amber-400/20 group-hover:scale-105 transition-transform duration-200">
                        <div class="w-full h-full bg-[#042f20] rounded-[10px] flex items-center justify-center text-amber-400">
                            <!-- Trophy / University Shield SVG -->
                            <svg class="w-6 h-6 fill-current text-amber-400" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-white tracking-wider text-lg leading-tight group-hover:text-amber-300 transition-colors">
                            KIU <span class="text-amber-400">SPORTS</span>
                        </span>
                        <span class="text-[10px] font-semibold uppercase tracking-widest text-emerald-300/90 leading-tight">
                            Annual Sports Gala
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links (All 9 items) -->
                <nav class="hidden xl:flex items-center space-x-1">
                    @php
                        $links = [
                            ['label' => 'Home', 'url' => Route::has('home') ? route('home') : url('/'), 'active' => request()->is('/') || request()->routeIs('home')],
                            ['label' => "Today's Matches", 'url' => Route::has('matches.today') ? route('matches.today') : (Route::has('today') ? route('today') : url('/today')), 'active' => request()->is('today*') || request()->routeIs('today*')],
                            ['label' => 'Schedule', 'url' => Route::has('schedule') ? route('schedule') : url('/schedule'), 'active' => request()->is('schedule*') || request()->routeIs('schedule*')],
                            ['label' => 'Results', 'url' => Route::has('results') ? route('results') : url('/results'), 'active' => request()->is('results*') || request()->routeIs('results*')],
                            ['label' => 'Teams', 'url' => Route::has('teams.public') ? route('teams.public') : (Route::has('teams') ? route('teams') : url('/teams')), 'active' => (request()->is('teams*') && !request()->is('admin*')) || request()->routeIs('teams.public')],
                            ['label' => 'Archive', 'url' => Route::has('archive') ? route('archive') : url('/archive'), 'active' => request()->is('archive*') || request()->routeIs('archive*')],
                            ['label' => 'Hall of Fame', 'url' => Route::has('hall-of-fame') ? route('hall-of-fame') : url('/hall-of-fame'), 'active' => request()->is('hall-of-fame*') || request()->routeIs('hall-of-fame*')],
                            ['label' => 'Gallery', 'url' => Route::has('gallery') ? route('gallery') : url('/gallery'), 'active' => request()->is('gallery*') || request()->routeIs('gallery*')],
                            ['label' => 'News', 'url' => Route::has('news') ? route('news') : url('/news'), 'active' => request()->is('news*') || request()->routeIs('news*')],
                        ];
                    @endphp

                    @foreach ($links as $link)
                        <a href="{{ $link['url'] }}"
                           class="px-2.5 py-1.5 rounded-lg text-[13px] font-semibold transition-all duration-150 {{ $link['active'] ? 'text-amber-300 bg-emerald-900/90 shadow-xs border-b-2 border-amber-400' : 'text-slate-200 hover:text-amber-300 hover:bg-emerald-900/50' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <!-- Navigation Links for LG screens (compact scroll or priority with dropdown) -->
                <nav class="hidden lg:flex xl:hidden items-center space-x-1">
                    @foreach (array_slice($links, 0, 6) as $link)
                        <a href="{{ $link['url'] }}"
                           class="px-2 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 {{ $link['active'] ? 'text-amber-300 bg-emerald-900/90 border-b-2 border-amber-400' : 'text-slate-200 hover:text-amber-300 hover:bg-emerald-900/50' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach

                    <!-- More Dropdown for Archive/Hall of Fame/Gallery/News on medium screens -->
                    <div class="relative" id="desktop-more-menu-container">
                        <button type="button"
                                onclick="document.getElementById('desktop-more-dropdown').classList.toggle('hidden')"
                                class="px-2 py-1.5 rounded-lg text-xs font-semibold text-slate-200 hover:text-amber-300 hover:bg-emerald-900/50 flex items-center space-x-1">
                            <span>More</span>
                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="desktop-more-dropdown" class="hidden absolute right-0 mt-2 w-44 bg-[#022116] border border-amber-400/40 rounded-xl shadow-xl py-2 z-50">
                            @foreach (array_slice($links, 6) as $link)
                                <a href="{{ $link['url'] }}"
                                   class="block px-4 py-2 text-xs font-medium text-slate-200 hover:text-amber-300 hover:bg-emerald-900/60 {{ $link['active'] ? 'text-amber-300 bg-emerald-900/40' : '' }}">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </nav>

                <!-- Right Action / User Area -->
                <div class="hidden sm:flex items-center space-x-3">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.editions.index') }}"
                               class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-400 hover:bg-amber-300 text-slate-950 shadow-sm transition-all duration-150">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Admin Portal</span>
                            </a>
                        @elseif(auth()->user()->role === 'scorer')
                            <a href="{{ route('admin.score-entry.index') }}"
                               class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-400 hover:bg-amber-300 text-slate-950 shadow-sm transition-all duration-150">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span>Score Entry</span>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-400 hover:bg-amber-300 text-slate-950 shadow-sm transition-all duration-150">
                                <span>Dashboard</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold text-amber-300 border border-amber-400/40 hover:bg-amber-400 hover:text-slate-950 transition-colors">
                            <span>Sign In</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button (Hamburger) -->
                <div class="flex items-center lg:hidden">
                    <button type="button"
                            id="mobile-menu-toggle-btn"
                            onclick="toggleMobileMenu()"
                            aria-label="Toggle Navigation Menu"
                            class="p-2 rounded-lg text-slate-200 hover:text-amber-400 hover:bg-emerald-900/60 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <!-- Closed Icon (Hamburger) -->
                        <svg id="mobile-menu-icon-closed" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Open Icon (X) -->
                        <svg id="mobile-menu-icon-open" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Navigation (Slide-down with Dark Green & Gold border) -->
        <div id="mobile-nav-menu" class="hidden lg:hidden bg-[#022116] border-t border-emerald-900/80 px-4 pt-3 pb-6 space-y-1">
            @foreach ($links as $link)
                <a href="{{ $link['url'] }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-semibold transition-colors {{ $link['active'] ? 'text-amber-300 bg-emerald-900/80 border-l-4 border-amber-400' : 'text-slate-200 hover:text-amber-300 hover:bg-emerald-900/40' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            <!-- Mobile Auth Actions -->
            <div class="pt-4 mt-3 border-t border-emerald-900/80 flex flex-col space-y-2">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.editions.index') }}"
                           class="w-full text-center px-4 py-2 rounded-lg text-xs font-bold bg-amber-400 text-slate-950 shadow-sm">
                            Admin Portal
                        </a>
                    @elseif(auth()->user()->role === 'scorer')
                        <a href="{{ route('admin.score-entry.index') }}"
                           class="w-full text-center px-4 py-2 rounded-lg text-xs font-bold bg-amber-400 text-slate-950 shadow-sm">
                            Score Entry
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="w-full text-center px-4 py-2 rounded-lg text-xs font-bold bg-amber-400 text-slate-950 shadow-sm">
                            Dashboard
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-center px-4 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-rose-400 hover:bg-emerald-950 transition-colors">
                            Log Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="w-full text-center px-4 py-2 rounded-lg text-xs font-bold bg-amber-400 text-slate-950 hover:bg-amber-300 transition-colors">
                        Sign In / Admin
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    @if (session('success') || session('status') || session('error') || (isset($errors) && $errors->any()))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center shadow-xs">
                    <svg class="w-5 h-5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
                    <p class="font-bold text-xs uppercase tracking-wider mb-1">Please correct the following errors:</p>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <!-- Optional Hero Slot -->
    @yield('hero')

    <!-- Main Content Body -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        @yield('content')
    </main>

    <!-- Public Footer (Dark Green & Gold Accents) -->
    <footer class="bg-[#022116] border-t-2 border-amber-400 text-slate-300 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

                <!-- Col 1: University & Gala Branding -->
                <div class="md:col-span-1 space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-400 flex items-center justify-center text-slate-950 font-black shadow-xs">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                        <span class="font-black tracking-wider text-white text-base">
                            KIU <span class="text-amber-400">SPORTS</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        The premier athletic tournament of Karakoram International University, uniting university faculties, sportsmanship, and excellence across Gilgit-Baltistan.
                    </p>
                    <div class="pt-2 text-xs text-amber-400 font-semibold">
                        #KIUSportsGala
                    </div>
                </div>

                <!-- Col 2: Tournament Live & Fixtures -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-3">
                        Tournament Action
                    </h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ Route::has('matches.today') ? route('matches.today') : (Route::has('today') ? route('today') : url('/today')) }}" class="hover:text-amber-300 transition-colors">Today's Matches</a></li>
                        <li><a href="{{ Route::has('schedule') ? route('schedule') : url('/schedule') }}" class="hover:text-amber-300 transition-colors">Fixture Schedule</a></li>
                        <li><a href="{{ Route::has('results') ? route('results') : url('/results') }}" class="hover:text-amber-300 transition-colors">Match Results & Scores</a></li>
                        <li><a href="{{ Route::has('teams.public') ? route('teams.public') : (Route::has('teams') ? route('teams') : url('/teams')) }}" class="hover:text-amber-300 transition-colors">Participating Teams</a></li>
                    </ul>
                </div>

                <!-- Col 3: Archives & Media -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-3">
                        Heritage & Media
                    </h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ Route::has('archive') ? route('archive') : url('/archive') }}" class="hover:text-amber-300 transition-colors">Gala Archive</a></li>
                        <li><a href="{{ Route::has('hall-of-fame') ? route('hall-of-fame') : url('/hall-of-fame') }}" class="hover:text-amber-300 transition-colors">Hall of Fame</a></li>
                        <li><a href="{{ Route::has('gallery') ? route('gallery') : url('/gallery') }}" class="hover:text-amber-300 transition-colors">Photo Gallery</a></li>
                        <li><a href="{{ Route::has('news') ? route('news') : url('/news') }}" class="hover:text-amber-300 transition-colors">Latest News & Bulletins</a></li>
                    </ul>
                </div>

                <!-- Col 4: Campus & Administration -->
                <div class="space-y-2 text-xs text-slate-400">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-3">
                        University Campus
                    </h4>
                    <p>Directorate of Sports & Physical Education</p>
                    <p>Karakoram International University</p>
                    <p>University Road, Gilgit, Gilgit-Baltistan</p>
                    <div class="pt-1 flex flex-col space-y-1">
                        <a href="{{ route('about') }}" class="hover:text-amber-300 transition-colors">About Gala &amp; Overview</a>
                        <a href="{{ route('contact') }}" class="hover:text-amber-300 transition-colors">Contact Directorate</a>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-semibold text-amber-400 hover:text-amber-300 transition-colors">
                            <span>Admin & Scorer Sign In</span> &rarr;
                        </a>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright Bar -->
            <div class="mt-8 pt-6 border-t border-emerald-950 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-3">
                <p>&copy; {{ date('Y') }} Karakoram International University Sports Gala. All rights reserved.</p>
                <div class="flex items-center space-x-4 text-[11px] text-slate-400">
                    <span>Sportsmanship &bull; Discipline &bull; Excellence</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Drawer Javascript Toggle -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-nav-menu');
            const iconClosed = document.getElementById('mobile-menu-icon-closed');
            const iconOpen = document.getElementById('mobile-menu-icon-open');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                iconClosed.classList.add('hidden');
                iconOpen.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
                iconClosed.classList.remove('hidden');
                iconOpen.classList.add('hidden');
            }
        }

        // Close dropdown when clicking outside on desktop
        document.addEventListener('click', function(event) {
            const container = document.getElementById('desktop-more-menu-container');
            const dropdown = document.getElementById('desktop-more-dropdown');
            if (container && dropdown && !container.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
