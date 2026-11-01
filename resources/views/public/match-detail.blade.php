@extends('layouts.public')

@section('title', ($fixture->team1->name ?? 'Team 1') . ' vs ' . ($fixture->team2->name ?? 'Team 2') . ' - Match Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center gap-2 text-xs md:text-sm text-emerald-200/70">
        <a href="{{ route('home') }}" class="hover:text-amber-400 transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('schedule', ['edition_id' => $fixture->edition_id, 'sport_id' => $fixture->sport_id]) }}" class="hover:text-amber-400 transition-colors">Schedule</a>
        <span>/</span>
        <span class="text-amber-400 font-medium">Match #{{ $fixture->id }}</span>
    </div>

    <!-- Match Header Card -->
    <div class="bg-gradient-to-br from-[#053d2b] to-[#022116] border border-emerald-800/80 rounded-2xl p-6 md:p-10 shadow-2xl relative overflow-hidden mb-10">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Meta Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 border-b border-emerald-800/60 text-xs md:text-sm">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1 rounded-full bg-emerald-900/80 border border-emerald-700/60 text-emerald-300 font-semibold uppercase tracking-wider">
                    {{ $fixture->sport->name ?? 'Sport' }}
                    @if($fixture->sport && $fixture->sport->category)
                        <span class="text-emerald-400 font-normal">({{ ucfirst($fixture->sport->category) }})</span>
                    @endif
                </span>

                @if($fixture->round)
                    <span class="px-3 py-1 rounded-full bg-emerald-950/60 border border-emerald-800 text-emerald-200">
                        {{ $fixture->round }}
                    </span>
                @endif

                <span class="px-3 py-1 rounded-full bg-emerald-950/60 border border-emerald-800 text-emerald-300">
                    {{ $fixture->edition->title ?? 'KIU Sports Gala' }} ({{ $fixture->edition->year ?? 'Annual' }})
                </span>
            </div>

            <div>
                @if($fixture->status === 'live')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/40 animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-red-400"></span> LIVE NOW
                    </span>
                @elseif($fixture->status === 'completed')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                        ✓ COMPLETED
                    </span>
                @elseif($fixture->status === 'cancelled')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-300 border border-gray-500/40">
                        CANCELLED
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        SCHEDULED
                    </span>
                @endif
            </div>
        </div>

        <!-- Scoreboard Teams Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-11 gap-6 items-center py-8">
            <!-- Team 1 -->
            <div class="lg:col-span-4 text-center lg:text-right">
                <div class="inline-block p-1">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">
                        {{ $fixture->team1->name ?? 'Team 1' }}
                    </h2>
                    <p class="text-sm md:text-base text-emerald-300 font-medium mt-1">
                        {{ $fixture->team1->department->name ?? 'Department of Team 1' }}
                    </p>
                    @if($fixture->result && $fixture->result->winner_team_id === $fixture->team1_id)
                        <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 text-xs font-bold">
                            🏆 WINNER
                        </span>
                    @endif
                </div>
            </div>

            <!-- Score / VS Center -->
            <div class="lg:col-span-3 text-center my-4 lg:my-0">
                @if($fixture->result)
                    <div class="inline-flex items-center justify-center gap-4 bg-[#022116]/80 px-6 py-4 rounded-2xl border border-amber-400/40 shadow-inner">
                        <span class="text-4xl md:text-5xl font-black {{ $fixture->result->winner_team_id === $fixture->team1_id ? 'text-amber-400' : 'text-white' }}">
                            {{ $fixture->result->team1_score }}
                        </span>
                        <span class="text-2xl font-light text-emerald-400">:</span>
                        <span class="text-4xl md:text-5xl font-black {{ $fixture->result->winner_team_id === $fixture->team2_id ? 'text-amber-400' : 'text-white' }}">
                            {{ $fixture->result->team2_score }}
                        </span>
                    </div>
                    <div class="mt-2 text-xs font-semibold uppercase tracking-wider text-emerald-300">
                        Final Score
                    </div>
                @else
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-tr from-amber-500/20 to-amber-400/10 border border-amber-400/40 text-amber-400 font-black text-2xl">
                        VS
                    </div>
                    <div class="mt-2 text-xs text-emerald-300/80">
                        Not Played Yet
                    </div>
                @endif
            </div>

            <!-- Team 2 -->
            <div class="lg:col-span-4 text-center lg:text-left">
                <div class="inline-block p-1">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">
                        {{ $fixture->team2->name ?? 'Team 2' }}
                    </h2>
                    <p class="text-sm md:text-base text-emerald-300 font-medium mt-1">
                        {{ $fixture->team2->department->name ?? 'Department of Team 2' }}
                    </p>
                    @if($fixture->result && $fixture->result->winner_team_id === $fixture->team2_id)
                        <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 text-xs font-bold">
                            🏆 WINNER
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Match Result Summary if any -->
        @if($fixture->result && $fixture->result->summary)
            <div class="mt-4 p-4 rounded-xl bg-[#022116]/60 border border-emerald-800/80 text-sm text-emerald-200">
                <span class="font-bold text-amber-400">Match Summary:</span> {{ $fixture->result->summary }}
            </div>
        @endif

        <!-- Venue & Time Footer Details -->
        <div class="mt-6 pt-6 border-t border-emerald-800/60 flex flex-wrap items-center justify-around gap-4 text-xs md:text-sm text-emerald-200">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span><strong>Venue:</strong> {{ $fixture->venue->name ?? 'KIU Main Sports Ground' }}</span>
            </div>

            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span><strong>Date:</strong> {{ $fixture->match_date ? \Carbon\Carbon::parse($fixture->match_date)->format('l, F j, Y') : 'Date TBA' }}</span>
            </div>

            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><strong>Time:</strong> {{ $fixture->match_time ? \Carbon\Carbon::parse($fixture->match_time)->format('h:i A') : 'Time TBA' }}</span>
            </div>
        </div>
    </div>

    <!-- Team Rosters / Squads Section -->
    <div class="mb-12">
        <h3 class="text-xl md:text-2xl font-bold text-white mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Team Squads & Rosters
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Team 1 Squad -->
            <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between pb-3 border-b border-emerald-800/60 mb-4">
                    <h4 class="font-bold text-lg text-white">{{ $fixture->team1->name ?? 'Team 1' }}</h4>
                    <span class="text-xs text-amber-400 font-semibold">{{ $fixture->team1->players->count() }} Players</span>
                </div>

                @if($fixture->team1 && $fixture->team1->players->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($fixture->team1->players as $index => $player)
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-[#022116]/60 border border-emerald-900/80 text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-800 text-xs font-bold text-emerald-200">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-semibold text-white">{{ $player->name }}</div>
                                        @if($player->reg_no)
                                            <div class="text-xs text-emerald-300/80">Reg: {{ $player->reg_no }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-emerald-300/60 italic py-4 text-center">No players registered for this team yet.</p>
                @endif
            </div>

            <!-- Team 2 Squad -->
            <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between pb-3 border-b border-emerald-800/60 mb-4">
                    <h4 class="font-bold text-lg text-white">{{ $fixture->team2->name ?? 'Team 2' }}</h4>
                    <span class="text-xs text-amber-400 font-semibold">{{ $fixture->team2->players->count() }} Players</span>
                </div>

                @if($fixture->team2 && $fixture->team2->players->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($fixture->team2->players as $index => $player)
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-[#022116]/60 border border-emerald-900/80 text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-800 text-xs font-bold text-emerald-200">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-semibold text-white">{{ $player->name }}</div>
                                        @if($player->reg_no)
                                            <div class="text-xs text-emerald-300/80">Reg: {{ $player->reg_no }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-emerald-300/60 italic py-4 text-center">No players registered for this team yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Match Photos Grid -->
    <div class="mb-12">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
            <h3 class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Match Photos & Action Gallery
                <span class="text-sm font-normal text-amber-400/90">({{ $fixture->photos->count() }})</span>
            </h3>

            @auth
                @if(in_array(auth()->user()->role, ['admin', 'scorer']))
                    <a href="{{ route('admin.fixtures.edit', $fixture) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-emerald-800 hover:bg-emerald-700 text-amber-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Upload Photos (Admin / Scorer)
                    </a>
                @endif
            @endauth
        </div>

        @if($fixture->photos->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($fixture->photos as $photo)
                    <div class="group bg-[#053d2b]/60 border border-emerald-800/80 rounded-xl overflow-hidden shadow-lg hover:border-amber-400/60 transition-all duration-300 flex flex-col">
                        <div class="relative aspect-video bg-black/40 overflow-hidden">
                            <img src="{{ $photo->image_url }}"
                                 alt="{{ $photo->caption ?? 'Match action photo' }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                            <a href="{{ $photo->image_url }}" target="_blank" rel="noopener noreferrer"
                               class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold uppercase tracking-wider gap-1.5">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Full Size
                            </a>
                        </div>
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            @if($photo->caption)
                                <p class="text-sm text-emerald-100 font-medium line-clamp-2 mb-2">
                                    {{ $photo->caption }}
                                </p>
                            @else
                                <p class="text-xs text-emerald-300/60 italic mb-2">
                                    Match action moment
                                </p>
                            @endif

                            <div class="text-[11px] text-emerald-300/60 flex items-center justify-between pt-2 border-t border-emerald-900/60">
                                <span>Uploaded {{ $photo->created_at->diffForHumans() }}</span>
                                @if($photo->uploader)
                                    <span>by {{ $photo->uploader->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-[#053d2b]/40 border border-emerald-800/60 rounded-xl p-8 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-emerald-900/60 flex items-center justify-center text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h4 class="text-base font-semibold text-white">No Match Photos Yet</h4>
                <p class="text-xs md:text-sm text-emerald-200/70 mt-1 max-w-md mx-auto">
                    Photos for this match will appear here once uploaded by tournament officials and photographers.
                </p>
            </div>
        @endif
    </div>

    <!-- Back to Schedule Link -->
    <div class="text-center pt-4">
        <a href="{{ route('schedule', ['edition_id' => $fixture->edition_id, 'sport_id' => $fixture->sport_id]) }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold hover:from-amber-400 hover:to-amber-500 transition-all shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Gala Schedule
        </a>
    </div>
</div>
@endsection

