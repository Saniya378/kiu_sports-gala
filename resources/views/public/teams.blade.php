@extends('layouts.public')

@section('title', 'Participating Teams - KIU Sports Gala')

@section('content')
<div class="space-y-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400/80 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest font-bold text-amber-300 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/30">
                    Departmental Squads
                </span>
                <h1 class="text-2xl sm:text-4xl font-black text-white mt-3 tracking-tight">
                    Participating Teams & Squads
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1">
                    Explore registered academic department teams, disciplines, and player rosters.
                </p>
            </div>

            <!-- Filters Form -->
            <form method="GET" action="{{ route('teams.public') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <div>
                    <label for="edition_id" class="block text-[11px] font-bold text-amber-300 uppercase mb-1">Edition</label>
                    <select name="edition_id" id="edition_id" onchange="this.form.submit()"
                            class="bg-slate-900/90 border border-slate-700 rounded-xl px-3 py-2 text-amber-300 font-semibold text-xs focus:ring-2 focus:ring-amber-400">
                        @foreach($editions as $ed)
                            <option value="{{ $ed->id }}" {{ $selectedEdition && $selectedEdition->id === $ed->id ? 'selected' : '' }}>
                                {{ $ed->title }} ({{ $ed->year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sport_id" class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Sport</label>
                    <select name="sport_id" id="sport_id" onchange="this.form.submit()"
                            class="bg-slate-900/90 border border-slate-700 rounded-xl px-3 py-2 text-slate-200 text-xs focus:ring-2 focus:ring-amber-400">
                        <option value="">All Sports</option>
                        @foreach($sports as $sp)
                            <option value="{{ $sp->id }}" {{ (string)$selectedSportId === (string)$sp->id ? 'selected' : '' }}>
                                {{ $sp->name }} ({{ ucfirst($sp->category) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Teams Cards Grid -->
    @if($teams->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($teams as $team)
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs hover:shadow-md hover:border-blue-400 transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Header Bar -->
                        <div class="flex items-start justify-between pb-3 border-b border-slate-100 mb-4 gap-2">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $team->sport->name ?? 'Sport' }}
                                    @if($team->sport && $team->sport->category)
                                        ({{ ucfirst($team->sport->category) }})
                                    @endif
                                </span>
                                <h3 class="font-extrabold text-xl text-slate-900 mt-1.5 group-hover:text-blue-600 transition-colors">{{ $team->name }}</h3>
                                <p class="text-xs text-slate-500 font-medium">
                                    {{ $team->department->name ?? 'Department' }}
                                </p>
                            </div>
                            <span class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-500 font-black text-sm shrink-0">
                                🏅
                            </span>
                        </div>

                        <!-- Player Roster -->
                        <div>
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                <span class="font-bold uppercase tracking-wider text-slate-700">Squad Roster</span>
                                <span class="font-semibold text-blue-600">{{ $team->players->count() }} Players</span>
                            </div>

                            @if($team->players->isNotEmpty())
                                <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                    @foreach($team->players as $idx => $player)
                                        <div class="flex items-center justify-between text-xs py-1.5 px-2.5 rounded-lg bg-slate-50 border border-slate-100 text-slate-700 hover:bg-slate-100/80 transition-colors">
                                            <div class="min-w-0 pr-2">
                                                <span class="font-medium truncate block">{{ $idx + 1 }}. {{ $player->name }}</span>
                                                @if($player->reg_no)
                                                    <span class="text-[10px] font-mono text-slate-400 block truncate">{{ $player->reg_no }}</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('players.card', $player) }}"
                                               class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors shrink-0"
                                               title="View Digital Pass & QR">
                                                Pass 🪪
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic py-2">No players registered yet.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Link -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs gap-2">
                        <a href="{{ route('teams.id-cards', $team) }}"
                           class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                            <span class="mr-1">🪪</span> Squad Passes
                        </a>
                        <a href="{{ route('schedule', ['edition_id' => $team->edition_id, 'sport_id' => $team->sport_id]) }}"
                           class="font-bold text-blue-600 hover:text-blue-800 transition-colors">
                            Matches &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center max-w-lg mx-auto shadow-xs">
            <h3 class="text-xl font-bold text-slate-900 mb-2">No Teams Found</h3>
            <p class="text-sm text-slate-500">
                No teams match the selected edition and sport filter.
            </p>
        </div>
    @endif
</div>
@endsection
