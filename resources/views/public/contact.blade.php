@extends('layouts.public')

@section('title', 'Contact Directorate of Sports - KIU Sports Gala')

@section('content')
<div class="space-y-8">
    <!-- Hero / Header Banner (Unified Royal Blue & Athletic Gold) -->
    <div class="relative bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 sm:p-10 text-center overflow-hidden shadow-xl text-white">
        <div class="relative z-10 max-w-3xl mx-auto space-y-3">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-sports font-black uppercase tracking-widest text-amber-300 bg-amber-400/20 border border-amber-400/40">
                Karakoram International University
            </span>
            <h1 class="text-3xl sm:text-5xl font-black font-sports uppercase tracking-wide text-white">
                Contact Directorate of Sports
            </h1>
            <p class="text-blue-100 text-sm sm:text-base leading-relaxed font-medium">
                Official tournament helpdesk for match schedules, athlete accreditation, venue logistics, and general inquiries.
            </p>
        </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Official Contact Points (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6">
                <div>
                    <h2 class="text-xl font-black text-slate-900 uppercase font-sports tracking-wide">
                        Contact Directorate &amp; Helpdesk
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Get in touch with the official KIU Sports Gala organizing committee
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Office Address -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Office Location</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Directorate of Sports &amp; Physical Education<br>
                            Karakoram International University<br>
                            University Road, Gilgit, Gilgit-Baltistan
                        </p>
                    </div>

                    <!-- Official Email -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Official Email</h3>
                        <a href="mailto:sports@kiu.edu.pk" class="text-xs text-blue-700 hover:text-blue-900 font-semibold underline block">
                            sports@kiu.edu.pk
                        </a>
                        <p class="text-[11px] text-slate-400">Response within 24 working hours</p>
                    </div>

                    <!-- Direct Line -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Telephone / Extension</h3>
                        <p class="text-xs text-slate-700 font-mono font-semibold">
                            +92 (05811) 960010 &bull; Ext. 234
                        </p>
                        <p class="text-[11px] text-slate-400">Direct desk helpline</p>
                    </div>

                    <!-- Operating Hours -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Desk Hours</h3>
                        <p class="text-xs text-slate-700">
                            Monday &ndash; Friday: 08:30 AM &ndash; 04:30 PM
                        </p>
                        <p class="text-[11px] text-slate-400">Closed on weekends &amp; gazetted holidays</p>
                    </div>
                </div>

                <!-- Emergency Match Officials Hotline Note -->
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 flex items-start gap-3">
                    <span class="text-base">📢</span>
                    <div>
                        <strong class="font-bold block">Match Day Emergency &amp; Scorers Line:</strong>
                        <span class="text-blue-800">
                            For urgent fixture delays, weather interruptions, or referee reporting during tournament days, contact the Scorer Control Desk at Ext. 235.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Helpful Navigation -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
                <span class="font-bold text-slate-700 uppercase tracking-wider">Quick Links:</span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('about') }}" class="text-blue-600 hover:underline font-semibold">&bull; About Gala</a>
                    <a href="{{ route('schedule') }}" class="text-blue-600 hover:underline font-semibold">&bull; Match Schedule</a>
                    <a href="{{ route('teams.public') }}" class="text-blue-600 hover:underline font-semibold">&bull; Department Teams</a>
                    <a href="{{ route('results') }}" class="text-blue-600 hover:underline font-semibold">&bull; Standings</a>
                </div>
            </div>

        </div>

        <!-- Right: Inquiry Form Card (5 cols) -->
        <div class="lg:col-span-5 bg-gradient-to-br from-blue-950 via-indigo-950 to-blue-900 border-2 border-amber-400 rounded-2xl p-6 sm:p-8 text-white shadow-xl space-y-6">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-sports font-black uppercase tracking-wider bg-amber-400/20 text-amber-300 border border-amber-400/40 mb-2">
                    Direct Inquiry
                </div>
                <h3 class="text-2xl font-black font-sports tracking-wide uppercase text-white">
                    Send a Message
                </h3>
                <p class="text-xs text-blue-200 mt-1">
                    Have questions regarding your department team, fixture timing, or player accreditation?
                </p>
            </div>

            <form action="mailto:sports@kiu.edu.pk" method="GET" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold uppercase tracking-wider text-blue-200 mb-1">Your Name</label>
                    <input type="text" placeholder="e.g. Ali Ahmed" required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-blue-900/60 border border-blue-700/80 text-white placeholder-blue-400 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400" />
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-blue-200 mb-1">Department / Faculty</label>
                    <input type="text" placeholder="e.g. Computer Science" required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-blue-900/60 border border-blue-700/80 text-white placeholder-blue-400 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400" />
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-blue-200 mb-1">Inquiry Subject</label>
                    <input type="text" name="subject" placeholder="e.g. Cricket Fixture Timing Query" required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-blue-900/60 border border-blue-700/80 text-white placeholder-blue-400 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400" />
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-blue-200 mb-1">Message Details</label>
                    <textarea rows="4" placeholder="Write your question or request..." required
                              class="w-full px-3.5 py-2.5 rounded-xl bg-blue-900/60 border border-blue-700/80 text-white placeholder-blue-400 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400"></textarea>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl font-sports font-black text-xs uppercase tracking-wider bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 shadow-md transition-all cursor-pointer">
                    Submit Inquiry &rarr;
                </button>
            </form>

            <div class="pt-4 border-t border-blue-800/80 text-center">
                <p class="text-[11px] text-blue-300">
                    Official Directorate of Sports &bull; Karakoram International University
                </p>
            </div>
        </div>

    </div>
</div>
@endsection

