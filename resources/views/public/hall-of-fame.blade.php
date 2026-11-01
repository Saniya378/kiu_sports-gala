@extends('layouts.public')

@section('title', 'Hall of Fame & Roll of Honor - KIU Sports Gala')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Hall of Fame Hero -->
    <div class="relative bg-gradient-to-br from-[#053d2b] via-[#042f20] to-[#022116] border-2 border-amber-400/80 rounded-3xl p-8 sm:p-12 text-center overflow-hidden shadow-2xl mb-12">
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto space-y-4">
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-amber-400 text-slate-950 shadow-md">
                <span>🏆</span> University Athletic Roll of Honor
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                KIU Sports Hall of Fame
            </h1>
            <p class="text-emerald-100 text-sm sm:text-base leading-relaxed">
                Celebrating departmental athletic supremacy, championship titles, and memorable rivalries across all editions of the Karakoram International University Sports Gala.
            </p>
        </div>
    </div>

    <!-- All-Time Department Medal Standings (if any winners exist) -->
    @if($allTimeLeaderboard->isNotEmpty())
        <div class="mb-14">
            <div class="flex items-center justify-between mb-6 pb-2 border-b border-emerald-800/60">
                <h2 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    All-Time Department Championship Leaderboard
                </h2>
                <span class="text-xs font-bold text-amber-400 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/20">
                    All Editions Combined
                </span>
            </div>

            <div class="bg-[#053d2b]/80 border border-emerald-800/80 rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-[#022116] text-emerald-300 text-xs uppercase tracking-wider border-b border-emerald-800/80">
                                <th class="py-3.5 px-4 font-bold text-center w-16">Rank</th>
                                <th class="py-3.5 px-6 font-bold">Academic Department</th>
                                <th class="py-3.5 px-4 font-bold text-center text-amber-400">🏆 Gold (Champions)</th>
                                <th class="py-3.5 px-4 font-bold text-center text-slate-300">🥈 Silver (Runners-Up)</th>
                                <th class="py-3.5 px-4 font-bold text-center text-emerald-200">Total Podiums</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-800/50">
                            @foreach($allTimeLeaderboard as $index => $row)
                                <tr class="hover:bg-emerald-800/30 transition-colors {{ $index === 0 ? 'bg-amber-400/10' : '' }}">
                                    <td class="py-3.5 px-4 text-center font-black">
                                        @if($index === 0)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black text-xs shadow">
                                                1
                                            </span>
                                        @elseif($index === 1)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-300 text-slate-950 font-black text-xs shadow">
                                                2
                                            </span>
                                        @elseif($index === 2)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700 text-white font-black text-xs shadow">
                                                3
                                            </span>
                                        @else
                                            <span class="text-xs text-emerald-300/80 font-bold">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6 font-bold text-white text-base">
                                        {{ $row['department']->name }}
                                        @if($index === 0)
                                            <span class="ml-2 text-[10px] uppercase font-black px-2 py-0.5 rounded bg-amber-400 text-slate-950">
                                                All-Time Leader
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-black text-amber-400 text-base">
                                        {{ $row['gold'] }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-300 text-base">
                                        {{ $row['silver'] }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-black text-white text-base">
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
    <div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-emerald-800/60 pb-3">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-white flex items-center gap-2">
                    <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Roll of Honor: Per Sport Records
                </h2>
                <p class="text-xs md:text-sm text-emerald-200/80 mt-1">
                    Complete chronicle of champions and finalists across every sport discipline.
                </p>
            </div>

            <!-- Quick Sport Jump Pills -->
            @if($sportsWithWinners->isNotEmpty())
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-xs text-emerald-300 font-semibold mr-1">Jump to:</span>
                    @foreach($sportsWithWinners as $sp)
                        <a href="#sport-{{ $sp->id }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#022116] hover:bg-emerald-800 text-emerald-200 hover:text-amber-300 border border-emerald-800 transition-colors">
                            {{ $sp->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @if($sportsWithWinners->isNotEmpty())
            <div class="space-y-12">
                @foreach($sportsWithWinners as $sport)
                    @php
                        $records = $winnersBySport->get($sport->id, collect());
                    @endphp

                    <div id="sport-{{ $sport->id }}" class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-6 shadow-xl scroll-mt-24">
                        <!-- Sport Title Header -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 mb-5 border-b border-emerald-800/60">
                            <div>
                                <h3 class="text-xl md:text-2xl font-black text-white flex items-center gap-2.5">
                                    <span class="text-amber-400">🏆</span>
                                    {{ $sport->name }}
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-900 border border-emerald-700 text-emerald-300 uppercase tracking-wider">
                                        {{ ucfirst($sport->category) }}
                                    </span>
                                </h3>
                                @if($sport->rules)
                                    <p class="text-xs text-emerald-300/70 mt-1">
                                        {{ $sport->rules }}
                                    </p>
                                @endif
                            </div>

                            <span class="text-xs font-bold text-amber-300 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/20">
                                {{ $records->count() }} Editions Contested
                            </span>
                        </div>

                        <!-- Editions Record Cards/Table -->
                        @if($records->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($records as $rec)
                                    <div class="bg-[#022116]/80 border border-emerald-800/80 rounded-xl p-4 shadow-md flex flex-col justify-between hover:border-amber-400/50 transition-all">
                                        <div>
                                            <!-- Edition Year Badge -->
                                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-emerald-900/60 text-xs">
                                                <a href="{{ route('archive', ['year' => $rec->edition->year ?? '']) }}" class="font-bold text-amber-400 hover:text-amber-300 flex items-center gap-1">
                                                    <span>{{ $rec->edition->year ?? 'Year N/A' }} Gala</span>
                                                    <span class="text-[10px] text-emerald-300">({{ $rec->edition->title ?? '' }})</span>
                                                </a>
                                                @if($rec->final_score)
                                                    <span class="font-mono text-xs font-bold text-emerald-200 bg-emerald-950 px-2 py-0.5 rounded border border-emerald-800">
                                                        {{ $rec->final_score }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Champion -->
                                            <div class="p-3 rounded-lg bg-amber-400/10 border border-amber-400/30 mb-2">
                                                <div class="text-[10px] uppercase font-bold tracking-wider text-amber-400 flex items-center gap-1">
                                                    <span>🏆</span> Champion
                                                </div>
                                                <div class="font-extrabold text-white text-base mt-0.5">
                                                    {{ $rec->winnerDepartment->name ?? 'Department Champion' }}
                                                </div>
                                            </div>

                                            <!-- Runner Up -->
                                            @if($rec->runnerUpDepartment)
                                                <div class="p-3 rounded-lg bg-emerald-950/60 border border-emerald-900/80">
                                                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-300 flex items-center gap-1">
                                                        <span>🥈</span> Runner-Up
                                                    </div>
                                                    <div class="font-semibold text-slate-200 text-sm mt-0.5">
                                                        {{ $rec->runnerUpDepartment->name }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-3 pt-2 border-t border-emerald-900/40 text-right">
                                            <a href="{{ route('archive', ['year' => $rec->edition->year ?? '']) }}" class="text-[11px] font-bold text-emerald-300 hover:text-amber-300">
                                                View Gala Archive &rarr;
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-emerald-300/60 italic">No championship records logged for this sport yet.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-400/10 flex items-center justify-center text-amber-400">
                    <span class="text-3xl">🏆</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Hall of Fame Records In Progress</h3>
                <p class="text-sm text-emerald-200/80 max-w-md mx-auto">
                    Historical championship data from tournament editions is currently being verified and compiled into the official university archive.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

