@extends('layouts.public')

@section('title', 'Hall of Fame & Roll of Honor - KIU Sports Gala')

@section('content')
<div class="space-y-10">
    <!-- Hall of Fame Hero -->
    <div class="relative bg-gradient-to-br from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400/80 rounded-3xl p-8 sm:p-12 text-center overflow-hidden shadow-xl text-white">
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto space-y-4">
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-amber-400 text-slate-950 shadow-md">
                <span>🏆</span> University Athletic Roll of Honor
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                KIU Sports Hall of Fame
            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                Celebrating departmental athletic supremacy, championship titles, and memorable rivalries across all editions of the Karakoram International University Sports Gala.
            </p>
        </div>
    </div>

    <!-- All-Time Department Medal Standings (if any winners exist) -->
    @if($allTimeLeaderboard->isNotEmpty())
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <h2 class="text-xl md:text-2xl font-black text-slate-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>All-Time Department Championship Leaderboard</span>
                </h2>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                    All Editions Combined
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-900 text-slate-200 text-xs uppercase tracking-wider border-b border-slate-800">
                                <th class="py-3.5 px-4 font-bold text-center w-16">Rank</th>
                                <th class="py-3.5 px-6 font-bold">Academic Department</th>
                                <th class="py-3.5 px-4 font-bold text-center text-amber-400">🏆 Gold (Champions)</th>
                                <th class="py-3.5 px-4 font-bold text-center text-slate-300">🥈 Silver (Runners-Up)</th>
                                <th class="py-3.5 px-4 font-bold text-center text-slate-100">Total Podiums</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($allTimeLeaderboard as $index => $row)
                                <tr class="hover:bg-slate-50 transition-colors {{ $index === 0 ? 'bg-amber-50/50' : '' }}">
                                    <td class="py-3.5 px-4 text-center font-black">
                                        @if($index === 0)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black text-xs shadow-sm">
                                                1
                                            </span>
                                        @elseif($index === 1)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-800 font-black text-xs shadow-sm">
                                                2
                                            </span>
                                        @elseif($index === 2)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700 text-white font-black text-xs shadow-sm">
                                                3
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-500 font-bold">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6 font-bold text-slate-900 text-base">
                                        {{ $row['department']->name }}
                                        @if($index === 0)
                                            <span class="ml-2 text-[10px] uppercase font-black px-2 py-0.5 rounded bg-amber-400 text-slate-950">
                                                All-Time Leader
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-black text-amber-600 text-base">
                                        {{ $row['gold'] }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-600 text-base">
                                        {{ $row['silver'] }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-black text-slate-900 text-base">
                                        {{ $row['total'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Per Sport Historical Winners and Runner-Ups -->
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-slate-900 flex items-center gap-2">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Roll of Honor: Per Sport Records</span>
                </h2>
                <p class="text-xs md:text-sm text-slate-500 mt-1">
                    Complete chronicle of champions and finalists across every sport discipline.
                </p>
            </div>

            <!-- Quick Sport Jump Pills -->
            @if($sportsWithWinners->isNotEmpty())
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-xs text-slate-500 font-semibold mr-1">Jump to:</span>
                    @foreach($sportsWithWinners as $sp)
                        <a href="#sport-{{ $sp->id }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-blue-600 border border-slate-200 transition-colors">
                            {{ $sp->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @if($sportsWithWinners->isNotEmpty())
            <div class="space-y-8">
                @foreach($sportsWithWinners as $sport)
                    @php
                        $records = $winnersBySport->get($sport->id, collect());
                    @endphp

                    <div id="sport-{{ $sport->id }}" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs scroll-mt-24">
                        <!-- Sport Title Header -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 mb-5 border-b border-slate-100">
                            <div>
                                <h3 class="text-xl md:text-2xl font-black text-slate-900 flex items-center gap-2.5">
                                    <span class="text-amber-500">🏆</span>
                                    <span>{{ $sport->name }}</span>
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-blue-700 uppercase tracking-wider">
                                        {{ ucfirst($sport->category) }}
                                    </span>
                                </h3>
                                @if($sport->rules)
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $sport->rules }}
                                    </p>
                                @endif
                            </div>

                            <span class="text-xs font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                                {{ $records->count() }} Editions Contested
                            </span>
                        </div>

                        <!-- Editions Record Cards/Table -->
                        @if($records->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($records as $rec)
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 shadow-2xs flex flex-col justify-between hover:border-blue-400 transition-all">
                                        <div>
                                            <!-- Edition Year Badge -->
                                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200 text-xs">
                                                <a href="{{ route('archive', ['year' => $rec->edition->year ?? '']) }}" class="font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                    <span>{{ $rec->edition->year ?? 'Year N/A' }} Gala</span>
                                                    <span class="text-[10px] text-slate-500">({{ $rec->edition->title ?? '' }})</span>
                                                </a>
                                                @if($rec->final_score)
                                                    <span class="font-mono text-xs font-bold text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-200">
                                                        {{ $rec->final_score }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Champion -->
                                            <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 mb-2">
                                                <div class="text-[10px] uppercase font-bold tracking-wider text-amber-800 flex items-center gap-1">
                                                    <span>🏆</span> Champion
                                                </div>
                                                <div class="font-extrabold text-slate-900 text-base mt-0.5">
                                                    {{ $rec->winnerDepartment->name ?? 'Department Champion' }}
                                                </div>
                                            </div>

                                            <!-- Runner Up -->
                                            @if($rec->runnerUpDepartment)
                                                <div class="p-3 rounded-lg bg-white border border-slate-200">
                                                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 flex items-center gap-1">
                                                        <span>🥈</span> Runner-Up
                                                    </div>
                                                    <div class="font-semibold text-slate-800 text-sm mt-0.5">
                                                        {{ $rec->runnerUpDepartment->name }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-3 pt-2 border-t border-slate-200 text-right">
                                            <a href="{{ route('archive', ['year' => $rec->edition->year ?? '']) }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800">
                                                View Gala Archive &rarr;
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-400 italic">No championship records logged for this sport yet.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-xs">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-500">
                    <span class="text-3xl">🏆</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Hall of Fame Records In Progress</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto">
                    Historical championship data from tournament editions is currently being verified and compiled into the official university archive.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
