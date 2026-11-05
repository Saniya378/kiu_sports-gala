@extends('layouts.public')

@section('title', 'Tournament Schedule | KIU Sports Gala')

@section('content')
<div class="space-y-8">

    <!-- Header & Edition Selection Banner (Unified Royal Blue & Athletic Gold) -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-sports font-black uppercase tracking-wider text-amber-300">
                    Official Fixture List
                </span>
                <h1 class="text-2xl sm:text-4xl font-black font-sports uppercase tracking-wide text-white mt-0.5">
                    Tournament Schedule
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 mt-1 font-medium">
                    Complete timetable, venues, and matchups for the Karakoram International University Sports Gala.
                </p>
            </div>

            <!-- Edition Selector Dropdown -->
            <form method="GET" action="{{ route('schedule') }}" class="self-start md:self-auto">
                <label for="edition_id" class="block text-[11px] font-bold uppercase tracking-wider text-amber-300 mb-1">
                    Select Edition
                </label>
                <select name="edition_id" id="edition_id" onchange="this.form.submit()"
                        class="rounded-xl border-blue-800 bg-blue-950 text-amber-300 text-xs font-semibold focus:border-amber-400 focus:ring-amber-400 py-2.5 px-4 shadow-sm w-full sm:w-60">
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}" {{ $selectedEdition?->id == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }}) &bull; {{ ucfirst($edition->status) }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if ($selectedEdition)
        <!-- View Switcher & Quick Navigation -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-blue-950 text-amber-400 shadow-xs">
                    📋 List Timetable
                </span>
                <a href="{{ route('bracket', ['edition_id' => $selectedEdition->id, 'sport_id' => request('sport_id')]) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    🌳 Visual Bracket Tree
                </a>
                <a href="{{ route('results', ['edition_id' => $selectedEdition->id, 'sport_id' => request('sport_id')]) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    🏅 Standings & Points
                </a>
            </div>

            <div class="text-xs text-slate-500 font-medium">
                Showing scheduled & completed match timetables
            </div>
        </div>

        <!-- Filters Toolbar (Sport & Date) -->
        <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-4">
            <form method="GET" action="{{ route('schedule') }}" class="flex flex-col md:flex-row gap-4 items-end">
                <input type="hidden" name="edition_id" value="{{ $selectedEdition->id }}">

                <!-- Filter: Sport -->
                <div class="w-full md:w-60">
                    <label for="sport_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                        Sport Discipline
                    </label>
                    <select name="sport_id" id="sport_id"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">All Sports</option>
                        @foreach ($sports as $sport)
                            <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                                {{ $sport->name }} ({{ ucfirst($sport->category) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter: Date -->
                <div class="w-full md:w-52">
                    <label for="date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                        Match Date
                    </label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                </div>

                <!-- Buttons -->
                <div class="flex items-center space-x-2">
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['sport_id', 'date']))
                        <a href="{{ route('schedule', ['edition_id' => $selectedEdition->id]) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Fixtures Timeline / Cards -->
        @if ($fixtures->count() > 0)
            <div class="space-y-4">
                @foreach ($fixtures as $fixture)
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)] hover:shadow-[0_12px_28px_-6px_rgba(15,23,42,0.12)] hover:-translate-y-0.5 transition-all duration-200 flex flex-col md:flex-row md:items-center justify-between gap-4 group">

                        <!-- Left: Sport, Round & Time -->
                        <div class="md:w-56 shrink-0 space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span class="font-sports font-bold uppercase tracking-wider text-sm text-slate-900">{{ $fixture->sport->name }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $fixture->sport->category === 'girls' ? 'bg-pink-500/10 text-pink-700 border border-pink-200' : 'bg-cyan-500/10 text-cyan-700 border border-cyan-200' }}">
                                    {{ ucfirst($fixture->sport->category) }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500">
                                @if ($fixture->match_date)
                                    <span class="font-semibold text-slate-700">{{ $fixture->match_date->format('D, M d, Y') }}</span>
                                    @if ($fixture->match_time)
                                        &bull; {{ \Carbon\Carbon::parse($fixture->match_time)->format('g:i A') }}
                                    @endif
                                @else
                                    <span class="italic text-slate-400">Date & time TBD</span>
                                @endif
                            </div>
                            @if ($fixture->round)
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $fixture->round }}
                                </span>
                            @endif
                        </div>

                        <!-- Center: Matchup & Scores with Department Crests -->
                        <div class="flex-1 max-w-xl">
                            <div class="grid grid-cols-5 items-center gap-2">
                                <!-- Team 1 -->
                                <div class="col-span-2 flex items-center justify-end space-x-2.5 text-right">
                                    <div class="min-w-0">
                                        <div class="font-sports font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors text-sm sm:text-base leading-tight uppercase tracking-tight truncate">
                                            {{ $fixture->team1->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 truncate">{{ $fixture->team1->department->name ?? 'Dept' }}</div>
                                    </div>
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-900 to-indigo-900 border border-blue-700/60 text-amber-400 font-sports font-black flex items-center justify-center text-[10px] shrink-0 shadow-xs">
                                        {{ $fixture->team1->department->code ?? substr($fixture->team1->name, 0, 3) }}
                                    </div>
                                </div>

                                <!-- Score or VS -->
                                <div class="col-span-1 text-center">
                                    @if ($fixture->result)
                                        <div class="font-sports font-black text-lg sm:text-xl text-amber-400 px-2.5 py-1 rounded-xl bg-blue-950 shadow-xs tracking-tight border border-blue-800/60">
                                            {{ $fixture->formatTeamScore(1) }} - {{ $fixture->formatTeamScore(2) }}
                                        </div>
                                    @else
                                        <span class="text-[10px] font-sports font-black uppercase tracking-widest text-slate-400 px-2 py-1 bg-slate-100 rounded-lg border border-slate-200">
                                            VS
                                        </span>
                                    @endif
                                </div>

                                <!-- Team 2 -->
                                <div class="col-span-2 flex items-center justify-start space-x-2.5 text-left">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-950 to-slate-900 border border-blue-800/60 text-amber-400 font-sports font-black flex items-center justify-center text-[10px] shrink-0 shadow-xs">
                                        {{ $fixture->team2->department->code ?? substr($fixture->team2->name, 0, 3) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-sports font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors text-sm sm:text-base leading-tight uppercase tracking-tight truncate">
                                            {{ $fixture->team2->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 truncate">{{ $fixture->team2->department->name ?? 'Dept' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Venue, Status & Link -->
                        <div class="md:w-56 shrink-0 flex flex-col md:items-end justify-between space-y-2 border-t md:border-t-0 pt-3 md:pt-0 border-slate-100">
                            <div class="flex items-center gap-2">
                                @if ($fixture->stream_url)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white" title="Live Video Stream available">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Stream
                                    </span>
                                @endif

                                @if ($fixture->status === 'live')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        LIVE
                                    </span>
                                @elseif ($fixture->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-amber-400 border border-slate-700">
                                        FINAL
                                    </span>
                                @elseif ($fixture->status === 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-700">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-950 text-indigo-300 border border-indigo-800/60">
                                        Scheduled
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs text-slate-500 truncate flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $fixture->venue->name ?? 'Venue TBD' }}</span>
                            </div>

                            <a href="{{ route('matches.show', $fixture) }}" class="inline-flex items-center gap-1 font-sports font-bold text-xs uppercase tracking-wide text-indigo-600 hover:text-indigo-800 transition-colors">
                                <span>Match Detail & Photos</span> &rarr;
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-md mx-auto">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">No Fixtures Found</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">
                    No scheduled fixtures match your search criteria. Try choosing another sport or clear filters.
                </p>
                <a href="{{ route('schedule', ['edition_id' => $selectedEdition->id]) }}" class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-xs transition-colors">
                    Reset Filter
                </a>
            </div>
        @endif
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-md mx-auto">
            <h3 class="text-base font-bold text-slate-900">No Edition Available</h3>
            <p class="text-xs text-slate-500 mt-1">
                Please check back later once tournaments are announced.
            </p>
        </div>
    @endif

</div>
@endsection

