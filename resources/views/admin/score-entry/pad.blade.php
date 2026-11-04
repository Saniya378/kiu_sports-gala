@extends('layouts.admin')

@section('header_title', 'Quick Scorer Pad')
@section('header_subtitle', 'Fast-tap mobile interface for ground scorers and referees.')

@section('header_actions')
    <div class="flex items-center space-x-2">
        <a href="{{ route('matches.show', $fixture) }}" target="_blank"
           class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-200 hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4 mr-1.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Public View
        </a>
        <a href="{{ route('admin.score-entry.index', ['fixture_id' => $fixture->id]) }}"
           class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
            &larr; Standard Form
        </a>
    </div>
@endsection

@section('content')
@php
    $sportName = strtolower($fixture->sport->name ?? '');
    $isCricket = str_contains($sportName, 'cricket');
    $isFootball = str_contains($sportName, 'football') || str_contains($sportName, 'futsal');
    $isBasketball = str_contains($sportName, 'basketball');
    $isVolleyball = str_contains($sportName, 'volleyball') || str_contains($sportName, 'badminton') || str_contains($sportName, 'tennis');

    $t1Score = old('team1_score', $fixture->result->team1_score ?? 0);
    $t2Score = old('team2_score', $fixture->result->team2_score ?? 0);
    $currentStatus = old('status', $fixture->status);
    $potmId = old('player_of_the_match_id', $fixture->result->player_of_the_match_id ?? '');
    $summary = old('summary', $fixture->result->summary ?? '');
@endphp

<div class="max-w-4xl mx-auto space-y-5 pb-20">

    <!-- Top Match Header Card -->
    <div class="bg-gradient-to-r from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400 rounded-2xl p-4 sm:p-5 text-white shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-3 text-xs mb-2">
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full bg-amber-400/20 text-amber-300 font-bold border border-amber-400/30">
                    {{ $fixture->sport->name ?? 'Sport' }} ({{ ucfirst($fixture->sport->category ?? '') }})
                </span>
                @if($fixture->round)
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 font-semibold">
                        {{ $fixture->round }}
                    </span>
                @endif
            </div>

            <!-- Dynamic Live Pill -->
            <div id="live-indicator-badge">
                @if ($fixture->status === 'live')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-rose-500 text-white animate-pulse">
                        <span class="w-2 h-2 mr-1.5 rounded-full bg-white"></span>
                        LIVE MATCH
                    </span>
                @elseif ($fixture->status === 'completed')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-200">
                        COMPLETED
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/40">
                        SCHEDULED
                    </span>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-800 text-xs text-slate-300">
            <span><strong>Venue:</strong> {{ $fixture->venue->name ?? 'Main Ground' }}</span>
            <span><strong>Date:</strong> {{ $fixture->match_date ? \Carbon\Carbon::parse($fixture->match_date)->format('M d, Y') : 'TBD' }}</span>
        </div>
    </div>

    <!-- Scorer Pad Form -->
    <form id="quick-scorer-form" method="POST" action="{{ route('admin.score-entry.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="fixture_id" value="{{ $fixture->id }}">

        <!-- Match Status Selector Buttons -->
        <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-xs">
            <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 block mb-2 px-1">
                Match State
            </span>
            <div class="grid grid-cols-3 gap-2">
                <label class="cursor-pointer">
                    <input type="radio" name="status" value="scheduled" class="sr-only peer" {{ $currentStatus === 'scheduled' ? 'checked' : '' }} onchange="updateStateTheme()">
                    <div class="py-2.5 px-3 text-center rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-xs peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all flex items-center justify-center gap-1.5">
                        <span>⏳</span> Scheduled
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="status" value="live" class="sr-only peer" {{ $currentStatus === 'live' ? 'checked' : '' }} onchange="updateStateTheme()">
                    <div class="py-2.5 px-3 text-center rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-xs peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 transition-all flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span> Live Now
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="status" value="completed" class="sr-only peer" {{ $currentStatus === 'completed' ? 'checked' : '' }} onchange="updateStateTheme()">
                    <div class="py-2.5 px-3 text-center rounded-xl border-2 border-slate-200 text-slate-600 font-bold text-xs peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all flex items-center justify-center gap-1.5">
                        <span>🏁</span> Completed
                    </div>
                </label>
            </div>
        </div>

        <!-- Scores & Rapid Keypads -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Team 1 Keypad Card -->
            <div class="bg-white border-2 border-slate-200 rounded-3xl p-5 shadow-sm space-y-4 relative overflow-hidden">
                <div class="border-b border-slate-100 pb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">
                        Team 1 (Home)
                    </span>
                    <h3 class="text-lg font-black text-slate-900 mt-1 truncate">
                        {{ $fixture->team1->name }}
                    </h3>
                    <p class="text-xs text-slate-500 truncate">
                        {{ $fixture->team1->department->name ?? 'Dept' }}
                    </p>
                </div>

                <!-- Big Display -->
                <div class="flex items-center justify-between bg-slate-900 text-white rounded-2xl p-4 shadow-inner">
                    <button type="button" onclick="adjustScore('team1_score', -1)"
                            class="w-12 h-12 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-white font-black text-2xl flex items-center justify-center transition-all">
                        &minus;
                    </button>

                    <div class="text-center px-4">
                        <input type="number" id="team1_score" name="team1_score" value="{{ $t1Score }}" min="0"
                               class="bg-transparent border-0 text-center font-black text-5xl text-amber-400 w-32 focus:ring-0 p-0 font-mono tracking-tight"
                               oninput="syncDisplay('team1')">
                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-widest block">
                            {{ $isCricket ? 'Runs' : ($isFootball ? 'Goals' : 'Points') }}
                        </span>
                    </div>

                    <button type="button" onclick="adjustScore('team1_score', 1)"
                            class="w-12 h-12 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white font-black text-2xl flex items-center justify-center transition-all shadow-md shadow-indigo-600/30">
                        +
                    </button>
                </div>

                <!-- Quick Increment Buttons for Team 1 -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">
                        Rapid Increments
                    </span>
                    <div class="grid grid-cols-4 gap-2">
                        @if ($isCricket)
                            <button type="button" onclick="adjustScore('team1_score', 1)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+1</button>
                            <button type="button" onclick="adjustScore('team1_score', 2)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+2</button>
                            <button type="button" onclick="adjustScore('team1_score', 4)" class="py-2.5 rounded-xl bg-amber-100 hover:bg-amber-200 active:scale-95 font-black text-sm text-amber-900 border border-amber-300 transition-all">+4 🏏</button>
                            <button type="button" onclick="adjustScore('team1_score', 6)" class="py-2.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 active:scale-95 font-black text-sm text-emerald-900 border border-emerald-300 transition-all">+6 🚀</button>
                        @elseif ($isBasketball)
                            <button type="button" onclick="adjustScore('team1_score', 1)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+1 FT</button>
                            <button type="button" onclick="adjustScore('team1_score', 2)" class="py-2.5 rounded-xl bg-indigo-100 hover:bg-indigo-200 active:scale-95 font-black text-sm text-indigo-900 border border-indigo-200 transition-all">+2 FG</button>
                            <button type="button" onclick="adjustScore('team1_score', 3)" class="py-2.5 rounded-xl bg-amber-100 hover:bg-amber-200 active:scale-95 font-black text-sm text-amber-900 border border-amber-300 transition-all">+3 🏀</button>
                            <button type="button" onclick="setScore('team1_score', 0)" class="py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 font-bold text-xs text-rose-700 transition-all">Clear</button>
                        @elseif ($isFootball)
                            <button type="button" onclick="adjustScore('team1_score', 1)" class="col-span-2 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 font-black text-base text-white shadow-md transition-all">+1 GOAL ⚽</button>
                            <button type="button" onclick="adjustScore('team1_score', -1)" class="py-3 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-bold text-xs text-slate-700 transition-all">Undo</button>
                            <button type="button" onclick="setScore('team1_score', 0)" class="py-3 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 font-bold text-xs text-rose-700 transition-all">0</button>
                        @else
                            <button type="button" onclick="adjustScore('team1_score', 1)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+1</button>
                            <button type="button" onclick="adjustScore('team1_score', 2)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+2</button>
                            <button type="button" onclick="adjustScore('team1_score', 3)" class="py-2.5 rounded-xl bg-indigo-100 hover:bg-indigo-200 active:scale-95 font-black text-sm text-indigo-900 transition-all">+3</button>
                            <button type="button" onclick="setScore('team1_score', 0)" class="py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 font-bold text-xs text-rose-700 transition-all">0</button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Team 2 Keypad Card -->
            <div class="bg-white border-2 border-slate-200 rounded-3xl p-5 shadow-sm space-y-4 relative overflow-hidden">
                <div class="border-b border-slate-100 pb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                        Team 2 (Away)
                    </span>
                    <h3 class="text-lg font-black text-slate-900 mt-1 truncate">
                        {{ $fixture->team2->name }}
                    </h3>
                    <p class="text-xs text-slate-500 truncate">
                        {{ $fixture->team2->department->name ?? 'Dept' }}
                    </p>
                </div>

                <!-- Big Display -->
                <div class="flex items-center justify-between bg-slate-900 text-white rounded-2xl p-4 shadow-inner">
                    <button type="button" onclick="adjustScore('team2_score', -1)"
                            class="w-12 h-12 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-white font-black text-2xl flex items-center justify-center transition-all">
                        &minus;
                    </button>

                    <div class="text-center px-4">
                        <input type="number" id="team2_score" name="team2_score" value="{{ $t2Score }}" min="0"
                               class="bg-transparent border-0 text-center font-black text-5xl text-amber-400 w-32 focus:ring-0 p-0 font-mono tracking-tight"
                               oninput="syncDisplay('team2')">
                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-widest block">
                            {{ $isCricket ? 'Runs' : ($isFootball ? 'Goals' : 'Points') }}
                        </span>
                    </div>

                    <button type="button" onclick="adjustScore('team2_score', 1)"
                            class="w-12 h-12 rounded-xl bg-amber-500 hover:bg-amber-400 active:scale-95 text-slate-950 font-black text-2xl flex items-center justify-center transition-all shadow-md shadow-amber-500/30">
                        +
                    </button>
                </div>

                <!-- Quick Increment Buttons for Team 2 -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">
                        Rapid Increments
                    </span>
                    <div class="grid grid-cols-4 gap-2">
                        @if ($isCricket)
                            <button type="button" onclick="adjustScore('team2_score', 1)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+1</button>
                            <button type="button" onclick="adjustScore('team2_score', 2)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+2</button>
                            <button type="button" onclick="adjustScore('team2_score', 4)" class="py-2.5 rounded-xl bg-amber-100 hover:bg-amber-200 active:scale-95 font-black text-sm text-amber-900 border border-amber-300 transition-all">+4 🏏</button>
                            <button type="button" onclick="adjustScore('team2_score', 6)" class="py-2.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 active:scale-95 font-black text-sm text-emerald-900 border border-emerald-300 transition-all">+6 🚀</button>
                        @elseif ($isBasketball)
                            <button type="button" onclick="adjustScore('team2_score', 1)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+1 FT</button>
                            <button type="button" onclick="adjustScore('team2_score', 2)" class="py-2.5 rounded-xl bg-indigo-100 hover:bg-indigo-200 active:scale-95 font-black text-sm text-indigo-900 border border-indigo-200 transition-all">+2 FG</button>
                            <button type="button" onclick="adjustScore('team2_score', 3)" class="py-2.5 rounded-xl bg-amber-100 hover:bg-amber-200 active:scale-95 font-black text-sm text-amber-900 border border-amber-300 transition-all">+3 🏀</button>
                            <button type="button" onclick="setScore('team2_score', 0)" class="py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 font-bold text-xs text-rose-700 transition-all">Clear</button>
                        @elseif ($isFootball)
                            <button type="button" onclick="adjustScore('team2_score', 1)" class="col-span-2 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 font-black text-base text-white shadow-md transition-all">+1 GOAL ⚽</button>
                            <button type="button" onclick="adjustScore('team2_score', -1)" class="py-3 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-bold text-xs text-slate-700 transition-all">Undo</button>
                            <button type="button" onclick="setScore('team2_score', 0)" class="py-3 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 font-bold text-xs text-rose-700 transition-all">0</button>
                        @else
                            <button type="button" onclick="adjustScore('team2_score', 1)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+1</button>
                            <button type="button" onclick="adjustScore('team2_score', 2)" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-sm text-slate-800 transition-all">+2</button>
                            <button type="button" onclick="adjustScore('team2_score', 3)" class="py-2.5 rounded-xl bg-indigo-100 hover:bg-indigo-200 active:scale-95 font-black text-sm text-indigo-900 transition-all">+3</button>
                            <button type="button" onclick="setScore('team2_score', 0)" class="py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 font-bold text-xs text-rose-700 transition-all">0</button>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Match Notes & Player of the Match -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
            <!-- Player of the Match Dropdown -->
            <div>
                <label for="player_of_the_match_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    🏅 Player of the Match (Optional)
                </label>
                <select name="player_of_the_match_id" id="player_of_the_match_id"
                        class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-amber-400 focus:ring-amber-400">
                    <option value="">-- None / Select Outstanding Performer --</option>
                    @if ($fixture->team1 && $fixture->team1->players->isNotEmpty())
                        <optgroup label="{{ $fixture->team1->name }}">
                            @foreach ($fixture->team1->players as $p)
                                <option value="{{ $p->id }}" {{ (string)$potmId === (string)$p->id ? 'selected' : '' }}>
                                    {{ $p->name }} {{ $p->reg_no ? "({$p->reg_no})" : '' }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                    @if ($fixture->team2 && $fixture->team2->players->isNotEmpty())
                        <optgroup label="{{ $fixture->team2->name }}">
                            @foreach ($fixture->team2->players as $p)
                                <option value="{{ $p->id }}" {{ (string)$potmId === (string)$p->id ? 'selected' : '' }}>
                                    {{ $p->name }} {{ $p->reg_no ? "({$p->reg_no})" : '' }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            <!-- Match Live Summary / Note -->
            <div>
                <label for="summary" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Live Score Ticker Note / Summary
                </label>
                <input type="text" name="summary" id="summary" value="{{ $summary }}"
                       placeholder="e.g. 1st Inning Complete, 12 overs remaining, or Final Set underway..."
                       class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400">
            </div>
        </div>

        <!-- Live Match Events & Key Moments Recorder -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">⏱️</span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Key Moments &amp; Match Events</h3>
                        <p class="text-[11px] text-slate-500">Record goals, wickets, cards, and live commentary for the match timeline</p>
                    </div>
                </div>
                <button type="button" onclick="openEventModal()"
                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-400 text-slate-950 hover:bg-amber-300 shadow-sm transition-all">
                    + Log Event
                </button>
            </div>

            <!-- List of existing events -->
            <div id="pad-events-container">
                @if($fixture->events->isEmpty())
                    <p id="pad-no-events-text" class="text-xs text-slate-400 italic text-center py-4">No key events logged for this match yet. Tap "+ Log Event" above.</p>
                    <div id="pad-events-list" class="space-y-2 max-h-60 overflow-y-auto pr-1 hidden"></div>
                @else
                    <div id="pad-events-list" class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($fixture->events as $ev)
                            <div id="event-row-{{ $ev->id }}" class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                                    <span class="text-base shrink-0">{{ $ev->icon }}</span>
                                    <div class="min-w-0">
                                        <div class="flex items-center space-x-1.5">
                                            @if($ev->minute)
                                                <span class="font-mono font-bold text-[10px] bg-slate-900 text-amber-300 px-1.5 py-0.5 rounded">{{ $ev->minute }}</span>
                                            @endif
                                            <span class="font-bold text-slate-800 truncate">{{ $ev->title }}</span>
                                        </div>
                                        @if($ev->player || $ev->team)
                                            <span class="text-[10px] text-slate-500 truncate block">
                                                {{ $ev->player?->name }} @if($ev->team) ({{ $ev->team->name }}) @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <button type="button" onclick="deleteEvent({{ $ev->id }})" class="text-rose-500 hover:text-rose-700 text-xs px-2 py-1 rounded hover:bg-rose-50 transition-colors shrink-0">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sticky Bottom Save Bar (Mobile Thumb-Friendly) -->
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-[#0a1226]/95 backdrop-blur-md border-t-2 border-amber-400 p-3 sm:p-4 shadow-2xl">
            <div class="max-w-4xl mx-auto flex items-center justify-between gap-3">
                <div class="text-xs text-slate-300 hidden sm:block">
                    <span class="text-amber-400 font-bold">Scorer Ground Mode:</span> Tap rapid buttons above and broadcast.
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="submitAjaxScore()" id="btn-save-ajax"
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-black text-sm bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white shadow-lg shadow-emerald-900/30 active:scale-98 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Broadcast Live Score
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<!-- Floating Toast Notification -->
<div id="scorer-toast" class="fixed top-24 right-5 z-50 transform translate-y-[-100px] opacity-0 transition-all duration-300 pointer-events-none">
    <div class="bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center space-x-2 text-xs font-bold border border-emerald-400">
        <span class="text-lg">✓</span>
        <span id="toast-message">Score updated and broadcasted live!</span>
    </div>
</div>

<!-- Add Event Modal -->
<div id="event-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Record Match Moment / Event</h3>
            <button type="button" onclick="closeEventModal()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form id="add-event-form" onsubmit="submitEventForm(event)" class="space-y-4">
            @csrf

            <!-- Event Type Presets -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Event Type</label>
                <div class="grid grid-cols-4 gap-1.5">
                    @php
                        $types = [
                            'goal' => ['label' => 'Goal', 'icon' => '⚽'],
                            'wicket' => ['label' => 'Wicket', 'icon' => '🏏'],
                            'six' => ['label' => 'Six', 'icon' => '🚀'],
                            'four' => ['label' => 'Four', 'icon' => '⚡'],
                            'yellow_card' => ['label' => 'Yellow', 'icon' => '🟨'],
                            'red_card' => ['label' => 'Red', 'icon' => '🟥'],
                            'point' => ['label' => 'Point', 'icon' => '🎯'],
                            'note' => ['label' => 'Comment', 'icon' => '📢'],
                        ];
                    @endphp
                    @foreach($types as $key => $meta)
                        <label class="cursor-pointer">
                            <input type="radio" name="event_type" value="{{ $key }}" class="sr-only peer" {{ $loop->first ? 'checked' : '' }} onchange="autoFillTitle('{{ $meta['label'] }}')">
                            <div class="py-2 px-1 text-center rounded-xl border border-slate-200 text-xs font-bold text-slate-700 peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-900 transition-all flex flex-col items-center">
                                <span class="text-base">{{ $meta['icon'] }}</span>
                                <span class="text-[10px] mt-0.5">{{ $meta['label'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Minute / Over -->
                <div>
                    <label for="event_minute" class="block text-xs font-semibold text-slate-600 mb-1">Time / Minute</label>
                    <input type="text" id="event_minute" name="minute" placeholder="e.g. 14' or Over 3.4"
                           class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400">
                </div>

                <!-- Team -->
                <div>
                    <label for="event_team_id" class="block text-xs font-semibold text-slate-600 mb-1">Team</label>
                    <select id="event_team_id" name="team_id"
                            class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400">
                        <option value="">-- Neither / Neutral --</option>
                        <option value="{{ $fixture->team1_id }}">{{ $fixture->team1->name }}</option>
                        <option value="{{ $fixture->team2_id }}">{{ $fixture->team2->name }}</option>
                    </select>
                </div>
            </div>

            <!-- Player -->
            <div>
                <label for="event_player_id" class="block text-xs font-semibold text-slate-600 mb-1">Player Involved (Optional)</label>
                <select id="event_player_id" name="player_id"
                        class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400">
                    <option value="">-- None / General Event --</option>
                    @if ($fixture->team1 && $fixture->team1->players->isNotEmpty())
                        <optgroup label="{{ $fixture->team1->name }}">
                            @foreach ($fixture->team1->players as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} {{ $p->reg_no ? "({$p->reg_no})" : '' }}</option>
                            @endforeach
                        </optgroup>
                    @endif
                    @if ($fixture->team2 && $fixture->team2->players->isNotEmpty())
                        <optgroup label="{{ $fixture->team2->name }}">
                            @foreach ($fixture->team2->players as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} {{ $p->reg_no ? "({$p->reg_no})" : '' }}</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            <!-- Title -->
            <div>
                <label for="event_title" class="block text-xs font-semibold text-slate-600 mb-1">Event Title <span class="text-rose-500">*</span></label>
                <input type="text" id="event_title" name="title" required value="Goal"
                       placeholder="e.g. Goal Scored, Caught behind, Dangerous Foul..."
                       class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400">
            </div>

            <!-- Description -->
            <div>
                <label for="event_description" class="block text-xs font-semibold text-slate-600 mb-1">Commentary / Note (Optional)</label>
                <input type="text" id="event_description" name="description"
                       placeholder="e.g. Scored with a curling left-footed shot into top corner"
                       class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400">
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeEventModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="btn-submit-event" class="px-5 py-2 rounded-xl text-xs font-bold bg-[#0a1226] text-amber-300 hover:bg-[#060b18] shadow-sm transition-colors">
                    Add Moment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEventModal() {
        document.getElementById('event-modal').classList.remove('hidden');
    }

    function closeEventModal() {
        document.getElementById('event-modal').classList.add('hidden');
    }

    function autoFillTitle(name) {
        document.getElementById('event_title').value = name;
    }

    async function submitEventForm(e) {
        e.preventDefault();
        const form = document.getElementById('add-event-form');
        const formData = new FormData(form);
        const btn = document.getElementById('btn-submit-event');
        btn.disabled = true;

        try {
            const response = await fetch('{{ route('admin.fixtures.events.store', $fixture) }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const data = await response.json();
            if (response.ok && data.success) {
                showToast('Event recorded to timeline!');
                closeEventModal();
                form.reset();
                appendEventToPadList(data.event);
            } else {
                showToast('Failed to record event.', true);
            }
        } catch (err) {
            console.error(err);
            showToast('Error recording event.', true);
        } finally {
            btn.disabled = false;
        }
    }

    function appendEventToPadList(ev) {
        const list = document.getElementById('pad-events-list');
        const noText = document.getElementById('pad-no-events-text');
        if (noText) noText.classList.add('hidden');
        if (list) {
            list.classList.remove('hidden');
            const row = document.createElement('div');
            row.id = `event-row-${ev.id}`;
            row.className = 'flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs';
            row.innerHTML = `
                <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                    <span class="text-base shrink-0">${ev.icon}</span>
                    <div class="min-w-0">
                        <div class="flex items-center space-x-1.5">
                            ${ev.minute ? `<span class="font-mono font-bold text-[10px] bg-slate-900 text-amber-300 px-1.5 py-0.5 rounded">${ev.minute}</span>` : ''}
                            <span class="font-bold text-slate-800 truncate">${ev.title}</span>
                        </div>
                        ${(ev.player || ev.team) ? `
                            <span class="text-[10px] text-slate-500 truncate block">
                                ${ev.player || ''} ${ev.team ? `(${ev.team})` : ''}
                            </span>
                        ` : ''}
                    </div>
                </div>
                <button type="button" onclick="deleteEvent(${ev.id})" class="text-rose-500 hover:text-rose-700 text-xs px-2 py-1 rounded hover:bg-rose-50 transition-colors shrink-0">
                    ✕
                </button>
            `;
            list.appendChild(row);
        }
    }

    async function deleteEvent(eventId) {
        if (!confirm('Remove this event from the timeline?')) return;
        try {
            const url = `/admin/fixtures/{{ $fixture->id }}/events/${eventId}`;
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            if (response.ok) {
                const row = document.getElementById(`event-row-${eventId}`);
                if (row) row.remove();
                showToast('Event removed from timeline.');
            }
        } catch (err) {
            console.error(err);
        }
    }
    function adjustScore(fieldId, delta) {
        const input = document.getElementById(fieldId);
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        input.value = val;
    }

    function setScore(fieldId, val) {
        const input = document.getElementById(fieldId);
        input.value = Math.max(0, val);
    }

    function updateStateTheme() {
        const status = document.querySelector('input[name="status"]:checked')?.value || 'scheduled';
        const badge = document.getElementById('live-indicator-badge');

        if (status === 'live') {
            badge.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-rose-500 text-white animate-pulse"><span class="w-2 h-2 mr-1.5 rounded-full bg-white"></span>LIVE MATCH</span>';
        } else if (status === 'completed') {
            badge.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-200">COMPLETED</span>';
        } else {
            badge.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/40">SCHEDULED</span>';
        }
    }

    async function submitAjaxScore() {
        const btn = document.getElementById('btn-save-ajax');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Publishing...</span>';

        const form = document.getElementById('quick-scorer-form');
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast(data.message || 'Score successfully broadcasted!');
                updateStateTheme();
            } else {
                showToast(data.message || 'Failed to update score.', true);
            }
        } catch (error) {
            console.error(error);
            // Fallback: standard submit if network error
            form.submit();
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    function showToast(message, isError = false) {
        const toast = document.getElementById('scorer-toast');
        const toastMsg = document.getElementById('toast-message');
        const box = toast.firstElementChild;

        toastMsg.textContent = message;
        if (isError) {
            box.classList.remove('bg-emerald-600', 'border-emerald-400');
            box.classList.add('bg-rose-600', 'border-rose-400');
        } else {
            box.classList.remove('bg-rose-600', 'border-rose-400');
            box.classList.add('bg-emerald-600', 'border-emerald-400');
        }

        toast.classList.remove('translate-y-[-100px]', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-[-100px]', 'opacity-0');
        }, 3000);
    }
</script>
@endsection
