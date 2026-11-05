@extends('layouts.public')

@section('title', ($selectedSport ? $selectedSport->name . ' Bracket' : 'Tournament Brackets') . ' | KIU Sports Gala')

@section('content')
<div class="space-y-8">

    <!-- Header & Filters Banner (Unified Royal Blue & Athletic Gold) -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="text-xs uppercase tracking-widest font-sports font-black text-amber-300 bg-amber-400/20 px-3 py-0.5 rounded-full border border-amber-400/40">
                        Interactive Bracket Tree
                    </span>
                    <span class="text-xs text-blue-200">Single Elimination Roadmap</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black font-sports uppercase tracking-wide text-white">
                    Tournament Knockout Brackets
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 mt-1 max-w-2xl font-medium">
                    Track departmental playoff progression, elimination rounds, and championship finals across sports disciplines.
                </p>
            </div>

            <!-- Edition & Sport Selector Form -->
            <form method="GET" action="{{ route('bracket') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
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

    <!-- View Switcher Toolbar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-blue-950 text-amber-400 shadow-xs">
                🌳 Visual Bracket
            </span>
            <a href="{{ route('schedule', ['edition_id' => $selectedEdition?->id, 'sport_id' => $selectedSport?->id]) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                📋 List Timetable
            </a>
            <a href="{{ route('results', ['edition_id' => $selectedEdition?->id, 'sport_id' => $selectedSport?->id]) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                🏅 Standings & Points
            </a>
        </div>

        <div class="text-xs text-slate-500 font-medium">
            Active Sport: <strong class="text-slate-800">{{ $selectedSport?->name ?? 'Discipline' }}</strong>
            &bull; <span class="text-slate-400">Scroll horizontally on mobile &rarr;</span>
        </div>
    </div>

    <!-- Tournament Group Stages & Pools (if configured) -->
    @if ($selectedSport && !empty($bracketData['has_groups']) && $bracketData['groups']->isNotEmpty())
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl md:text-2xl font-bold font-sports uppercase tracking-wide text-slate-900 flex items-center gap-2">
                        <span>🏁</span>
                        <span>Group Stages &amp; Pool Standings</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Top 2 teams from each group advance to the elimination knockout tree below.</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-amber-100 text-amber-900 rounded-full border border-amber-300">
                    {{ $bracketData['groups']->count() }} {{ Str::plural('Group', $bracketData['groups']->count()) }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach ($bracketData['groups'] as $group)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-blue-950 px-4 py-3 flex items-center justify-between text-white border-b border-blue-900">
                            <span class="font-sports font-black text-sm uppercase tracking-wider text-amber-400 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                {{ $group['name'] }}
                            </span>
                            <span class="text-[11px] text-slate-300 font-medium">
                                {{ $group['matches_count'] }} {{ Str::plural('Fixture', $group['matches_count']) }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">#</th>
                                        <th class="py-2.5 px-3">Department / Team</th>
                                        <th class="py-2.5 px-2 text-center">P</th>
                                        <th class="py-2.5 px-2 text-center">W</th>
                                        <th class="py-2.5 px-2 text-center">D</th>
                                        <th class="py-2.5 px-2 text-center">L</th>
                                        <th class="py-2.5 px-2 text-center">Diff</th>
                                        <th class="py-2.5 px-3 text-right">Pts</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($group['teams'] as $idx => $tRow)
                                        <tr class="hover:bg-slate-50/80 transition-colors {{ $idx < 2 ? 'bg-emerald-50/30' : '' }}">
                                            <td class="py-2 px-3 font-mono font-bold text-slate-400">
                                                @if($idx < 2)
                                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] font-bold">
                                                        {{ $idx + 1 }}
                                                    </span>
                                                @else
                                                    {{ $idx + 1 }}
                                                @endif
                                            </td>
                                            <td class="py-2 px-3">
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-bold text-slate-900">{{ $tRow['team']->name }}</span>
                                                    @if($idx < 2)
                                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 uppercase tracking-tighter">
                                                            Qualify
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] text-slate-400 block">{{ $tRow['team']->department->name ?? '' }}</span>
                                            </td>
                                            <td class="py-2 px-2 text-center font-mono">{{ $tRow['played'] }}</td>
                                            <td class="py-2 px-2 text-center font-mono text-emerald-700 font-bold">{{ $tRow['won'] }}</td>
                                            <td class="py-2 px-2 text-center font-mono text-slate-500">{{ $tRow['drawn'] }}</td>
                                            <td class="py-2 px-2 text-center font-mono text-rose-600">{{ $tRow['lost'] }}</td>
                                            <td class="py-2 px-2 text-center font-mono text-[11px] {{ $tRow['score_diff'] >= 0 ? 'text-slate-700' : 'text-rose-500' }}">
                                                {{ $tRow['score_diff'] > 0 ? '+' . $tRow['score_diff'] : $tRow['score_diff'] }}
                                            </td>
                                            <td class="py-2 px-3 text-right font-mono font-black text-sm text-blue-950">
                                                {{ $tRow['points'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($selectedSport && $bracketData['rounds']->isNotEmpty())
        <!-- Visual Knockout Bracket Canvas -->
        <div class="overflow-x-auto pb-8 pt-4 -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="inline-flex items-stretch gap-8 min-w-full">

                @foreach ($bracketData['rounds'] as $stageName => $stageFixtures)
                    <!-- Bracket Column / Stage -->
                    <div class="w-80 shrink-0 flex flex-col justify-around space-y-6">
                        <!-- Stage Column Header -->
                        <div class="text-center pb-2 border-b-2 {{ $stageName === 'Final' ? 'border-amber-400' : 'border-slate-300' }}">
                            <span class="text-xs font-black uppercase tracking-wider {{ $stageName === 'Final' ? 'text-amber-600' : 'text-slate-700' }}">
                                @if($stageName === 'Final') 🏆 Championship Final @else {{ $stageName }} @endif
                            </span>
                            <div class="text-[11px] text-slate-400">
                                {{ $stageFixtures->count() }} Match{{ $stageFixtures->count() > 1 ? 'es' : '' }}
                            </div>
                        </div>

                        <!-- Matchup Cards in this Stage -->
                        <div class="flex-1 flex flex-col justify-around space-y-6">
                            @foreach ($stageFixtures as $fixture)
                                <div class="bg-white rounded-2xl border-2 {{ $stageName === 'Final' ? 'border-amber-400 shadow-md ring-1 ring-amber-400/40' : 'border-slate-200 shadow-xs' }} hover:shadow-md transition-all overflow-hidden">
                                    <!-- Match Meta Header -->
                                    <div class="bg-slate-50 px-3.5 py-2 border-b border-slate-100 flex items-center justify-between text-[11px]">
                                        <span class="font-bold text-slate-700 truncate">
                                            {{ $fixture->round ?: 'Match #' . $fixture->id }}
                                        </span>
                                        @if ($fixture->status === 'live')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                                                Live
                                            </span>
                                        @elseif ($fixture->status === 'completed')
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-200 text-slate-700">
                                                Final
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-mono">
                                                {{ $fixture->match_time ? \Carbon\Carbon::parse($fixture->match_time)->format('g:i A') : 'Scheduled' }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Teams Box -->
                                    <div class="p-3.5 space-y-2">
                                        <!-- Team 1 -->
                                        @php
                                            $isT1Winner = $fixture->result && $fixture->result->winner_team_id === $fixture->team1_id;
                                            $isT2Winner = $fixture->result && $fixture->result->winner_team_id === $fixture->team2_id;
                                        @endphp

                                        <div class="flex items-center justify-between p-2 rounded-xl {{ $isT1Winner ? 'bg-amber-50 border border-amber-300 font-bold' : ($isT2Winner ? 'opacity-60' : 'bg-slate-50/60') }}">
                                            <div class="min-w-0 pr-2">
                                                <div class="text-xs text-slate-900 truncate flex items-center gap-1">
                                                    @if($isT1Winner) <span class="text-amber-500">🏆</span> @endif
                                                    <span class="{{ $isT1Winner ? 'font-black text-slate-950' : 'font-semibold' }}">{{ $fixture->team1->name }}</span>
                                                </div>
                                                <div class="text-[10px] text-slate-500 truncate">{{ $fixture->team1->department->name ?? 'Dept' }}</div>
                                            </div>
                                            <span class="text-sm font-black font-mono {{ $isT1Winner ? 'text-amber-600' : 'text-slate-800' }}">
                                                {{ $fixture->result ? $fixture->result->team1_score : '-' }}
                                            </span>
                                        </div>

                                        <!-- Team 2 -->
                                        <div class="flex items-center justify-between p-2 rounded-xl {{ $isT2Winner ? 'bg-amber-50 border border-amber-300 font-bold' : ($isT1Winner ? 'opacity-60' : 'bg-slate-50/60') }}">
                                            <div class="min-w-0 pr-2">
                                                <div class="text-xs text-slate-900 truncate flex items-center gap-1">
                                                    @if($isT2Winner) <span class="text-amber-500">🏆</span> @endif
                                                    <span class="{{ $isT2Winner ? 'font-black text-slate-950' : 'font-semibold' }}">{{ $fixture->team2->name }}</span>
                                                </div>
                                                <div class="text-[10px] text-slate-500 truncate">{{ $fixture->team2->department->name ?? 'Dept' }}</div>
                                            </div>
                                            <span class="text-sm font-black font-mono {{ $isT2Winner ? 'text-amber-600' : 'text-slate-800' }}">
                                                {{ $fixture->result ? $fixture->result->team2_score : '-' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Footer Link -->
                                    <div class="bg-slate-50/60 px-3.5 py-1.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                                        <span class="truncate">{{ $fixture->venue->name ?? 'Ground Venue' }}</span>
                                        <a href="{{ route('matches.show', $fixture) }}" class="text-blue-600 hover:text-blue-800 font-bold ml-2">
                                            Details &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Champion Podium Stage (Rightmost Column) -->
                @if ($bracketData['champion'])
                    <div class="w-80 shrink-0 flex flex-col justify-center">
                        <div class="bg-gradient-to-br from-amber-500/20 via-amber-50 to-white border-2 border-amber-400 rounded-3xl p-6 text-center shadow-lg relative overflow-hidden">
                            <div class="absolute -top-12 -right-12 w-32 h-32 bg-amber-400/20 rounded-full blur-2xl"></div>

                            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-tr from-amber-400 to-amber-500 text-slate-950 flex items-center justify-center font-black text-3xl shadow-md shadow-amber-500/30 mb-4">
                                🏆
                            </div>

                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-700 bg-amber-400/20 px-3 py-1 rounded-full border border-amber-400/40">
                                Tournament Champion
                            </span>

                            @if(isset($bracketData['champion']['team']))
                                <h3 class="text-xl font-black text-slate-950 mt-2">
                                    {{ $bracketData['champion']['team']->name }}
                                </h3>
                                <p class="text-xs text-amber-800 font-bold mt-0.5">
                                    {{ $bracketData['champion']['department']->name ?? '' }}
                                </p>
                            @else
                                <h3 class="text-xl font-black text-slate-950 mt-2">
                                    {{ $bracketData['champion']['department']->name }}
                                </h3>
                            @endif

                            <div class="mt-4 pt-4 border-t border-amber-200/60 text-xs text-slate-600">
                                {{ $selectedSport->name }} Gold Medalist 🥇
                                <div class="text-[11px] text-slate-400 mt-0.5">KIU Sports Gala {{ $selectedEdition->year }}</div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-lg mx-auto shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">No Bracket Matches Configured</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">
                Knockout brackets are rendered when Quarter-Finals, Semi-Finals, and Final matches are scheduled for this sport discipline.
            </p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('schedule', ['edition_id' => $selectedEdition?->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-900 text-amber-300 font-bold text-xs rounded-xl shadow-sm hover:bg-blue-800 transition-colors">
                    View Match Timetable
                </a>
                <a href="{{ route('results', ['edition_id' => $selectedEdition?->id]) }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs rounded-xl transition-colors">
                    Check Standings
                </a>
            </div>
        </div>
    @endif

</div>
@endsection

