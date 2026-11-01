@extends('layouts.public')

@section('title', 'About & Contact - KIU Sports Gala')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Hero / Header -->
    <div class="relative bg-gradient-to-br from-[#053d2b] via-[#042f20] to-[#022116] border-2 border-amber-400/80 rounded-3xl p-8 sm:p-12 text-center overflow-hidden shadow-2xl mb-12">
        <div class="relative z-10 max-w-3xl mx-auto space-y-4">
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-amber-400 text-slate-950 shadow-md">
                Karakoram International University
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                About the Annual Sports Gala
            </h1>
            <p class="text-emerald-100 text-sm sm:text-base leading-relaxed">
                The flagship collegiate tournament fostering sportsmanship, physical wellness, camaraderie, and athletic excellence across Gilgit-Baltistan.
            </p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Left 2 Cols: About & Mission (Static content user will edit) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- About Section -->
            <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-6 sm:p-8 shadow-xl">
                <h2 class="text-2xl font-black text-white mb-4 flex items-center gap-2">
                    <span class="text-amber-400">🏅</span> Tournament Overview
                </h2>
                <div class="text-emerald-100/90 text-sm sm:text-base leading-relaxed space-y-3">
                    <p>
                        The <strong>KIU Annual Sports Gala</strong> is organized each academic year by the Directorate of Sports & Physical Education at Karakoram International University. It stands as the largest university-wide sporting celebration in the region, bringing together student-athletes, faculty members, and campus departments.
                    </p>
                    <p>
                        Spanning across diverse sports disciplines—including Cricket, Football, Volleyball, Basketball, Badminton, Table Tennis, Futsal, and Athletics—the tournament provides a competitive platform for students to demonstrate discipline, teamwork, and athletic talent.
                    </p>
                </div>
            </div>

            <!-- Core Objectives -->
            <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-6 sm:p-8 shadow-xl">
                <h2 class="text-2xl font-black text-white mb-4 flex items-center gap-2">
                    <span class="text-amber-400">🎯</span> Core Objectives
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-[#022116]/80 border border-emerald-800/80">
                        <div class="text-amber-400 font-bold text-sm mb-1">Athletic Excellence</div>
                        <p class="text-xs text-emerald-200/80">
                            Scouting and nurturing athletic talent to represent KIU at national and inter-university championships.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#022116]/80 border border-emerald-800/80">
                        <div class="text-amber-400 font-bold text-sm mb-1">Campus Harmony</div>
                        <p class="text-xs text-emerald-200/80">
                            Strengthening bonds between academic disciplines and encouraging positive student engagement.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#022116]/80 border border-emerald-800/80">
                        <div class="text-amber-400 font-bold text-sm mb-1">Equal Opportunity</div>
                        <p class="text-xs text-emerald-200/80">
                            Offering structured boys and girls divisions across sports disciplines with qualified officiating.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#022116]/80 border border-emerald-800/80">
                        <div class="text-amber-400 font-bold text-sm mb-1">Physical Well-Being</div>
                        <p class="text-xs text-emerald-200/80">
                            Promoting lifelong fitness habits and mental wellness through regular competitive participation.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Facilities & Grounds -->
            <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-6 sm:p-8 shadow-xl">
                <h2 class="text-2xl font-black text-white mb-4 flex items-center gap-2">
                    <span class="text-amber-400">🏟️</span> University Sports Venues
                </h2>
                <div class="space-y-3 text-sm text-emerald-100">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></div>
                        <div>
                            <strong class="text-white">KIU Main Sports Ground:</strong>
                            <span class="text-emerald-200/80"> Home of cricket tournaments, opening ceremonies, and field athletics.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></div>
                        <div>
                            <strong class="text-white">Football & Futsal Stadium:</strong>
                            <span class="text-emerald-200/80"> Dedicated football pitch hosting departmental matches and evening kickoffs.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></div>
                        <div>
                            <strong class="text-white">Indoor Gymnasium:</strong>
                            <span class="text-emerald-200/80"> Badminton courts, table tennis tables, and indoor multi-sport facilities.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></div>
                        <div>
                            <strong class="text-white">Basketball & Volleyball Courts:</strong>
                            <span class="text-emerald-200/80"> Outdoor courts with stadium seating and floodlight illumination.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Contact Information Card -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-[#053d2b] to-[#022116] border-2 border-amber-400/80 rounded-2xl p-6 sm:p-8 shadow-2xl">
                <h3 class="text-xl font-black text-white mb-4 flex items-center gap-2">
                    <span class="text-amber-400">📞</span> Contact Directorate
                </h3>

                <p class="text-xs text-emerald-200/80 mb-6">
                    Have questions regarding team registrations, schedules, or officiating guidelines? Reach out to the organizing team:
                </p>

                <div class="space-y-4 text-xs sm:text-sm text-emerald-100">
                    <div class="flex items-start gap-3">
                        <span class="text-amber-400 text-base">📍</span>
                        <div>
                            <div class="font-bold text-white">Office Location</div>
                            <p class="text-emerald-200/80">
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
                            <p class="text-emerald-200/80">+92 (05811) 960010 - Ext. 234</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="text-amber-400 text-base">⏰</span>
                        <div>
                            <div class="font-bold text-white">Office Hours</div>
                            <p class="text-emerald-200/80">Monday &ndash; Friday: 08:30 AM &ndash; 04:30 PM</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-emerald-800/80">
                    <a href="{{ route('schedule') }}"
                       class="w-full py-2.5 px-4 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs transition shadow-md flex items-center justify-center gap-2">
                        <span>View Tournament Schedule</span> &rarr;
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-5 shadow-lg text-xs space-y-2">
                <h4 class="font-bold uppercase tracking-wider text-amber-400 mb-2">Helpful Portal Links</h4>
                <div><a href="{{ route('results') }}" class="text-emerald-200 hover:text-amber-300">&bull; Standings & Results</a></div>
                <div><a href="{{ route('hall-of-fame') }}" class="text-emerald-200 hover:text-amber-300">&bull; Hall of Fame Records</a></div>
                <div><a href="{{ route('gallery') }}" class="text-emerald-200 hover:text-amber-300">&bull; Event Photo Gallery</a></div>
                <div><a href="{{ route('archive') }}" class="text-emerald-200 hover:text-amber-300">&bull; Previous Editions Archive</a></div>
            </div>
        </div>
    </div>
</div>
@endsection
