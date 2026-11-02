@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header Welcome Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg border border-slate-800 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        Admin Control Center
                    </span>
                    @if($runningEdition)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ $runningEdition->title }} is Live
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Welcome back, {{ auth()->user()->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl">
                    Manage tournament editions, department rosters, fixtures, scores, press releases, and print-ready official reports from this portal.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <a href="{{ route('admin.fixtures.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Schedule Match
                </a>
                <a href="{{ route('admin.score-entry.index') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Score Entry
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Editions</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['editions'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                🏆
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Departments</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['departments'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                🏛️
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Teams & Players</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['teams'] }} <span class="text-xs font-normal text-slate-400">({{ $stats['players'] }} players)</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                👥
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fixtures</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['completedFixtures'] }} / {{ $stats['fixtures'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                ⚽
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts Row -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider px-2">Quick Navigation:</span>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.teams.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                Browse Teams
            </a>
            <a href="{{ route('admin.sports.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                Manage Sports
            </a>
            <a href="{{ route('admin.points-table.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                Points Standings
            </a>
            <a href="{{ route('admin.news.create') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                Write Bulletin
            </a>
            <a href="{{ route('admin.reports.index') }}" class="px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-xs font-bold text-amber-800 border border-amber-200 transition flex items-center gap-1">
                <span>🖨️</span> Print Reports
            </a>
            <a href="{{ url('/') }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-xs font-bold text-indigo-700 border border-indigo-200 transition flex items-center gap-1">
                <span>🌐</span> Public Portal &rarr;
            </a>
        </div>
    </div>

    <!-- Main Content Two-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Col 1 & 2: Today's Action & Live Schedule -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span>Today's Tournament Fixtures</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ now()->format('l, F j, Y') }} &bull; Showing today's matches
                        </p>
                    </div>
                    <a href="{{ route('admin.fixtures.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                        View All Fixtures &rarr;
                    </a>
                </div>

                @if($todayFixtures->isNotEmpty())
                    <div class="divide-y divide-slate-100 text-sm">
                        @foreach($todayFixtures as $f)
                            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 transition">
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
                                    <div class="text-xs text-slate-500">
                                        📍 {{ $f->venue->name ?? 'Main Ground' }} &bull; {{ $f->round ?? 'Match #' . $f->id }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    @if($f->result)
                                        <div class="text-right">
                                            <span class="font-mono font-black text-lg text-slate-900">
                                                {{ $f->result->team1_score }} - {{ $f->result->team2_score }}
                                            </span>
                                            <div class="text-[10px] text-emerald-600 font-semibold">Final Score</div>
                                        </div>
                                    @endif
                                    <a href="{{ route('admin.score-entry.index', ['fixture_id' => $f->id]) }}"
                                       class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition">
                                        Update Score
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-slate-400">
                        <p class="text-sm">No matches scheduled for today.</p>
                        <a href="{{ route('admin.fixtures.create') }}" class="text-xs font-bold text-indigo-600 hover:underline mt-2 inline-block">
                            + Schedule a new fixture for today
                        </a>
                    </div>
                @endif
            </div>

            <!-- Recent Completed Matches -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800">Recent Completed Results</h2>
                    <a href="{{ route('admin.points-table.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                        Points Table &rarr;
                    </a>
                </div>

                @if($recentResults->isNotEmpty())
                    <div class="divide-y divide-slate-100 text-sm">
                        @foreach($recentResults as $r)
                            <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                                <div>
                                    <div class="text-xs text-slate-400">
                                        {{ $r->sport->name }} &bull; {{ $r->match_date ? \Carbon\Carbon::parse($r->match_date)->format('M d, Y') : '' }}
                                    </div>
                                    <div class="font-bold text-slate-900 mt-0.5">
                                        {{ $r->team1->name }} vs {{ $r->team2->name }}
                                    </div>
                                    @if($r->result && $r->result->winnerTeam)
                                        <div class="text-xs text-amber-600 font-semibold mt-0.5">
                                            Winner: {{ $r->result->winnerTeam->name }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-black text-base text-slate-800">
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
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-base">Latest Bulletins</h3>
                    <a href="{{ route('admin.news.create') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                        + New
                    </a>
                </div>

                @if($recentNews->isNotEmpty())
                    <div class="divide-y divide-slate-100">
                        @foreach($recentNews as $newsItem)
                            <div class="p-4 hover:bg-slate-50 transition">
                                <span class="text-[10px] text-slate-400 uppercase font-semibold">
                                    {{ $newsItem->published_at ? $newsItem->published_at->format('M d, Y') : '' }}
                                </span>
                                <h4 class="font-bold text-slate-800 text-sm mt-0.5 line-clamp-2">
                                    <a href="{{ route('admin.news.edit', $newsItem) }}" class="hover:text-indigo-600">
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
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-lg">🖨️</span>
                    <h3 class="font-black text-amber-950 text-sm">Official Print Center</h3>
                </div>
                <p class="text-xs text-amber-800/90 mb-4 leading-relaxed">
                    Generate formatted A4 print-ready reports with university letterhead for tournament meetings:
                </p>
                <div class="space-y-2 text-xs">
                    <a href="{{ route('admin.reports.winners') }}" class="block p-2 rounded-lg bg-white/80 hover:bg-white text-slate-800 font-semibold border border-amber-200/60 shadow-2xs transition">
                        &bull; Winners List by Edition &rarr;
                    </a>
                    <a href="{{ route('admin.reports.fixtures') }}" class="block p-2 rounded-lg bg-white/80 hover:bg-white text-slate-800 font-semibold border border-amber-200/60 shadow-2xs transition">
                        &bull; Daily Match Fixtures by Date &rarr;
                    </a>
                    <a href="{{ route('admin.reports.teams') }}" class="block p-2 rounded-lg bg-white/80 hover:bg-white text-slate-800 font-semibold border border-amber-200/60 shadow-2xs transition">
                        &bull; Department Teams &amp; Player Rosters &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
