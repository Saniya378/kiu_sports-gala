@extends('layouts.public')

@section('title', 'Results & Points Table - KIU Sports Gala')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Header -->
    <div class="mb-8 text-center md:text-left">
        <span class="text-xs uppercase tracking-widest font-bold text-amber-400 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/20">
            Tournament Standings
        </span>
        <h1 class="text-3xl md:text-4xl font-black text-white mt-3 tracking-tight">
            Results & Points Table
        </h1>
        <p class="text-emerald-200/80 text-sm md:text-base mt-1">
            Official department standings, points breakdown, and completed match scores.
        </p>
    </div>

    <!-- Edition & Sport Filters -->
    <div class="bg-[#053d2b]/80 border border-emerald-800/80 rounded-2xl p-5 mb-8 shadow-xl">
        <form method="GET" action="{{ route('results') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
            <!-- Edition Selector (Defaults to latest edition) -->
            <div>
                <label for="edition_id" class="block text-xs font-semibold uppercase tracking-wider text-emerald-300 mb-1.5">
                    Edition
                </label>
                <select name="edition_id" id="edition_id" onchange="this.form.submit()"
                        class="w-full bg-[#022116] border border-emerald-700/80 rounded-xl px-3.5 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
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
                <label for="sport_id" class="block text-xs font-semibold uppercase tracking-wider text-emerald-300 mb-1.5">
                    Sport Discipline
                </label>
                <select name="sport_id" id="sport_id" onchange="this.form.submit()"
                        class="w-full bg-[#022116] border border-emerald-700/80 rounded-xl px-3.5 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
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
                        class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    @if(!$selectedEdition)
        <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-12 text-center">
            <h3 class="text-xl font-bold text-white mb-2">No Gala Editions Found</h3>
            <p class="text-sm text-emerald-200/70">Please check back soon for upcoming editions and match records.</p>
        </div>
    @elseif(!$selectedSport)
        <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-12 text-center">
            <h3 class="text-xl font-bold text-white mb-2">No Sports Configured</h3>
            <p class="text-sm text-emerald-200/70">No sports categories are available for standings calculation.</p>
        </div>
    @else
        <!-- Points Table Section -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="text-2xl font-black text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Points Table: {{ $selectedSport->name }}
                        @if($selectedSport->category)
                            <span class="text-sm font-semibold text-emerald-400">({{ ucfirst($selectedSport->category) }})</span>
                        @endif
                    </h2>
                    <p class="text-xs text-emerald-300/70 mt-0.5">
                        {{ $selectedEdition->title }} ({{ $selectedEdition->year }}) • Win: 3 pts, Draw: 1 pt, Loss: 0 pts
                    </p>
                </div>
            </div>

            @if($standings->isNotEmpty())
                <div class="bg-[#053d2b]/80 border border-emerald-800/80 rounded-2xl overflow-hidden shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-[#022116] text-emerald-300 text-xs uppercase tracking-wider border-b border-emerald-800/80">
                                    <th class="py-3.5 px-4 font-bold text-center w-14">Pos</th>
                                    <th class="py-3.5 px-4 font-bold">Team / Department</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Matches Played">P</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Matches Won">W</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Matches Drawn">D</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Matches Lost">L</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Score For">SF</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Score Against">SA</th>
                                    <th class="py-3.5 px-3 font-bold text-center" title="Score Difference">+/-</th>
                                    <th class="py-3.5 px-4 font-extrabold text-center text-amber-400 bg-amber-500/10" title="Total Points">PTS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-800/50">
                                @foreach($standings as $index => $row)
                                    <tr class="hover:bg-emerald-800/30 transition-colors {{ $index === 0 ? 'bg-amber-400/5' : '' }}">
                                        <!-- Position -->
                                        <td class="py-3.5 px-4 text-center font-bold">
                                            @if($index === 0)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black text-xs shadow">
                                                    1
                                                </span>
                                            @elseif($index === 1)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-300 text-slate-950 font-black text-xs shadow">
                                                    2
                                                </span>
                                            @elseif($index === 2)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700/80 text-white font-black text-xs shadow">
                                                    3
                                                </span>
                                            @else
                                                <span class="text-emerald-300/80 text-xs">
                                                    {{ $index + 1 }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Team -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-white flex items-center gap-2">
                                                {{ $row['team']->name }}
                                                @if($index === 0)
                                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                                        Leader
                                                    </span>
                                                @endif
                                            </div>
                                            @if($row['team']->department)
                                                <div class="text-xs text-emerald-300/80">
                                                    {{ $row['team']->department->name }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Record -->
                                        <td class="py-3.5 px-3 text-center text-emerald-100 font-medium">{{ $row['played'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-emerald-300 font-semibold">{{ $row['won'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-emerald-200/80">{{ $row['drawn'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-rose-300/80">{{ $row['lost'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-emerald-200/80 text-xs">{{ $row['score_for'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-emerald-200/80 text-xs">{{ $row['score_against'] }}</td>
                                        <td class="py-3.5 px-3 text-center text-xs font-semibold {{ $row['score_diff'] > 0 ? 'text-emerald-400' : ($row['score_diff'] < 0 ? 'text-rose-400' : 'text-emerald-300/80') }}">
                                            {{ $row['score_diff'] > 0 ? '+' . $row['score_diff'] : $row['score_diff'] }}
                                        </td>

                                        <!-- Points -->
                                        <td class="py-3.5 px-4 text-center font-black text-base text-amber-400 bg-amber-500/10">
                                            {{ $row['points'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-8 text-center">
                    <p class="text-sm text-emerald-200/80">
                        No teams or points data recorded yet for {{ $selectedSport->name }} in {{ $selectedEdition->title }}.
                    </p>
                </div>
            @endif
        </div>

        <!-- Completed Match Results -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-black text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Completed Match Results
                    <span class="text-sm font-normal text-amber-400">({{ $completedFixtures->count() }})</span>
                </h2>
            </div>

            @if($completedFixtures->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($completedFixtures as $fixture)
                        <div class="bg-[#053d2b]/70 border border-emerald-800/80 rounded-2xl p-5 shadow-lg hover:border-amber-400/50 transition-all flex flex-col justify-between">
                            <div>
                                <!-- Fixture Header -->
                                <div class="flex items-center justify-between text-xs text-emerald-300 pb-3 border-b border-emerald-800/60 mb-3">
                                    <span class="font-semibold text-amber-300">
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
                                            <div class="font-bold text-white text-base flex items-center gap-1.5">
                                                {{ $fixture->team1->name ?? 'Team 1' }}
                                                @if($fixture->result && $fixture->result->winner_team_id === $fixture->team1_id)
                                                    <span class="text-amber-400 text-xs">🏆</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-emerald-300/70">
                                                {{ $fixture->team1->department->name ?? '' }}
                                            </div>
                                        </div>
                                        <div class="text-2xl font-black {{ $fixture->result && $fixture->result->winner_team_id === $fixture->team1_id ? 'text-amber-400' : 'text-white' }}">
                                            {{ $fixture->result->team1_score ?? 0 }}
                                        </div>
                                    </div>

                                    <!-- Team 2 -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 pr-3">
                                            <div class="font-bold text-white text-base flex items-center gap-1.5">
                                                {{ $fixture->team2->name ?? 'Team 2' }}
                                                @if($fixture->result && $fixture->result->winner_team_id === $fixture->team2_id)
                                                    <span class="text-amber-400 text-xs">🏆</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-emerald-300/70">
                                                {{ $fixture->team2->department->name ?? '' }}
                                            </div>
                                        </div>
                                        <div class="text-2xl font-black {{ $fixture->result && $fixture->result->winner_team_id === $fixture->team2_id ? 'text-amber-400' : 'text-white' }}">
                                            {{ $fixture->result->team2_score ?? 0 }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Winner announcement -->
                                <div class="mt-3 pt-3 border-t border-emerald-800/60 flex items-center justify-between text-xs">
                                    <div class="text-emerald-200">
                                        @if($fixture->result && $fixture->result->winnerTeam)
                                            <span class="text-amber-300 font-bold">Winner:</span> {{ $fixture->result->winnerTeam->name }}
                                        @elseif($fixture->result && $fixture->result->winner_team_id === null)
                                            <span class="text-emerald-300 font-semibold">Match Drawn</span>
                                        @endif
                                    </div>

                                    @if($fixture->venue)
                                        <span class="text-emerald-300/70 truncate max-w-[150px]">📍 {{ $fixture->venue->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer action -->
                            <div class="mt-4 pt-3 border-t border-emerald-800/40 flex items-center justify-between">
                                <span class="text-[11px] text-emerald-300/60">
                                    {{ $fixture->photos->count() }} action photos
                                </span>
                                <a href="{{ route('matches.show', $fixture) }}"
                                   class="text-xs font-bold text-amber-400 hover:text-amber-300 inline-flex items-center gap-1">
                                    Match Details & Photos &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-8 text-center">
                    <p class="text-sm text-emerald-200/80">
                        No completed matches found for this sport and edition.
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

