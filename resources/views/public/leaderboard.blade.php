@extends('layouts.public')

@section('title', 'Player Stats & Leaderboard | KIU Sports Gala')

@section('content')
<div class="space-y-8">

    <!-- Header & Filter Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="text-xs uppercase tracking-widest font-sports font-black text-amber-300 bg-amber-400/20 px-3 py-0.5 rounded-full border border-amber-400/40">
                        Official Athlete Rankings
                    </span>
                    <span class="text-xs text-blue-200">Tournament Individual Honours</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black font-sports uppercase tracking-wide text-white">
                    Stats &amp; Player Leaderboards
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 mt-1 max-w-2xl font-medium">
                    Tracking KIU's top goalscorers, wicket takers, big hitters, and Player of the Match MVP leaders.
                </p>
            </div>

            <!-- Edition & Sport Selector Form -->
            <form method="GET" action="{{ route('leaderboard') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                <!-- Edition Selector -->
                <div>
                    <label for="edition_id" class="sr-only">Edition</label>
                    <select name="edition_id" id="edition_id" onchange="this.form.submit()"
                            class="w-full sm:w-auto rounded-xl border-blue-800 bg-blue-950 text-amber-300 text-xs font-semibold focus:border-amber-400 focus:ring-amber-400 py-2.5 px-3.5 shadow-sm">
                        @foreach ($editions as $edition)
                            <option value="{{ $edition->id }}" {{ $selectedEdition?->id == $edition->id ? 'selected' : '' }}>
                                {{ $edition->title }} ({{ $edition->year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sport Selector -->
                <div>
                    <label for="sport_id" class="sr-only">Sport</label>
                    <select name="sport_id" id="sport_id" onchange="this.form.submit()"
                            class="w-full sm:w-auto rounded-xl border-blue-800 bg-blue-950 text-amber-300 text-xs font-semibold focus:border-amber-400 focus:ring-amber-400 py-2.5 px-3.5 shadow-sm">
                        <option value="">All Sports Disciplines</option>
                        @foreach ($sports as $sport)
                            <option value="{{ $sport->id }}" {{ $selectedSport?->id == $sport->id ? 'selected' : '' }}>
                                {{ $sport->name }} ({{ ucfirst($sport->category) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- 4 Main Leaderboard Pillars Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- 1. Gala MVP Race (Player of the Match) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-yellow-600 p-4 text-slate-950 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="text-2xl">⭐</span>
                        <div>
                            <h3 class="font-sports font-black text-base uppercase tracking-wider text-slate-950">Gala MVP Award</h3>
                            <span class="text-[10px] font-bold text-slate-900/80 uppercase">Most Player of the Match Trophies</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-950/20 text-slate-950 font-black text-xs font-mono">
                        {{ $leaderboards['mvp_leaders']->count() }} {{ Str::plural('Winner', $leaderboards['mvp_leaders']->count()) }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100 p-2">
                    @forelse($leaderboards['mvp_leaders'] as $idx => $player)
                        <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors">
                            <div class="flex items-center space-x-3 min-w-0 pr-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $idx === 0 ? 'bg-amber-400 text-slate-950' : ($idx === 1 ? 'bg-slate-300 text-slate-800' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-500')) }}">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('players.profile', $player) }}" class="font-bold text-slate-900 hover:text-blue-700 transition-colors text-sm truncate block">
                                        {{ $player->name }}
                                    </a>
                                    <span class="text-[11px] text-slate-500 truncate block">
                                        {{ $player->team->name ?? 'Team' }} &bull; {{ $player->team->department->name ?? 'Dept' }}
                                    </span>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 font-mono font-black text-sm shrink-0">
                                {{ $player->award_count }} ⭐
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No Player of the Match awards assigned yet for this selection.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 2. Golden Boot (Football / Futsal Goals) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 p-4 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="text-2xl">⚽</span>
                        <div>
                            <h3 class="font-sports font-black text-base uppercase tracking-wider text-amber-300">Golden Boot</h3>
                            <span class="text-[10px] font-bold text-blue-200 uppercase">Top Football &amp; Futsal Goalscorers</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10 text-amber-300 font-black text-xs font-mono">
                        {{ $leaderboards['top_goalscorers']->count() }} {{ Str::plural('Scorer', $leaderboards['top_goalscorers']->count()) }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100 p-2">
                    @forelse($leaderboards['top_goalscorers'] as $idx => $player)
                        <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors">
                            <div class="flex items-center space-x-3 min-w-0 pr-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $idx === 0 ? 'bg-amber-400 text-slate-950' : ($idx === 1 ? 'bg-slate-300 text-slate-800' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-500')) }}">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('players.profile', $player) }}" class="font-bold text-slate-900 hover:text-blue-700 transition-colors text-sm truncate block">
                                        {{ $player->name }}
                                    </a>
                                    <span class="text-[11px] text-slate-500 truncate block">
                                        {{ $player->team->name ?? 'Team' }} &bull; {{ $player->team->department->name ?? 'Dept' }}
                                    </span>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 font-mono font-black text-sm shrink-0">
                                {{ $player->goals_count }} ⚽
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No goal records recorded yet for this selection.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 3. Purple Cap (Cricket Wickets) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="bg-gradient-to-r from-purple-900 via-indigo-950 to-purple-900 p-4 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="text-2xl">🏏</span>
                        <div>
                            <h3 class="font-sports font-black text-base uppercase tracking-wider text-purple-300">Purple Cap</h3>
                            <span class="text-[10px] font-bold text-purple-200 uppercase">Top Cricket Wicket Takers</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10 text-purple-200 font-black text-xs font-mono">
                        {{ $leaderboards['top_wicket_takers']->count() }} {{ Str::plural('Bowler', $leaderboards['top_wicket_takers']->count()) }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100 p-2">
                    @forelse($leaderboards['top_wicket_takers'] as $idx => $player)
                        <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors">
                            <div class="flex items-center space-x-3 min-w-0 pr-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $idx === 0 ? 'bg-amber-400 text-slate-950' : ($idx === 1 ? 'bg-slate-300 text-slate-800' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-500')) }}">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('players.profile', $player) }}" class="font-bold text-slate-900 hover:text-blue-700 transition-colors text-sm truncate block">
                                        {{ $player->name }}
                                    </a>
                                    <span class="text-[11px] text-slate-500 truncate block">
                                        {{ $player->team->name ?? 'Team' }} &bull; {{ $player->team->department->name ?? 'Dept' }}
                                    </span>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-xl bg-purple-50 border border-purple-200 text-purple-900 font-mono font-black text-sm shrink-0">
                                {{ $player->wickets_count }} ☝️
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No wicket events logged yet for this selection.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 4. Six Kings (Cricket Sixes) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="bg-gradient-to-r from-rose-900 via-rose-950 to-red-950 p-4 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="text-2xl">🚀</span>
                        <div>
                            <h3 class="font-sports font-black text-base uppercase tracking-wider text-rose-300">Six Kings</h3>
                            <span class="text-[10px] font-bold text-rose-200 uppercase">Most Maximums Hit (6s)</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10 text-rose-200 font-black text-xs font-mono">
                        {{ $leaderboards['top_six_hitters']->count() }} {{ Str::plural('Hitter', $leaderboards['top_six_hitters']->count()) }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100 p-2">
                    @forelse($leaderboards['top_six_hitters'] as $idx => $player)
                        <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors">
                            <div class="flex items-center space-x-3 min-w-0 pr-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $idx === 0 ? 'bg-amber-400 text-slate-950' : ($idx === 1 ? 'bg-slate-300 text-slate-800' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-500')) }}">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('players.profile', $player) }}" class="font-bold text-slate-900 hover:text-blue-700 transition-colors text-sm truncate block">
                                        {{ $player->name }}
                                    </a>
                                    <span class="text-[11px] text-slate-500 truncate block">
                                        {{ $player->team->name ?? 'Team' }} &bull; {{ $player->team->department->name ?? 'Dept' }}
                                    </span>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 font-mono font-black text-sm shrink-0">
                                {{ $player->sixes_count }} 💥
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No boundary sixes recorded yet for this selection.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

