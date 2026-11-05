@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Welcome Banner (Clean Executive Style) -->
    <div class="bg-slate-900 rounded-xl p-6 sm:p-7 text-white border border-slate-800 shadow-xs relative overflow-hidden bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-900/30 via-slate-900 to-slate-950">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                        Admin Control Center
                    </span>
                    @if($runningEdition)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-950/80 text-emerald-300 border border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ $runningEdition->title }} is Live
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                    Welcome back, {{ auth()->user()->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Manage tournament editions, department rosters, fixtures, scores, press releases, and print-ready official reports from this portal.
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <a href="{{ route('ceremony') }}" target="_blank" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-amber-300 border border-slate-700 font-semibold text-xs rounded-lg shadow-xs transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4 fill-current text-amber-400" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    <span>Closing Ceremony</span>
                </a>
                <a href="{{ route('admin.fixtures.create') }}" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-lg shadow-xs transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Schedule Match</span>
                </a>
                <a href="{{ route('admin.score-entry.index') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-lg shadow-xs transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>Score Entry</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid (Clean Corporate KPI Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Editions -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/90 shadow-xs hover:border-slate-300 transition-colors flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Editions</p>
                <p class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 mt-1">{{ $stats['editions'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Active & Archive</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>

        <!-- Departments -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/90 shadow-xs hover:border-slate-300 transition-colors flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Departments</p>
                <p class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 mt-1">{{ $stats['departments'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Faculties Enrolled</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>

        <!-- Teams & Players -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/90 shadow-xs hover:border-slate-300 transition-colors flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Teams & Squads</p>
                <p class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 mt-1">
                    {{ $stats['teams'] }}
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $stats['players'] }} registered athletes</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- Fixtures -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/90 shadow-xs hover:border-slate-300 transition-colors flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Match Fixtures</p>
                <p class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 mt-1">
                    {{ $stats['completedFixtures'] }} <span class="text-base text-slate-400 font-normal">/ {{ $stats['fixtures'] }}</span>
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">Completed / Total</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Toolbar -->
    <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 px-2 uppercase tracking-wider">
            <span>Navigation:</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.teams.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-xs font-medium text-slate-700 border border-slate-200 transition-colors">
                Browse Teams
            </a>
            <a href="{{ route('admin.sports.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-xs font-medium text-slate-700 border border-slate-200 transition-colors">
                Manage Sports
            </a>
            <a href="{{ route('admin.points-table.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-xs font-medium text-slate-700 border border-slate-200 transition-colors">
                Points Standings
            </a>
            <a href="{{ route('admin.news.create') }}" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-xs font-medium text-slate-700 border border-slate-200 transition-colors">
                Write Bulletin
            </a>
            <a href="{{ route('admin.reports.index') }}" class="px-3 py-1.5 rounded-lg bg-amber-50/70 hover:bg-amber-100/70 text-xs font-semibold text-amber-900 border border-amber-200 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Print Reports</span>
            </a>
            <a href="{{ url('/') }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-blue-50/70 hover:bg-blue-100/70 text-xs font-semibold text-blue-900 border border-blue-200 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span>Public Portal &rarr;</span>
            </a>
        </div>
    </div>

    <!-- Main Content Two-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Col 1 & 2: Today's Action & Live Schedule -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span>Today's Tournament Fixtures</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ now()->format('l, F j, Y') }} &bull; Today's match schedule
                        </p>
                    </div>
                    <a href="{{ route('admin.fixtures.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                        View All Fixtures &rarr;
                    </a>
                </div>

                @if($todayFixtures->isNotEmpty())
                    <div class="divide-y divide-slate-100 text-sm">
                        @foreach($todayFixtures as $f)
                            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-700 uppercase">
                                            {{ $f->sport->name }} ({{ ucfirst($f->sport->category) }})
                                        </span>
                                        @if($f->status === 'live')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                                                ● LIVE
                                            </span>
                                        @elseif($f->status === 'completed')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                                ✓ Completed
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700">
                                                Scheduled
                                            </span>
                                        @endif
                                        <span class="text-xs text-slate-400">&bull; {{ $f->match_time ? \Carbon\Carbon::parse($f->match_time)->format('h:i A') : 'Time TBA' }}</span>
                                    </div>

                                    <div class="font-bold text-slate-900 text-base">
                                        {{ $f->team1->name }} <span class="text-xs font-semibold text-slate-400">vs</span> {{ $f->team2->name }}
                                    </div>
                                    <div class="text-xs text-slate-500 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ $f->venue->name ?? 'Main Ground' }} &bull; {{ $f->round ?? 'Match #' . $f->id }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    @if($f->result)
                                        <div class="text-right">
                                            <span class="font-mono font-bold text-lg text-slate-900">
                                                {{ $f->result->team1_score }} - {{ $f->result->team2_score }}
                                            </span>
                                            <div class="text-[10px] text-emerald-600 font-semibold">Final Score</div>
                                        </div>
                                    @endif
                                    <a href="{{ route('admin.score-entry.index', ['fixture_id' => $f->id]) }}"
                                       class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200 transition-colors">
                                        Update Score
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-slate-400">
                        <p class="text-sm">No matches scheduled for today.</p>
                        <a href="{{ route('admin.fixtures.create') }}" class="text-xs font-semibold text-blue-600 hover:underline mt-2 inline-block">
                            + Schedule a new fixture for today
                        </a>
                    </div>
                @endif
            </div>

            <!-- Recent Completed Matches -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">Recent Completed Results</h2>
                    <a href="{{ route('admin.points-table.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                        Points Table &rarr;
                    </a>
                </div>

                @if($recentResults->isNotEmpty())
                    <div class="divide-y divide-slate-100 text-sm">
                        @foreach($recentResults as $r)
                            <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div>
                                    <div class="text-xs text-slate-400">
                                        {{ $r->sport->name }} &bull; {{ $r->match_date ? \Carbon\Carbon::parse($r->match_date)->format('M d, Y') : '' }}
                                    </div>
                                    <div class="font-bold text-slate-900 mt-0.5">
                                        {{ $r->team1->name }} vs {{ $r->team2->name }}
                                    </div>
                                    @if($r->result && $r->result->winnerTeam)
                                        <div class="text-xs text-amber-700 font-semibold mt-0.5">
                                            Winner: {{ $r->result->winnerTeam->name }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold text-base text-slate-900">
                                        {{ $r->result->team1_score }} - {{ $r->result->team2_score }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center text-slate-400 text-sm">
                        No completed matches recorded yet.
                    </div>
                @endif
            </div>
        </div>

        <!-- Col 3: Bulletins & Quick Administrative Tasks -->
        <div class="space-y-6">
            <!-- News & Announcements Widget -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Latest Bulletins</h3>
                    <a href="{{ route('admin.news.create') }}" class="text-xs font-semibold text-blue-600 hover:underline">
                        + New
                    </a>
                </div>

                @if($recentNews->isNotEmpty())
                    <div class="divide-y divide-slate-100">
                        @foreach($recentNews as $newsItem)
                            <div class="p-4 hover:bg-slate-50 transition-colors">
                                <span class="text-[10px] text-slate-400 uppercase font-semibold">
                                    {{ $newsItem->published_at ? $newsItem->published_at->format('M d, Y') : '' }}
                                </span>
                                <h4 class="font-bold text-slate-800 text-sm mt-0.5 line-clamp-2">
                                    <a href="{{ route('admin.news.edit', $newsItem) }}" class="hover:text-blue-600">
                                        {{ $newsItem->title }}
                                    </a>
                                </h4>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center text-slate-400 text-xs">
                        No news articles published yet.
                    </div>
                @endif
            </div>

            <!-- Official Report Quick Links -->
            <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <h3 class="font-bold text-slate-900 text-sm">Official Print Center</h3>
                </div>
                <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                    Generate formatted A4 print-ready reports with university letterhead for tournament meetings:
                </p>
                <div class="space-y-2 text-xs">
                    <a href="{{ route('admin.reports.winners') }}" class="block p-2.5 rounded-lg bg-white hover:bg-slate-100 text-slate-800 font-medium border border-slate-200 transition-colors">
                        &bull; Winners List by Edition &rarr;
                    </a>
                    <a href="{{ route('admin.reports.fixtures') }}" class="block p-2.5 rounded-lg bg-white hover:bg-slate-100 text-slate-800 font-medium border border-slate-200 transition-colors">
                        &bull; Daily Match Fixtures by Date &rarr;
                    </a>
                    <a href="{{ route('admin.reports.teams') }}" class="block p-2.5 rounded-lg bg-white hover:bg-slate-100 text-slate-800 font-medium border border-slate-200 transition-colors">
                        &bull; Department Teams &amp; Player Rosters &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
