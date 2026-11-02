@extends('layouts.public')

@section('title', 'About & Contact - KIU Sports Gala')

@section('content')
<div class="space-y-8">
    <!-- Hero / Header Banner -->
    <div class="relative bg-gradient-to-br from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400/80 rounded-3xl p-8 sm:p-12 text-center overflow-hidden shadow-xl text-white">
        <div class="relative z-10 max-w-3xl mx-auto space-y-4">
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-amber-400 text-slate-950 shadow-md">
                Karakoram International University
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                About the Annual Sports Gala
            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                The flagship collegiate tournament fostering sportsmanship, physical wellness, camaraderie, and athletic excellence across Gilgit-Baltistan.
            </p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Cols: About & Mission -->
        <div class="lg:col-span-2 space-y-8">
            <!-- About Section -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs">
                <h2 class="text-2xl font-black text-slate-900 mb-4 flex items-center gap-2">
                    <span class="text-amber-500">🏅</span>
                    <span>Tournament Overview</span>
                </h2>
                <div class="text-slate-600 text-sm sm:text-base leading-relaxed space-y-3">
                    <p>
                        The <strong>KIU Annual Sports Gala</strong> is organized each academic year by the Directorate of Sports & Physical Education at Karakoram International University. It stands as the largest university-wide sporting celebration in the region, bringing together student-athletes, faculty members, and campus departments.
                    </p>
                    <p>
                        Spanning across diverse sports disciplines—including Cricket, Football, Volleyball, Basketball, Badminton, Table Tennis, Futsal, and Athletics—the tournament provides a competitive platform for students to demonstrate discipline, teamwork, and athletic talent.
                    </p>
                </div>
            </div>

            <!-- Core Objectives -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs">
                <h2 class="text-2xl font-black text-slate-900 mb-4 flex items-center gap-2">
                    <span class="text-amber-500">🎯</span>
                    <span>Core Objectives</span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-blue-700 font-bold text-sm mb-1">Athletic Excellence</div>
                        <p class="text-xs text-slate-600">
                            Scouting and nurturing athletic talent to represent KIU at national and inter-university championships.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-blue-700 font-bold text-sm mb-1">Campus Harmony</div>
                        <p class="text-xs text-slate-600">
                            Strengthening bonds between academic disciplines and encouraging positive student engagement.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-blue-700 font-bold text-sm mb-1">Equal Opportunity</div>
                        <p class="text-xs text-slate-600">
                            Offering structured boys and girls divisions across sports disciplines with qualified officiating.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-blue-700 font-bold text-sm mb-1">Physical Well-Being</div>
                        <p class="text-xs text-slate-600">
                            Promoting lifelong fitness habits and mental wellness through regular competitive participation.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Facilities & Grounds -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs">
                <h2 class="text-2xl font-black text-slate-900 mb-4 flex items-center gap-2">
                    <span class="text-amber-500">🏟️</span>
                    <span>University Sports Venues</span>
                </h2>
                <div class="space-y-3 text-sm text-slate-700">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></div>
                        <div>
                            <strong class="text-slate-900">KIU Main Sports Ground:</strong>
                            <span class="text-slate-600"> Home of cricket tournaments, opening ceremonies, and field athletics.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></div>
                        <div>
                            <strong class="text-slate-900">Football & Futsal Stadium:</strong>
                            <span class="text-slate-600"> Dedicated football pitch hosting departmental matches and evening kickoffs.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></div>
                        <div>
                            <strong class="text-slate-900">Indoor Gymnasium:</strong>
                            <span class="text-slate-600"> Badminton courts, table tennis tables, and indoor multi-sport facilities.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></div>
                        <div>
                            <strong class="text-slate-900">Basketball & Volleyball Courts:</strong>
                            <span class="text-slate-600"> Outdoor courts with stadium seating and floodlight illumination.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Contact Information Card -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400/80 rounded-2xl p-6 sm:p-8 shadow-xl text-white">
                <h3 class="text-xl font-black text-white mb-4 flex items-center gap-2">
                    <span class="text-amber-400">📞</span>
                    <span>Contact Directorate</span>
                </h3>

                <p class="text-xs text-slate-300 mb-6">
                    Have questions regarding team registrations, schedules, or officiating guidelines? Reach out to the organizing team:
                </p>

                <div class="space-y-4 text-xs sm:text-sm text-slate-200">
                    <div class="flex items-start gap-3">
                        <span class="text-amber-400 text-base">📍</span>
                        <div>
                            <div class="font-bold text-white">Office Location</div>
                            <p class="text-slate-300">
                                Directorate of Sports & Physical Education<br>
                                Karakoram International University<br>
                                University Road, Gilgit, Gilgit-Baltistan
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="text-amber-400 text-base">✉️</span>
                        <div>
                            <div class="font-bold text-white">Email Inquiries</div>
                            <a href="mailto:sports@kiu.edu.pk" class="text-amber-300 hover:underline">
                                sports@kiu.edu.pk
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="text-amber-400 text-base">☎️</span>
                        <div>
                            <div class="font-bold text-white">Telephone / Extension</div>
                            <p class="text-slate-300">+92 (05811) 960010 - Ext. 234</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="text-amber-400 text-base">⏰</span>
                        <div>
                            <div class="font-bold text-white">Office Hours</div>
                            <p class="text-slate-300">Monday &ndash; Friday: 08:30 AM &ndash; 04:30 PM</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-700">
                    <a href="{{ route('schedule') }}"
                       class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-bold text-xs transition shadow-md flex items-center justify-center gap-2">
                        <span>View Tournament Schedule</span> &rarr;
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs text-xs space-y-2">
                <h4 class="font-bold uppercase tracking-wider text-slate-900 mb-2">Helpful Portal Links</h4>
                <div><a href="{{ route('results') }}" class="text-blue-600 hover:text-blue-800">&bull; Standings & Results</a></div>
                <div><a href="{{ route('hall-of-fame') }}" class="text-blue-600 hover:text-blue-800">&bull; Hall of Fame Records</a></div>
                <div><a href="{{ route('gallery') }}" class="text-blue-600 hover:text-blue-800">&bull; Event Photo Gallery</a></div>
                <div><a href="{{ route('archive') }}" class="text-blue-600 hover:text-blue-800">&bull; Previous Editions Archive</a></div>
            </div>
        </div>
    </div>
</div>
@endsection
