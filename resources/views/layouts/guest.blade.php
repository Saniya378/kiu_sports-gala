<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KIU Sports Gala') }} &bull; Official Portal Sign In</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900|barlow-semi-condensed:700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-slate-800 bg-white selection:bg-amber-400 selection:text-slate-950">

        <!-- Split-Screen Half & Half Container -->
        <div class="min-h-screen flex flex-col lg:flex-row">

            <!-- ====================================================
                 LEFT HALF (50% on Desktop): Athletic Showcase & Branding
                 ==================================================== -->
            <div class="hidden lg:flex lg:w-1/2 bg-[#050c1f] text-white flex-col justify-between p-12 lg:p-16 relative overflow-hidden border-r border-blue-900/40 select-none">
                
                <!-- Stadium Atmospheric Lighting Effects -->
                <div class="absolute inset-0 pointer-events-none opacity-30 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-amber-400/30 via-blue-600/20 to-transparent"></div>
                <div class="absolute -top-32 -left-32 w-80 h-80 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Left Top: University & Sports Branding -->
                <div class="relative z-10 flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 via-amber-500 to-yellow-600 p-0.5 shadow-lg shadow-amber-500/20">
                        <div class="w-full h-full bg-[#0a1226] rounded-[10px] flex items-center justify-center text-amber-400">
                            <svg class="w-6 h-6 fill-current text-amber-400" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-bold uppercase tracking-widest text-amber-400">
                            Karakoram International University
                        </div>
                        <div class="text-xl font-black font-sports tracking-wider text-white uppercase">
                            KIU <span class="text-amber-400">Sports Gala</span> 2026
                        </div>
                    </div>
                </div>

                <!-- Left Middle: Hero Title & Feature Highlights -->
                <div class="relative z-10 my-auto py-12 space-y-8 max-w-lg">
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-sports font-bold tracking-wider uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Official Tournament Management</span>
                        </div>
                        
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black font-sports tracking-tight text-white leading-tight uppercase">
                            Precision Scoring. Live Coverage. Championship Glory.
                        </h2>
                        
                        <p class="text-sm sm:text-base text-blue-200/90 leading-relaxed">
                            Official match scoring, accredited athlete passes, inter-department standings, and live coverage across cricket, football, volleyball, and campus athletics.
                        </p>
                    </div>

                    <!-- 3 Athletic Feature Highlights -->
                    <div class="space-y-3.5 pt-2">
                        <div class="flex items-center space-x-3 text-sm text-slate-200 bg-white/5 border border-white/10 rounded-xl p-3.5 backdrop-blur-xs">
                            <div class="w-8 h-8 rounded-lg bg-blue-600/30 border border-blue-400/30 text-blue-300 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span class="font-medium text-xs sm:text-sm">Real-time ball-by-ball score entry & live match pad</span>
                        </div>

                        <div class="flex items-center space-x-3 text-sm text-slate-200 bg-white/5 border border-white/10 rounded-xl p-3.5 backdrop-blur-xs">
                            <div class="w-8 h-8 rounded-lg bg-blue-600/30 border border-blue-400/30 text-blue-300 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span class="font-medium text-xs sm:text-sm">Accredited digital athlete passes & QR code security</span>
                        </div>

                        <div class="flex items-center space-x-3 text-sm text-slate-200 bg-white/5 border border-white/10 rounded-xl p-3.5 backdrop-blur-xs">
                            <div class="w-8 h-8 rounded-lg bg-blue-600/30 border border-blue-400/30 text-blue-300 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <span class="font-medium text-xs sm:text-sm">Department general trophy standings & medal tallies</span>
                        </div>
                    </div>
                </div>

                <!-- Left Bottom: Directorate Footer Note -->
                <div class="relative z-10 pt-6 border-t border-blue-900/40 flex items-center justify-between text-xs text-blue-300/80">
                    <div>
                        <div class="font-semibold text-white">Directorate of Sports & Physical Education</div>
                        <div class="text-[11px] text-blue-400/70">Main Campus, Gilgit &bull; Gilgit-Baltistan</div>
                    </div>
                    <div class="text-right font-mono text-[11px] text-amber-400/90 font-bold">
                        EST. KIU GALA
                    </div>
                </div>

            </div>

            <!-- ====================================================
                 RIGHT HALF (50% on Desktop): Clean Modern Auth Form Area
                 ==================================================== -->
            <div class="w-full lg:w-1/2 flex flex-col justify-between p-6 sm:p-12 lg:p-16 bg-white min-h-screen lg:min-h-0 overflow-y-auto">

                <!-- Right Top Bar: Navigation & Mobile Logo -->
                <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                    <!-- Mobile Logo (shown only on small screens) -->
                    <div class="lg:hidden flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-lg bg-blue-900 text-amber-400 flex items-center justify-center p-1">
                            <svg class="w-5 h-5 fill-current text-amber-400" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                        <span class="font-black text-slate-900 text-base font-sports">KIU SPORTS</span>
                    </div>

                    <!-- Return to Public Portal Link -->
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-blue-700 transition-colors group">
                        <span class="transform group-hover:-translate-x-1 transition-transform">&larr;</span>
                        <span>Return to Public Portal</span>
                    </a>

                    <!-- Status Pill -->
                    <div class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Portal Active</span>
                    </div>
                </div>

                <!-- Right Middle Stage: Main Slot Content -->
                <div class="w-full max-w-md mx-auto my-auto py-8">
                    {{ $slot }}
                </div>

                <!-- Right Bottom Footer -->
                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-2">
                    <p>&copy; {{ date('Y') }} Karakoram International University</p>
                    <p class="flex items-center gap-1 text-[11px]">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Official Match Scoring Control</span>
                    </p>
                </div>

            </div>

        </div>

    </body>
</html>
