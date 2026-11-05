@extends('layouts.public')

@section('title', $player->name . ' - Athlete Profile | KIU Sports Gala')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs md:text-sm text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('teams.public') }}" class="hover:text-blue-600 transition-colors">Teams</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold">{{ $player->name }}</span>
    </div>

    <!-- Hero Card -->
    <div class="bg-gradient-to-br from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 relative z-10">
            <!-- Avatar / Photo -->
            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-gradient-to-tr from-slate-950 to-blue-950 border-2 border-amber-400 p-1 shrink-0 shadow-lg flex items-center justify-center overflow-hidden">
                @if($player->photo_url)
                    <img src="{{ $player->photo_url }}" alt="{{ $player->name }}" class="w-full h-full object-cover rounded-xl">
                @else
                    <div class="w-full h-full rounded-xl bg-blue-950/80 flex items-center justify-center text-amber-400 font-sports font-black text-4xl">
                        {{ substr($player->name, 0, 2) }}
                    </div>
                @endif
            </div>

            <!-- Details -->
            <div class="flex-1 text-center sm:text-left">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1.5">
                    <span class="px-3 py-0.5 rounded-full bg-amber-400/20 text-amber-300 font-sports font-black text-xs border border-amber-400/40 uppercase tracking-wider">
                        {{ $player->team->sport->name ?? 'Athlete' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-950 text-blue-200 text-xs border border-blue-800">
                        {{ $player->team->edition->title ?? 'KIU Sports Gala' }} ({{ $player->team->edition->year ?? 'Annual' }})
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-sports font-black uppercase tracking-tight text-white">
                    {{ $player->name }}
                </h1>

                <p class="text-sm text-blue-200 mt-1 font-medium">
                    {{ $player->team->name ?? 'Team' }} &bull; <strong class="text-white">{{ $player->team->department->name ?? 'Department' }}</strong>
                </p>

                @if($player->reg_no)
                    <p class="text-xs text-blue-300 font-mono mt-0.5">
                        University Reg #: <strong>{{ $player->reg_no }}</strong>
                    </p>
                @endif

                <div class="mt-4 flex flex-wrap items-center justify-center sm:justify-start gap-3">
                    <a href="{{ route('players.card', $player) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black bg-amber-400 hover:bg-amber-300 text-slate-950 transition-all shadow-md">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>Official Digital ID Card &bull; QR</span>
                    </a>

                    <a href="{{ route('leaderboard', ['edition_id' => $player->team->edition_id, 'sport_id' => $player->team->sport_id]) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-blue-200 transition-all">
                        <span>View in Leaderboard &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Career Statistics Counters Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- POTM Awards -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-xs">
            <span class="text-2xl block mb-1">⭐</span>
            <span class="font-mono font-black text-2xl text-slate-900 block">{{ $stats['potm_awards'] }}</span>
            <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 block mt-0.5">Player of Match</span>
        </div>

        <!-- Goals -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-xs">
            <span class="text-2xl block mb-1">⚽</span>
            <span class="font-mono font-black text-2xl text-slate-900 block">{{ $stats['goals'] }}</span>
            <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 block mt-0.5">Goals Scored</span>
        </div>

        <!-- Wickets -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-xs">
            <span class="text-2xl block mb-1">🏏</span>
            <span class="font-mono font-black text-2xl text-slate-900 block">{{ $stats['wickets'] }}</span>
            <span class="text-[10px] font-black uppercase tracking-wider text-purple-600 block mt-0.5">Wickets Taken</span>
        </div>

        <!-- Sixes -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-xs">
            <span class="text-2xl block mb-1">🚀</span>
            <span class="font-mono font-black text-2xl text-slate-900 block">{{ $stats['sixes'] }}</span>
            <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 block mt-0.5">Sixes Hit</span>
        </div>

        <!-- Fours -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-xs">
            <span class="text-2xl block mb-1">⚡</span>
            <span class="font-mono font-black text-2xl text-slate-900 block">{{ $stats['fours'] }}</span>
            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 block mt-0.5">Boundaries (4s)</span>
        </div>

        <!-- Disciplinary -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-xs">
            <span class="text-2xl block mb-1">🟨</span>
            <span class="font-mono font-black text-2xl text-slate-900 block">{{ $stats['yellow_cards'] + $stats['red_cards'] }}</span>
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block mt-0.5">Cards Received</span>
        </div>
    </div>

    <!-- Match Milestones & Event Log -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-sports font-black text-lg text-slate-900 uppercase tracking-wide flex items-center gap-2">
                <span>🏆</span>
                <span>Match Highlights &amp; Milestones Log</span>
            </h3>
            <span class="text-xs font-bold text-slate-400">
                {{ $player->matchEvents->count() }} Key Actions Logged
            </span>
        </div>

        <div class="p-5">
            @if($player->matchEvents->isEmpty() && $player->playerOfTheMatchAwards->isEmpty())
                <div class="text-center py-10 text-slate-400 text-xs">
                    No individual match events logged yet for {{ $player->name }}. Actions will appear as matches are scored live.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($player->matchEvents as $ev)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 hover:border-amber-300 transition-colors">
                            <div class="flex items-center space-x-3 min-w-0 pr-3">
                                <span class="text-2xl shrink-0">{{ $ev->icon }}</span>
                                <div class="min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-slate-900 text-sm truncate">{{ $ev->title }}</span>
                                        @if($ev->minute)
                                            <span class="font-mono text-[10px] bg-slate-900 text-amber-300 px-2 py-0.5 rounded font-bold">{{ $ev->minute }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-500 block mt-0.5">
                                        vs {{ $ev->fixture?->team1_id === $player->team_id ? $ev->fixture?->team2?->name : $ev->fixture?->team1?->name }}
                                        &bull; {{ $ev->fixture?->sport?->name }}
                                    </span>
                                </div>
                            </div>
                            @if($ev->fixture)
                                <a href="{{ route('matches.show', $ev->fixture) }}" class="text-xs font-bold text-indigo-700 hover:text-indigo-900 shrink-0">
                                    Match Details &rarr;
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

