@extends('layouts.public')

@section('title', 'Results & Points Table - KIU Sports Gala')

@section('content')
<div class="space-y-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400/80 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-widest font-bold text-amber-300 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/30">
                    Tournament Standings
                </span>
                <h1 class="text-2xl sm:text-4xl font-black text-white mt-3 tracking-tight">
                    Results & Points Table
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1">
                    Official department standings, points breakdown, and completed match scores across sports.
                </p>
            </div>
        </div>
    </div>

    <!-- Edition & Sport Filters -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
        <form method="GET" action="{{ route('results') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
            <!-- Edition Selector (Defaults to latest edition) -->
            <div>
                <label for="edition_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Edition
                </label>
                <select name="edition_id" id="edition_id" onchange="this.form.submit()"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                    @forelse($editions as $edition)
                        <option value="{{ $edition->id }}" {{ $selectedEdition && $selectedEdition->id === $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }}) {{ $edition->status === 'running' ? '• Live' : '' }}
                        </option>
                    @empty
                        <option value="">No Editions Available</option>
                    @endforelse
                </select>
            </div>

            <!-- Sport Selector -->
            <div>
                <label for="sport_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Sport Discipline
                </label>
                <select name="sport_id" id="sport_id" onchange="this.form.submit()"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                    @forelse($sports as $sport)
                        <option value="{{ $sport->id }}" {{ $selectedSport && $selectedSport->id === $sport->id ? 'selected' : '' }}>
                            {{ $sport->name }} @if($sport->category) ({{ ucfirst($sport->category) }}) @endif
                        </option>
                    @empty
                        <option value="">No Sports Available</option>
                    @endforelse
                </select>
            </div>

            <!-- Filter submit button -->
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-bold text-sm transition-all shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    @if(!$selectedEdition)
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-xs">
            <h3 class="text-xl font-bold text-slate-900 mb-2">No Gala Editions Found</h3>
            <p class="text-sm text-slate-500">Please check back soon for upcoming editions and match records.</p>
        </div>
    @elseif(!$selectedSport)
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-xs">
            <h3 class="text-xl font-bold text-slate-900 mb-2">No Sports Configured</h3>
            <p class="text-sm text-slate-500">No sports categories are available for standings calculation.</p>
        </div>
    @else
        <!-- Overall Department Medal Tally Section -->
        <div id="general-trophy" class="space-y-6 pt-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="text-[11px] uppercase tracking-widest font-extrabold text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-300 inline-block mb-1">
                        KIU General Trophy Standings
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 flex items-center gap-2">
                        <span>Overall Department Medal Tally</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $selectedEdition->title }} ({{ $selectedEdition->year }}) &bull; Ranked by Gold 🥇 (5 pts), Silver 🥈 (3 pts), Bronze 🥉 (1 pt).
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="#sport-points" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                        Jump to {{ $selectedSport->name }} &darr;
                    </a>
                </div>
            </div>

            @if($medalTally->isNotEmpty())
                <!-- Top 3 Podium Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- 2nd Place (Silver) -->
                    @if($medalTally->count() > 1 && isset($medalTally[1]))
                        @php $second = $medalTally[1]; @endphp
                        <div class="bg-gradient-to-b from-slate-50 to-white border-2 border-slate-300 rounded-2xl p-5 shadow-sm flex flex-col justify-between order-2 md:order-1">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-slate-200 text-slate-800">
                                        🥈 2nd Place
                                    </span>
                                    <span class="text-xs font-bold text-slate-500">{{ $second['points'] }} PTS</span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900">{{ $second['department']->name }}</h3>
                                <div class="flex items-center gap-3 text-xs text-slate-600 mt-3 pt-3 border-t border-slate-100">
                                    <span>🥇 <strong>{{ $second['gold'] }}</strong></span>
                                    <span>🥈 <strong>{{ $second['silver'] }}</strong></span>
                                    <span>🥉 <strong>{{ $second['bronze'] }}</strong></span>
                                    <span class="text-slate-400">({{ $second['total_medals'] }} Medals)</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- 1st Place (Gold) -->
                    @if(isset($medalTally[0]))
                        @php $first = $medalTally[0]; @endphp
                        <div class="bg-gradient-to-b from-amber-500/10 via-amber-50/40 to-white border-2 border-amber-400 rounded-2xl p-6 shadow-md flex flex-col justify-between order-1 md:order-2 transform md:-translate-y-2 relative">
                            <div class="absolute -top-3 right-6 bg-gradient-to-r from-amber-400 to-amber-500 text-slate-950 text-[10px] font-black uppercase tracking-widest px-3 py-0.5 rounded-full shadow-sm">
                                Leader &bull; General Trophy
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-amber-400 text-slate-950 shadow-sm">
                                        🏆 1st Champion
                                    </span>
                                    <span class="text-sm font-black text-amber-600">{{ $first['points'] }} PTS</span>
                                </div>
                                <h3 class="text-xl font-black text-slate-900">{{ $first['department']->name }}</h3>
                                <div class="flex items-center gap-3 text-xs text-slate-700 mt-3 pt-3 border-t border-amber-100">
                                    <span class="font-bold text-amber-700">🥇 {{ $first['gold'] }}</span>
                                    <span class="font-bold text-slate-700">🥈 {{ $first['silver'] }}</span>
                                    <span class="font-bold text-amber-900">🥉 {{ $first['bronze'] }}</span>
                                    <span class="text-slate-500">({{ $first['total_medals'] }} Total Medals)</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- 3rd Place (Bronze) -->
                    @if($medalTally->count() > 2 && isset($medalTally[2]))
                        @php $third = $medalTally[2]; @endphp
                        <div class="bg-gradient-to-b from-amber-900/5 to-white border-2 border-amber-700/30 rounded-2xl p-5 shadow-sm flex flex-col justify-between order-3">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900">
                                        🥉 3rd Place
                                    </span>
                                    <span class="text-xs font-bold text-slate-500">{{ $third['points'] }} PTS</span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900">{{ $third['department']->name }}</h3>
                                <div class="flex items-center gap-3 text-xs text-slate-600 mt-3 pt-3 border-t border-slate-100">
                                    <span>🥇 <strong>{{ $third['gold'] }}</strong></span>
                                    <span>🥈 <strong>{{ $third['silver'] }}</strong></span>
                                    <span>🥉 <strong>{{ $third['bronze'] }}</strong></span>
                                    <span class="text-slate-400">({{ $third['total_medals'] }} Medals)</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Detailed Medal Table -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-slate-900 text-slate-200 text-xs uppercase tracking-wider border-b border-slate-800">
                                    <th class="py-3.5 px-4 font-bold text-center w-14">Rank</th>
                                    <th class="py-3.5 px-4 font-bold">Department</th>
                                    <th class="py-3.5 px-3 font-bold text-center text-amber-400">🥇 Gold</th>
                                    <th class="py-3.5 px-3 font-bold text-center text-slate-300">🥈 Silver</th>
                                    <th class="py-3.5 px-3 font-bold text-center text-amber-300">🥉 Bronze</th>
                                    <th class="py-3.5 px-3 font-bold text-center">Total</th>
                                    <th class="py-3.5 px-4 font-black text-center text-amber-400 bg-slate-950">Points</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($medalTally as $row)
                                    <tr class="hover:bg-slate-50 transition-colors {{ $row['rank'] === 1 ? 'bg-amber-50/40' : '' }}">
                                        <td class="py-3.5 px-4 text-center font-bold">
                                            @if($row['rank'] === 1)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black text-xs shadow-xs">
                                                    1
                                                </span>
                                            @elseif($row['rank'] === 2)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-800 font-bold text-xs">
                                                    2
                                                </span>
                                            @elseif($row['rank'] === 3)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-900 font-bold text-xs">
                                                    3
                                                </span>
                                            @else
                                                <span class="text-slate-500 font-semibold">{{ $row['rank'] }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900">{{ $row['department']->name }}</div>
                                            @if(!empty($row['breakdown']['gold']))
                                                <div class="text-[11px] text-amber-700 font-medium mt-0.5">
                                                    🏆 Won Gold in: {{ implode(', ', $row['breakdown']['gold']) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-3 text-center font-black text-amber-600">{{ $row['gold'] }}</td>
                                        <td class="py-3.5 px-3 text-center font-bold text-slate-600">{{ $row['silver'] }}</td>
                                        <td class="py-3.5 px-3 text-center font-bold text-amber-800">{{ $row['bronze'] }}</td>
                                        <td class="py-3.5 px-3 text-center font-bold text-slate-800">{{ $row['total_medals'] }}</td>
                                        <td class="py-3.5 px-4 text-center font-black text-slate-950 bg-slate-50 text-base">
                                            {{ $row['points'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-500 shadow-xs">
                    <p class="text-sm font-medium text-slate-700">Tournament in progress!</p>
                    <p class="text-xs text-slate-500 mt-1">Department medal tally will update live as sport finals and podium finishes conclude.</p>
                </div>
            @endif
        </div>

        <div class="border-t-2 border-slate-200/80 my-8"></div>

        <!-- Points Table Section -->
        <div id="sport-points" class="space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Points Table: {{ $selectedSport->name }}</span>
                        @if($selectedSport->category)
                            <span class="text-sm font-semibold text-blue-600">({{ ucfirst($selectedSport->category) }})</span>
                        @endif
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $selectedEdition->title }} ({{ $selectedEdition->year }}) &bull; Win: 3 pts, Draw: 1 pt, Loss: 0 pts
                    </p>
                </div>
            </div>

            @if($standings->isNotEmpty())
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-slate-900 text-slate-200 text-xs uppercase tracking-wider border-b border-slate-800">
                                    <th class="py-3.5 px-4 font-bold text-center w-14">Pos</th>
                                    <th class="py-3.5 px-4 font-bold">Team / Department</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Matches Played">P</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Matches Won">W</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Matches Drawn">D</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Matches Lost">L</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Score For">SF</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Score Against">SA</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Score Difference">+/-</th>
                                    <th class="py-3.5 px-4 font-extrabold text-center text-amber-400 bg-slate-950" title="Total Points">PTS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($standings as $index => $row)
                                    <tr class="hover:bg-slate-50 transition-colors {{ $index === 0 ? 'bg-amber-50/50' : '' }}">
                                        <!-- Position -->
                                        <td class="py-3.5 px-4 text-center font-bold">
                                            @if($index === 0)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black text-xs shadow-sm">
                                                    1
                                                </span>
                                            @elseif($index === 1)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-800 font-black text-xs shadow-sm">
                                                    2
                                                </span>
                                            @elseif($index === 2)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700/80 text-white font-black text-xs shadow-sm">
                                                    3
                                                </span>
                                            @else
                                                <span class="text-slate-500 text-xs font-semibold">
                                                    {{ $index + 1 }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Team -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900 flex items-center gap-2">
                                                {{ $row['team']->name }}
                                                @if($index === 0)
                                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300">
                                                        Leader
                                                    </span>
                                                @endif
                                            </div>
                                            @if($row['team']->department)
                                                <div class="text-xs text-slate-500">
                                                    {{ $row['team']->department->name }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Record -->
                                        <td class="py-3.5 px-3 text-center text-slate-700 font-medium">{{ $row['played'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-emerald-700 font-semibold">{{ $row['won'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-slate-500">{{ $row['drawn'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-rose-600">{{ $row['lost'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-slate-600 text-xs">{{ $row['score_for'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-slate-600 text-xs">{{ $row['score_against'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-xs font-semibold {{ $row['score_diff'] > 0 ? 'text-emerald-600' : ($row['score_diff'] < 0 ? 'text-rose-600' : 'text-slate-500') }}">
                                            {{ $row['score_diff'] > 0 ? '+' . $row['score_diff'] : $row['score_diff'] }}
                                        </td>

                                        <!-- Points -->
                                        <td class="py-3.5 px-4 text-center font-black text-base text-slate-900 bg-amber-100/50">
                                            {{ $row['points'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                    <p class="text-sm text-slate-500">
                        No teams or points data recorded yet for {{ $selectedSport->name }} in {{ $selectedEdition->title }}.
                    </p>
                </div>
            @endif
        </div>

        <!-- Completed Match Results -->
        <div class="space-y-4 pt-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Completed Match Results</span>
                    <span class="text-sm font-bold text-blue-600">({{ $completedFixtures->count() }})</span>
                </h2>
            </div>

            @if($completedFixtures->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($completedFixtures as $fixture)
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:shadow-md hover:border-blue-400 transition-all flex flex-col justify-between group">
                            <div>
                                <!-- Fixture Header -->
                                <div class="flex items-center justify-between text-xs text-slate-500 pb-3 border-b border-slate-100 mb-3">
                                    <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">
                                        {{ $fixture->round ?? 'Match #' . $fixture->id }}
                                    </span>
                                    <span>
                                        {{ $fixture->match_date ? \Carbon\Carbon::parse($fixture->match_date)->format('M d, Y') : 'Date N/A' }}
                                    </span>
                                </div>

                                <!-- Teams & Scores Row -->
                                <div class="space-y-3 py-1">
                                    <!-- Team 1 -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 pr-3">
                                            <div class="font-bold text-slate-900 text-base flex items-center gap-1.5 group-hover:text-blue-600 transition-colors">
                                                <span>{{ $fixture->team1->name ?? 'Team 1' }}</span>
                                                @if($fixture->result && $fixture->result->winner_team_id === $fixture->team1_id)
                                                    <span class="text-amber-500 text-xs">🏆</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ $fixture->team1->department->name ?? '' }}
                                            </div>
                                        </div>
                                        <div class="text-2xl font-black {{ $fixture->result && $fixture->result->winner_team_id === $fixture->team1_id ? 'text-amber-600' : 'text-slate-900' }}">
                                            {{ $fixture->result->team1_score ?? 0 }}
                                        </div>
                                    </div>

                                    <!-- Team 2 -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 pr-3">
                                            <div class="font-bold text-slate-900 text-base flex items-center gap-1.5 group-hover:text-blue-600 transition-colors">
                                                <span>{{ $fixture->team2->name ?? 'Team 2' }}</span>
                                                @if($fixture->result && $fixture->result->winner_team_id === $fixture->team2_id)
                                                    <span class="text-amber-500 text-xs">🏆</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ $fixture->team2->department->name ?? '' }}
                                            </div>
                                        </div>
                                        <div class="text-2xl font-black {{ $fixture->result && $fixture->result->winner_team_id === $fixture->team2_id ? 'text-amber-600' : 'text-slate-900' }}">
                                            {{ $fixture->result->team2_score ?? 0 }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Winner & POTM announcement -->
                                <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                                    <div class="text-slate-700">
                                        @if($fixture->result && $fixture->result->winnerTeam)
                                            <span class="text-amber-600 font-bold">Winner:</span> {{ $fixture->result->winnerTeam->name }}
                                        @elseif($fixture->result && $fixture->result->winner_team_id === null)
                                            <span class="text-slate-600 font-semibold">Match Drawn</span>
                                        @endif
                                    </div>

                                    @if($fixture->result && $fixture->result->playerOfTheMatch)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                            ⭐ POTM: {{ $fixture->result->playerOfTheMatch->name }}
                                        </span>
                                    @endif

                                    @if($fixture->venue)
                                        <span class="text-slate-400 truncate max-w-[150px]">📍 {{ $fixture->venue->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer action -->
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400">
                                    {{ $fixture->photos->count() }} action photos
                                </span>
                                <a href="{{ route('matches.show', $fixture) }}"
                                   class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1">
                                    Match Details & Photos &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                    <p class="text-sm text-slate-500">
                        No completed matches found for this sport and edition.
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
