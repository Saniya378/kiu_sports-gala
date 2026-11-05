@extends('layouts.public')

@section('title', ($fixture->team1->name ?? 'Team 1') . ' vs ' . ($fixture->team2->name ?? 'Team 2') . ' - Match Details')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs md:text-sm text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('schedule', ['edition_id' => $fixture->edition_id, 'sport_id' => $fixture->sport_id]) }}" class="hover:text-blue-600 transition-colors">Schedule</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold">Match #{{ $fixture->id }}</span>
    </div>

    <!-- Match Header Card (Unified Royal Blue & Athletic Gold Theme) -->
    <div class="bg-gradient-to-br from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 md:p-10 shadow-xl relative overflow-hidden text-white">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Meta Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 border-b border-blue-800/80 text-xs md:text-sm">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1 rounded-full bg-blue-950/80 border border-blue-700/60 text-amber-300 font-sports font-bold uppercase tracking-wider">
                    {{ $fixture->sport->name ?? 'Sport' }}
                    @if($fixture->sport && $fixture->sport->category)
                        <span class="text-blue-200 font-normal">({{ ucfirst($fixture->sport->category) }})</span>
                    @endif
                </span>

                @if($fixture->round)
                    <span class="px-3 py-1 rounded-full bg-blue-950/80 border border-blue-700 text-slate-200">
                        {{ $fixture->round }}
                    </span>
                @endif

                <span class="px-3 py-1 rounded-full bg-blue-950/80 border border-blue-700 text-amber-300">
                    {{ $fixture->edition->title ?? 'KIU Sports Gala' }} ({{ $fixture->edition->year ?? 'Annual' }})
                </span>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <div id="live-sync-indicator" class="hidden sm:flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-900/80 border border-slate-700 text-[11px] text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Live Sync: <span id="last-sync-time" class="text-slate-300 font-mono">Connecting...</span></span>
                </div>

                <div id="status-badge-container">
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
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/40">
                            SCHEDULED
                        </span>
                    @endif
                </div>

                <!-- WhatsApp One-Click Match Share Button -->
                <a id="whatsapp-share-btn" href="{{ $fixture->whatsapp_share_url }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-[#25D366] hover:bg-[#20bd5a] text-slate-950 shadow-md shadow-emerald-950/40 hover:scale-105 active:scale-95 transition-all"
                   title="Share match update on WhatsApp">
                    <svg class="w-3.5 h-3.5 fill-current text-slate-950 shrink-0" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.044c.101-.116.433-.506.549-.68.116-.174.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.392-10.416c-5.523 0-10 4.477-10 10 0 1.765.459 3.424 1.261 4.871l-1.341 4.897 5.024-1.317c1.401.764 3.003 1.196 4.707 1.196 5.523 0 10-4.477 10-10s-4.477-10-10-10z"/>
                    </svg>
                    <span>Share on WhatsApp</span>
                </a>
            </div>
        </div>

        <!-- Scoreboard Teams Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-11 gap-6 items-center py-8">
            <!-- Team 1 -->
            <div class="lg:col-span-4 text-center lg:text-right">
                <div class="inline-flex flex-col lg:flex-row-reverse items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-slate-900 to-indigo-900 border-2 border-amber-400/80 text-amber-400 font-sports font-black flex items-center justify-center text-lg shadow-lg shrink-0">
                        {{ $fixture->team1->department->code ?? substr($fixture->team1->name ?? 'T1', 0, 3) }}
                    </div>
                    <div class="text-center lg:text-right">
                        <h2 class="text-2xl md:text-3xl font-sports font-black text-white tracking-tight uppercase">
                            {{ $fixture->team1->name ?? 'Team 1' }}
                        </h2>
                        <p class="text-xs md:text-sm text-blue-200 font-medium mt-0.5">
                            {{ $fixture->team1->department->name ?? 'Department of Team 1' }}
                        </p>
                        <div id="team1-winner-badge">
                            @if($fixture->result && $fixture->result->winner_team_id === $fixture->team1_id)
                                <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 text-xs font-bold">
                                    🏆 WINNER
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Score / VS Center -->
            <div class="lg:col-span-3 text-center my-4 lg:my-0" id="center-score-container">
                <div class="inline-flex items-center justify-center gap-4 bg-[#040813]/90 px-6 py-4 rounded-2xl border border-amber-400/50 shadow-inner">
                    <span id="team1-score-display" class="text-4xl md:text-5xl font-black transition-all duration-300 {{ $fixture->result && $fixture->result->winner_team_id === $fixture->team1_id ? 'text-amber-400' : 'text-white' }}">
                        {{ $fixture->result ? $fixture->formatTeamScore(1) : '-' }}
                    </span>
                    <span class="text-2xl font-light text-slate-500">:</span>
                    <span id="team2-score-display" class="text-4xl md:text-5xl font-black transition-all duration-300 {{ $fixture->result && $fixture->result->winner_team_id === $fixture->team2_id ? 'text-amber-400' : 'text-white' }}">
                        {{ $fixture->result ? $fixture->formatTeamScore(2) : '-' }}
                    </span>
                </div>
                <div id="score-label" class="mt-2 text-xs font-semibold uppercase tracking-wider text-amber-300">
                    {{ $fixture->status === 'live' ? '● Live Running Score' : ($fixture->status === 'completed' ? 'Final Score' : 'Scheduled Score') }}
                </div>
            </div>

            <!-- Team 2 -->
            <div class="lg:col-span-4 text-center lg:text-left">
                <div class="inline-flex flex-col lg:flex-row items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-800 border-2 border-cyan-400/80 text-cyan-400 font-sports font-black flex items-center justify-center text-lg shadow-lg shrink-0">
                        {{ $fixture->team2->department->code ?? substr($fixture->team2->name ?? 'T2', 0, 3) }}
                    </div>
                    <div class="text-center lg:text-left">
                        <h2 class="text-2xl md:text-3xl font-sports font-black text-white tracking-tight uppercase">
                            {{ $fixture->team2->name ?? 'Team 2' }}
                        </h2>
                        <p class="text-xs md:text-sm text-blue-200 font-medium mt-0.5">
                            {{ $fixture->team2->department->name ?? 'Department of Team 2' }}
                        </p>
                        <div id="team2-winner-badge">
                            @if($fixture->result && $fixture->result->winner_team_id === $fixture->team2_id)
                                <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 text-xs font-bold">
                                    🏆 WINNER
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Player of the Match (MVP) Spotlight Card -->
        <div id="potm-card" class="{{ $fixture->result && $fixture->result->playerOfTheMatch ? '' : 'hidden' }} mt-5 bg-gradient-to-r from-amber-500/20 via-amber-400/10 to-transparent border border-amber-400/50 rounded-2xl p-4 sm:p-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-400 to-amber-500 text-slate-950 flex items-center justify-center font-black text-2xl shadow-md shadow-amber-500/20 shrink-0">
                    ⭐
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-300 bg-amber-400/20 px-2 py-0.5 rounded-full border border-amber-400/40">
                        Player of the Match
                    </span>
                    <h3 id="potm-name" class="text-lg sm:text-xl font-black text-white mt-0.5">
                        {{ $fixture->result?->playerOfTheMatch?->name }}
                    </h3>
                    <p id="potm-details" class="text-xs text-blue-200 mt-0.5">
                        {{ $fixture->result?->playerOfTheMatch?->team?->name }} &bull; {{ $fixture->result?->playerOfTheMatch?->team?->department?->name }}
                        @if($fixture->result?->playerOfTheMatch?->reg_no)
                            &bull; Reg #{{ $fixture->result->playerOfTheMatch->reg_no }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="hidden sm:block text-right">
                <span class="text-xs font-bold text-amber-400 bg-slate-900/80 px-3 py-1.5 rounded-xl border border-amber-400/30">
                    Official MVP Award 🏅
                </span>
            </div>
        </div>

        <!-- Match Result Summary if any -->
        <div id="match-summary-box" class="{{ $fixture->result && $fixture->result->summary ? '' : 'hidden' }} mt-4 p-4 rounded-xl bg-slate-900/80 border border-slate-700 text-sm text-slate-200">
            <span class="font-bold text-amber-400">Match Summary:</span> <span id="match-summary-text">{{ $fixture->result?->summary }}</span>
        </div>

        <!-- Venue & Time Footer Details -->
        <div class="mt-6 pt-6 border-t border-slate-800 flex flex-wrap items-center justify-around gap-4 text-xs md:text-sm text-slate-300">
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

    <!-- Live Video Broadcast Player Section (YouTube / Facebook Live Stream) -->
    <div id="live-stream-section" class="{{ $fixture->stream_url ? '' : 'hidden' }}">
        <div class="bg-slate-900 border-2 border-amber-400/80 rounded-2xl overflow-hidden shadow-2xl">
            <!-- Player Header Bar -->
            <div class="px-5 py-3.5 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border-b border-slate-800 flex flex-wrap items-center justify-between gap-3 text-white">
                <div class="flex items-center gap-3">
                    <div id="stream-status-pill">
                        @if($fixture->status === 'live')
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black bg-red-600 text-white animate-pulse tracking-wider uppercase">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                LIVE STREAM
                            </span>
                        @elseif($fixture->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-amber-300 border border-slate-700">
                                🎥 MATCH REPLAY / RECORDING
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-900 text-indigo-200 border border-indigo-700">
                                📺 SCHEDULED BROADCAST
                            </span>
                        @endif
                    </div>

                    <h3 class="text-sm font-bold text-slate-200">
                        {{ $fixture->team1->name ?? 'Team 1' }} vs {{ $fixture->team2->name ?? 'Team 2' }} Official Video Broadcast
                    </h3>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <span id="stream-platform-badge" class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 font-semibold uppercase tracking-wider text-[11px]">
                        {{ ucfirst($fixture->stream_platform ?? 'Stream') }}
                    </span>
                    <a id="stream-external-link" href="{{ $fixture->stream_url ?? '#' }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold transition-all shadow-xs">
                        <span>Open Video</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Responsive 16:9 Video Embed -->
            <div class="relative w-full aspect-video bg-black flex items-center justify-center">
                <iframe
                    id="live-stream-iframe"
                    src="{{ $fixture->embed_stream_url ?? '' }}"
                    title="{{ $fixture->team1->name ?? 'Team 1' }} vs {{ $fixture->team2->name ?? 'Team 2' }} Live Stream"
                    class="w-full h-full border-0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
            </div>

            <div class="px-5 py-2.5 bg-slate-950/90 text-slate-400 text-xs flex flex-wrap items-center justify-between gap-2 border-t border-slate-800">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Directorate of Sports KIU Official Live Stream Channel
                </span>
                <span class="text-[11px]">If playback is restricted by your device or network, tap "Open Video" to view directly.</span>
            </div>
        </div>
    </div>

    <!-- Department Fan Zone: Cheer & Match Winner Poll -->
    <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 border-2 border-amber-400/70 rounded-2xl p-6 md:p-8 shadow-xl text-white relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Section Header -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 border-b border-slate-800">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950">
                        Fan Engagement
                    </span>
                    <span class="text-xs text-amber-300 font-semibold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        Live Department Fan Zone
                    </span>
                </div>
                <h3 class="text-xl md:text-2xl font-black text-white mt-1">
                    Cheer for Your Department &amp; Predict the Winner
                </h3>
                <p class="text-xs md:text-sm text-slate-300 mt-0.5">
                    Tap to predict match outcome and send live emoji cheers to the stadium ground!
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span id="fan-total-interactions-pill" class="text-xs font-bold text-amber-300 bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                    👥 {{ ($fixture->fan_vote_stats['total'] ?? 0) + array_sum($fixture->reactions_stats ?? []) }} Spectators Engaged
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pt-6">
            <!-- Col 1: Winner Prediction Poll (7 cols) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-black uppercase tracking-wider text-amber-400 flex items-center gap-2">
                        <span>🗳️</span>
                        <span>Who will win this match?</span>
                    </h4>
                    <span id="poll-total-votes-label" class="text-xs text-slate-400 font-medium">
                        {{ $fixture->fan_vote_stats['total'] ?? 0 }} {{ Str::plural('vote', $fixture->fan_vote_stats['total'] ?? 0) }}
                    </span>
                </div>

                @php
                    $pollStats = $fixture->fan_vote_stats;
                    $team1 = $fixture->team1;
                    $team2 = $fixture->team2;
                @endphp

                <!-- Interactive Poll Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Vote Team 1 -->
                    <button type="button"
                            onclick="castFanVote({{ $team1?->id }})"
                            id="vote-btn-{{ $team1?->id }}"
                            class="fan-vote-btn group text-left p-4 rounded-xl border transition-all flex flex-col justify-between relative overflow-hidden {{ ($userVotedTeamId ?? null) == $team1?->id ? 'bg-amber-500/20 border-amber-400 text-white shadow-md shadow-amber-500/10' : 'bg-slate-800/80 hover:bg-slate-800 border-slate-700 text-slate-200 hover:border-amber-400/60' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-300">Team 1</span>
                                <h5 class="text-base font-black text-white group-hover:text-amber-300 transition-colors">
                                    {{ $team1?->name ?? 'Team 1' }}
                                </h5>
                                <p class="text-xs text-slate-300 mt-0.5">
                                    {{ $team1?->department?->name ?? 'Dept 1' }}
                                </p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-sm font-bold group-hover:scale-110 transition-transform">
                                👈
                            </span>
                        </div>
                        <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-700/60">
                            <span id="poll-pct-team1" class="text-base font-black text-amber-400 font-mono">
                                {{ $pollStats['team1_percentage'] }}%
                            </span>
                            <span id="poll-count-team1" class="text-xs text-slate-400">
                                {{ $pollStats['team1_votes'] }} votes
                            </span>
                        </div>
                    </button>

                    <!-- Vote Team 2 -->
                    <button type="button"
                            onclick="castFanVote({{ $team2?->id }})"
                            id="vote-btn-{{ $team2?->id }}"
                            class="fan-vote-btn group text-left p-4 rounded-xl border transition-all flex flex-col justify-between relative overflow-hidden {{ ($userVotedTeamId ?? null) == $team2?->id ? 'bg-amber-500/20 border-amber-400 text-white shadow-md shadow-amber-500/10' : 'bg-slate-800/80 hover:bg-slate-800 border-slate-700 text-slate-200 hover:border-amber-400/60' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-300">Team 2</span>
                                <h5 class="text-base font-black text-white group-hover:text-amber-300 transition-colors">
                                    {{ $team2?->name ?? 'Team 2' }}
                                </h5>
                                <p class="text-xs text-slate-300 mt-0.5">
                                    {{ $team2?->department?->name ?? 'Dept 2' }}
                                </p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-sm font-bold group-hover:scale-110 transition-transform">
                                👉
                            </span>
                        </div>
                        <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-700/60">
                            <span id="poll-pct-team2" class="text-base font-black text-amber-400 font-mono">
                                {{ $pollStats['team2_percentage'] }}%
                            </span>
                            <span id="poll-count-team2" class="text-xs text-slate-400">
                                {{ $pollStats['team2_votes'] }} votes
                            </span>
                        </div>
                    </button>
                </div>

                <!-- Dual-colored Progress bar -->
                <div class="space-y-1.5">
                    <div class="w-full h-3 rounded-full bg-slate-800 overflow-hidden flex border border-slate-700/80">
                        <div id="poll-bar-team1"
                             style="width: {{ $pollStats['team1_percentage'] }}%"
                             class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-500"></div>
                        <div id="poll-bar-team2"
                             style="width: {{ $pollStats['team2_percentage'] }}%"
                             class="h-full bg-gradient-to-r from-amber-500 to-orange-600 transition-all duration-500"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                            {{ $team1?->name }}
                        </span>
                        <span id="poll-status-message" class="text-amber-300 font-bold">
                            @if($userVotedTeamId)
                                ✓ You predicted {{ $userVotedTeamId == $team1?->id ? $team1?->name : $team2?->name }}
                            @else
                                Tap your team to vote!
                            @endif
                        </span>
                        <span class="flex items-center gap-1.5">
                            {{ $team2?->name }}
                            <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Col 2: Live Emoji Cheer Reactions (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div>
                    <h4 class="text-sm font-black uppercase tracking-wider text-amber-400 flex items-center gap-2">
                        <span>🎉</span>
                        <span>Send Live Cheers to Ground</span>
                    </h4>
                    <p class="text-xs text-slate-300 mt-0.5">
                        Tap repeatedly to hype up the atmosphere and show department pride!
                    </p>
                </div>

                @php
                    $reactions = $fixture->reactions_stats;
                @endphp

                <div class="grid grid-cols-2 gap-3 relative">
                    <!-- Floating reactions container -->
                    <div id="floating-reactions-container" class="absolute inset-0 pointer-events-none overflow-hidden z-20"></div>

                    <!-- Cheer: Fire -->
                    <button type="button"
                            onclick="sendReaction('fire', this)"
                            class="reaction-btn p-3.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 hover:border-amber-400 rounded-xl flex items-center justify-between text-left transition-all active:scale-95 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl group-hover:scale-125 transition-transform">🔥</span>
                            <div>
                                <span class="text-xs font-bold text-white block">Fire</span>
                                <span class="text-[10px] text-slate-400">Pure Hype</span>
                            </div>
                        </div>
                        <span id="reaction-count-fire" class="text-sm font-black text-amber-400 font-mono">
                            {{ $reactions['fire'] ?? 0 }}
                        </span>
                    </button>

                    <!-- Cheer: Clap -->
                    <button type="button"
                            onclick="sendReaction('clap', this)"
                            class="reaction-btn p-3.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 hover:border-amber-400 rounded-xl flex items-center justify-between text-left transition-all active:scale-95 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl group-hover:scale-125 transition-transform">👏</span>
                            <div>
                                <span class="text-xs font-bold text-white block">Claps</span>
                                <span class="text-[10px] text-slate-400">Respect</span>
                            </div>
                        </div>
                        <span id="reaction-count-clap" class="text-sm font-black text-amber-400 font-mono">
                            {{ $reactions['clap'] ?? 0 }}
                        </span>
                    </button>

                    <!-- Cheer: Cheer / Rocket -->
                    <button type="button"
                            onclick="sendReaction('cheer', this)"
                            class="reaction-btn p-3.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 hover:border-amber-400 rounded-xl flex items-center justify-between text-left transition-all active:scale-95 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl group-hover:scale-125 transition-transform">🚀</span>
                            <div>
                                <span class="text-xs font-bold text-white block">Power</span>
                                <span class="text-[10px] text-slate-400">Let's Go!</span>
                            </div>
                        </div>
                        <span id="reaction-count-cheer" class="text-sm font-black text-amber-400 font-mono">
                            {{ $reactions['cheer'] ?? 0 }}
                        </span>
                    </button>

                    <!-- Cheer: Trophy -->
                    <button type="button"
                            onclick="sendReaction('trophy', this)"
                            class="reaction-btn p-3.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 hover:border-amber-400 rounded-xl flex items-center justify-between text-left transition-all active:scale-95 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl group-hover:scale-125 transition-transform">🏆</span>
                            <div>
                                <span class="text-xs font-bold text-white block">KIU Pride</span>
                                <span class="text-[10px] text-slate-400">Champions</span>
                            </div>
                        </div>
                        <span id="reaction-count-trophy" class="text-sm font-black text-amber-400 font-mono">
                            {{ $reactions['trophy'] ?? 0 }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Match Events & Commentary Timeline -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl md:text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span class="text-2xl">⏱️</span>
                <span>Match Timeline &amp; Key Moments</span>
            </h3>
            <span id="events-count-badge" class="text-xs text-slate-500 font-semibold bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                {{ $fixture->events->count() }} {{ Str::plural('Event', $fixture->events->count()) }}
            </span>
        </div>

        <div id="events-timeline-container" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
            @if($fixture->events->isEmpty())
                <div id="no-events-placeholder" class="text-center py-8 text-slate-400">
                    <span class="text-3xl block mb-2">⏱️</span>
                    <p class="text-sm font-medium text-slate-600">No timeline moments recorded yet.</p>
                    <p class="text-xs text-slate-400 mt-0.5">Key match moments (goals, wickets, cards, commentary) will appear here live during play.</p>
                </div>
                <div id="events-list" class="relative pl-6 space-y-4 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 hidden"></div>
            @else
                <div id="no-events-placeholder" class="hidden"></div>
                <div id="events-list" class="relative pl-6 space-y-4 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @foreach($fixture->events as $event)
                        <div class="relative flex items-start space-x-3 group">
                            <!-- Bullet / Icon -->
                            <div class="absolute -left-6 mt-0.5 w-6 h-6 rounded-full bg-white border-2 border-amber-400 flex items-center justify-center text-xs shadow-xs">
                                {{ $event->icon }}
                            </div>
                            <div class="flex-1 bg-slate-50 p-3.5 rounded-xl border border-slate-100 hover:border-amber-300 transition-colors">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center space-x-2">
                                        @if($event->minute)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black font-mono bg-slate-900 text-amber-300">
                                                {{ $event->minute }}
                                            </span>
                                        @endif
                                        <span class="font-bold text-slate-900 text-sm">
                                            {{ $event->title }}
                                        </span>
                                    </div>
                                    @if($event->team)
                                        <span class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                            {{ $event->team->name }}
                                        </span>
                                    @endif
                                </div>
                                @if($event->description || $event->player)
                                    <div class="mt-1 text-xs text-slate-600">
                                        @if($event->player)
                                            <strong class="text-slate-900">{{ $event->player->name }}</strong>
                                            @if($event->description) &bull; @endif
                                        @endif
                                        {{ $event->description }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Team Rosters / Squads Section -->
    <div class="space-y-4">
        <h3 class="text-xl md:text-2xl font-bold text-slate-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Team Squads & Rosters</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Team 1 Squad -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h4 class="font-bold text-lg text-slate-900">{{ $fixture->team1->name ?? 'Team 1' }}</h4>
                    <span class="text-xs text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded">{{ $fixture->team1->players->count() }} Players</span>
                </div>

                @if($fixture->team1 && $fixture->team1->players->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($fixture->team1->players as $index => $player)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-700">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $player->name }}</div>
                                        @if($player->reg_no)
                                            <div class="text-xs text-slate-500">Reg: {{ $player->reg_no }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic py-4 text-center">No players registered for this team yet.</p>
                @endif
            </div>

            <!-- Team 2 Squad -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h4 class="font-bold text-lg text-slate-900">{{ $fixture->team2->name ?? 'Team 2' }}</h4>
                    <span class="text-xs text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded">{{ $fixture->team2->players->count() }} Players</span>
                </div>

                @if($fixture->team2 && $fixture->team2->players->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($fixture->team2->players as $index => $player)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-700">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $player->name }}</div>
                                        @if($player->reg_no)
                                            <div class="text-xs text-slate-500">Reg: {{ $player->reg_no }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic py-4 text-center">No players registered for this team yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Match Photos Grid -->
    <div class="space-y-4 pt-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h3 class="text-xl md:text-2xl font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Match Photos & Action Gallery</span>
                <span class="text-sm font-bold text-blue-600">({{ $fixture->photos->count() }})</span>
            </h3>

            @auth
                @if(in_array(auth()->user()->role, ['admin', 'scorer']))
                    <a href="{{ route('admin.fixtures.edit', $fixture) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-amber-300 transition-colors">
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
                    <div class="group bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs hover:shadow-md hover:border-blue-400 transition-all duration-300 flex flex-col">
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
                                <p class="text-sm text-slate-800 font-medium line-clamp-2 mb-2">
                                    {{ $photo->caption }}
                                </p>
                            @else
                                <p class="text-xs text-slate-400 italic mb-2">
                                    Match action moment
                                </p>
                            @endif

                            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-2 border-t border-slate-100">
                                <span>Uploaded {{ $photo->created_at->diffForHumans() }}</span>
                                @if($photo->uploader)
                                    <span>by {{ $photo->uploader->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-amber-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h4 class="text-base font-bold text-slate-900">No Match Photos Yet</h4>
                <p class="text-xs md:text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    Photos for this match will appear here once uploaded by tournament officials and photographers.
                </p>
            </div>
        @endif
    </div>

    <!-- Back to Schedule Link -->
    <div class="text-center pt-4">
        <a href="{{ route('schedule', ['edition_id' => $fixture->edition_id, 'sport_id' => $fixture->sport_id]) }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-bold transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Gala Schedule
        </a>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const fixtureId = {{ $fixture->id }};
        const endpoint = "{{ route('api.live-scores.show', $fixture) }}";
        let lastTeam1Score = "{{ $fixture->result?->team1_score ?? '-' }}";
        let lastTeam2Score = "{{ $fixture->result?->team2_score ?? '-' }}";
        let currentStatus = "{{ $fixture->status }}";

        const team1ScoreEl = document.getElementById('team1-score-display');
        const team2ScoreEl = document.getElementById('team2-score-display');
        const statusBadgeContainer = document.getElementById('status-badge-container');
        const lastSyncTimeEl = document.getElementById('last-sync-time');
        const scoreLabelEl = document.getElementById('score-label');
        const summaryBox = document.getElementById('match-summary-box');
        const summaryText = document.getElementById('match-summary-text');
        const team1WinnerBadge = document.getElementById('team1-winner-badge');
        const team2WinnerBadge = document.getElementById('team2-winner-badge');

        function flashUpdate(element) {
            if (!element) return;
            element.classList.add('scale-125', 'text-amber-300', 'transition-transform', 'duration-300');
            setTimeout(() => {
                element.classList.remove('scale-125', 'text-amber-300');
            }, 600);
        }

        async function fetchLiveMatch() {
            try {
                const response = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) return;

                const data = await response.json();

                // Update sync time
                const now = new Date();
                if (lastSyncTimeEl) {
                    lastSyncTimeEl.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }

                // Update scores
                const newT1 = data.result ? String(data.result.team1_score) : '-';
                const newT2 = data.result ? String(data.result.team2_score) : '-';

                if (newT1 !== lastTeam1Score) {
                    lastTeam1Score = newT1;
                    if (team1ScoreEl) {
                        team1ScoreEl.textContent = newT1;
                        flashUpdate(team1ScoreEl);
                    }
                }

                if (newT2 !== lastTeam2Score) {
                    lastTeam2Score = newT2;
                    if (team2ScoreEl) {
                        team2ScoreEl.textContent = newT2;
                        flashUpdate(team2ScoreEl);
                    }
                }

                // Update status if changed
                if (data.status !== currentStatus) {
                    currentStatus = data.status;

                    if (statusBadgeContainer) {
                        if (currentStatus === 'live') {
                            statusBadgeContainer.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/40 animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-red-400"></span> LIVE NOW
                                </span>
                            `;
                            if (scoreLabelEl) scoreLabelEl.textContent = '● Live Running Score';
                        } else if (currentStatus === 'completed') {
                            statusBadgeContainer.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                    ✓ COMPLETED
                                </span>
                            `;
                            if (scoreLabelEl) scoreLabelEl.textContent = 'Final Score';
                        }
                    }
                }

                // Update winner badges
                if (data.result && data.result.winner_team_id) {
                    const winnerHtml = `
                        <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 text-xs font-bold">
                            🏆 WINNER
                        </span>
                    `;
                    if (data.team1 && data.team1.is_winner && team1WinnerBadge) {
                        team1WinnerBadge.innerHTML = winnerHtml;
                    }
                    if (data.team2 && data.team2.is_winner && team2WinnerBadge) {
                        team2WinnerBadge.innerHTML = winnerHtml;
                    }
                }

                // Update Player of the Match
                const potmCard = document.getElementById('potm-card');
                const potmName = document.getElementById('potm-name');
                const potmDetails = document.getElementById('potm-details');
                if (data.result && data.result.player_of_the_match && potmCard && potmName && potmDetails) {
                    potmName.textContent = data.result.player_of_the_match.name;
                    let desc = data.result.player_of_the_match.team || '';
                    if (data.result.player_of_the_match.department) {
                        desc += ' • ' + data.result.player_of_the_match.department;
                    }
                    if (data.result.player_of_the_match.reg_no) {
                        desc += ' • Reg #' + data.result.player_of_the_match.reg_no;
                    }
                    potmDetails.textContent = desc;
                    potmCard.classList.remove('hidden');
                }

                // Update summary
                if (data.result && data.result.summary && summaryBox && summaryText) {
                    summaryText.textContent = data.result.summary;
                    summaryBox.classList.remove('hidden');
                }

                // Update Live Stream if broadcast is attached
                const streamSection = document.getElementById('live-stream-section');
                const streamIframe = document.getElementById('live-stream-iframe');
                const streamExternalLink = document.getElementById('stream-external-link');
                const streamPlatformBadge = document.getElementById('stream-platform-badge');
                const streamStatusPill = document.getElementById('stream-status-pill');

                if (data.stream_url && streamSection) {
                    streamSection.classList.remove('hidden');
                    if (streamIframe && data.embed_stream_url && streamIframe.src !== data.embed_stream_url) {
                        streamIframe.src = data.embed_stream_url;
                    }
                    if (streamExternalLink) {
                        streamExternalLink.href = data.stream_url;
                    }
                    if (streamPlatformBadge && data.stream_platform) {
                        streamPlatformBadge.textContent = data.stream_platform.toUpperCase();
                    }
                    if (streamStatusPill) {
                        if (currentStatus === 'live') {
                            streamStatusPill.innerHTML = `
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black bg-red-600 text-white animate-pulse tracking-wider uppercase">
                                    <span class="w-2 h-2 rounded-full bg-white"></span> LIVE STREAM
                                </span>
                            `;
                        } else if (currentStatus === 'completed') {
                            streamStatusPill.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-amber-300 border border-slate-700">
                                    🎥 MATCH REPLAY / RECORDING
                                </span>
                            `;
                        }
                    }
                }

                // Update Timeline Events dynamically
                const eventsList = document.getElementById('events-list');
                const noEventsPlaceholder = document.getElementById('no-events-placeholder');
                const eventsCountBadge = document.getElementById('events-count-badge');

                if (data.events && eventsList) {
                    if (eventsCountBadge) {
                        eventsCountBadge.textContent = `${data.events.length} ${data.events.length === 1 ? 'Event' : 'Events'}`;
                    }

                    if (data.events.length > 0) {
                        if (noEventsPlaceholder) noEventsPlaceholder.classList.add('hidden');
                        eventsList.classList.remove('hidden');

                        let html = '';
                        data.events.forEach(ev => {
                            html += `
                                <div class="relative flex items-start space-x-3 group">
                                    <div class="absolute -left-6 mt-0.5 w-6 h-6 rounded-full bg-white border-2 border-amber-400 flex items-center justify-center text-xs shadow-xs">
                                        ${ev.icon || '📢'}
                                    </div>
                                    <div class="flex-1 bg-slate-50 p-3.5 rounded-xl border border-slate-100 hover:border-amber-300 transition-colors">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center space-x-2">
                                                ${ev.minute ? `<span class="px-2 py-0.5 rounded text-[10px] font-black font-mono bg-slate-900 text-amber-300">${ev.minute}</span>` : ''}
                                                <span class="font-bold text-slate-900 text-sm">${ev.title}</span>
                                            </div>
                                            ${ev.team_name ? `<span class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">${ev.team_name}</span>` : ''}
                                        </div>
                                        ${(ev.description || ev.player_name) ? `
                                            <div class="mt-1 text-xs text-slate-600">
                                                ${ev.player_name ? `<strong class="text-slate-900">${ev.player_name}</strong> ${ev.description ? '&bull;' : ''}` : ''}
                                                ${ev.description || ''}
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        });
                        eventsList.innerHTML = html;
                    } else {
                        if (noEventsPlaceholder) noEventsPlaceholder.classList.remove('hidden');
                        eventsList.classList.add('hidden');
                    }
                }

                // Update WhatsApp Share Link
                const waBtn = document.getElementById('whatsapp-share-btn');
                if (waBtn && data.whatsapp_share_url) {
                    waBtn.href = data.whatsapp_share_url;
                }

            } catch (err) {
                console.error("Live score sync failed:", err);
            }
        }

        // Fan Engagement: Poll & Reactions Sync
        const fanStatsEndpoint = "{{ route('matches.fan-stats', $fixture) }}";

        async function fetchFanStats() {
            try {
                const res = await fetch(fanStatsEndpoint, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();
                if (data.poll) updateFanPollUI(data.poll, data.user_voted_team_id);
                if (data.reactions) updateReactionCounts(data.reactions);
            } catch (e) {
                // Ignore transient fan poll sync issues
            }
        }

        function updateFanPollUI(stats, userVotedTeamId, message) {
            if (!stats) return;

            const bar1 = document.getElementById('poll-bar-team1');
            const bar2 = document.getElementById('poll-bar-team2');
            const pct1 = document.getElementById('poll-pct-team1');
            const pct2 = document.getElementById('poll-pct-team2');
            const count1 = document.getElementById('poll-count-team1');
            const count2 = document.getElementById('poll-count-team2');
            const totalLabel = document.getElementById('poll-total-votes-label');
            const statusMsg = document.getElementById('poll-status-message');

            if (bar1) bar1.style.width = `${stats.team1_percentage}%`;
            if (bar2) bar2.style.width = `${stats.team2_percentage}%`;
            if (pct1) pct1.textContent = `${stats.team1_percentage}%`;
            if (pct2) pct2.textContent = `${stats.team2_percentage}%`;
            if (count1) count1.textContent = `${stats.team1_votes} votes`;
            if (count2) count2.textContent = `${stats.team2_votes} votes`;
            if (totalLabel) totalLabel.textContent = `${stats.total} ${stats.total === 1 ? 'vote' : 'votes'}`;

            if (userVotedTeamId) {
                document.querySelectorAll('.fan-vote-btn').forEach(btn => {
                    btn.classList.remove('bg-amber-500/20', 'border-amber-400', 'shadow-md', 'shadow-amber-500/10');
                    btn.classList.add('bg-slate-800/80', 'border-slate-700');
                });
                const votedBtn = document.getElementById(`vote-btn-${userVotedTeamId}`);
                if (votedBtn) {
                    votedBtn.classList.remove('bg-slate-800/80', 'border-slate-700');
                    votedBtn.classList.add('bg-amber-500/20', 'border-amber-400', 'shadow-md', 'shadow-amber-500/10');
                }
            }

            if (message && statusMsg) {
                statusMsg.textContent = message;
            }
        }

        function updateReactionCounts(reactions) {
            if (!reactions) return;
            Object.keys(reactions).forEach(key => {
                const el = document.getElementById(`reaction-count-${key}`);
                if (el) el.textContent = reactions[key];
            });
        }

        function spawnFloatingEmoji(type, btnEl) {
            const emojis = { fire: '🔥', clap: '👏', cheer: '🚀', trophy: '🏆' };
            const emojiChar = emojis[type] || '🔥';

            const container = document.getElementById('floating-reactions-container');
            if (!container) return;

            const span = document.createElement('span');
            span.textContent = emojiChar;
            span.className = 'absolute pointer-events-none text-2xl transition-all duration-1000 ease-out select-none';

            const rect = btnEl ? btnEl.getBoundingClientRect() : null;
            const contRect = container.getBoundingClientRect();

            let left = contRect.width / 2;
            let top = contRect.height - 20;

            if (rect && contRect) {
                left = (rect.left - contRect.left) + rect.width / 2;
                top = (rect.top - contRect.top) + rect.height / 2;
            }

            span.style.left = `${left + (Math.random() * 40 - 20)}px`;
            span.style.top = `${top}px`;
            span.style.opacity = '1';
            span.style.transform = 'translateY(0) scale(1)';

            container.appendChild(span);

            requestAnimationFrame(() => {
                span.style.opacity = '0';
                span.style.transform = `translateY(-90px) scale(${1.3 + Math.random() * 0.4}) rotate(${Math.random() * 40 - 20}deg)`;
            });

            setTimeout(() => {
                span.remove();
            }, 1100);
        }

        // Global functions for inline onclick handlers
        window.castFanVote = async function(teamId) {
            if (!teamId) return;
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch("{{ route('matches.vote', $fixture) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ team_id: teamId })
                });
                const data = await res.json();
                if (data.success && data.stats) {
                    updateFanPollUI(data.stats, data.user_voted_team_id, data.message);
                }
            } catch (err) {
                console.error("Fan vote error:", err);
            }
        };

        window.sendReaction = async function(type, btnEl) {
            spawnFloatingEmoji(type, btnEl);

            const countEl = document.getElementById(`reaction-count-${type}`);
            if (countEl) {
                const cur = parseInt(countEl.textContent.trim()) || 0;
                countEl.textContent = cur + 1;
                countEl.classList.add('scale-125', 'text-amber-300');
                setTimeout(() => countEl.classList.remove('scale-125', 'text-amber-300'), 300);
            }

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch("{{ route('matches.react', $fixture) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ reaction: type })
                });
                const data = await res.json();
                if (data.success && data.reactions) {
                    updateReactionCounts(data.reactions);
                }
            } catch (err) {
                console.error("Reaction send error:", err);
            }
        };

        // Initial sync and set polling interval
        fetchLiveMatch();

        // Poll every 5s if live, 8s if scheduled, 30s if completed
        const intervalMs = (currentStatus === 'live') ? 5000 : (currentStatus === 'scheduled' ? 8000 : 30000);
        setInterval(() => {
            fetchLiveMatch();
            fetchFanStats();
        }, intervalMs);
    })();
</script>
@endpush
@endsection
