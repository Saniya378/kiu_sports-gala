@extends('layouts.admin')

@section('header_title', 'Points Table')
@section('header_subtitle', 'Dynamically calculated tournament standings (Win = 3 pts, Draw = 1 pt, Loss = 0 pts).')

@section('header_actions')
    <a href="{{ route('admin.score-entry.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
        </svg>
        Enter Scores
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.points-table.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Filter: Edition -->
            <div class="w-full md:w-64">
                <label for="edition_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Select Edition
                </label>
                <select name="edition_id" id="edition_id"
                        onchange="this.form.submit()"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}" {{ $selectedEditionId == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter: Sport -->
            <div class="w-full md:w-64">
                <label for="sport_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Select Sport
                </label>
                <select name="sport_id" id="sport_id"
                        onchange="this.form.submit()"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach ($sports as $sport)
                        <option value="{{ $sport->id }}" {{ $selectedSportId == $sport->id ? 'selected' : '' }}>
                            {{ $sport->name }} ({{ ucfirst($sport->category) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                Apply Selection
            </button>
        </form>
    </div>

    <!-- Standings Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Table Header Context -->
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 bg-slate-50/50">
            <div>
                <h3 class="text-base font-bold text-slate-900">
                    {{ $selectedSport ? $selectedSport->name . ' (' . ucfirst($selectedSport->category) . ')' : 'Sport' }}
                    &mdash;
                    {{ $selectedEdition ? $selectedEdition->title : 'Tournament' }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Calculated live from recorded match results. Not persisted in a separate table.
                </p>
            </div>

            <div class="text-xs text-slate-500 bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-medium shadow-xs">
                Win: <strong class="text-emerald-700">3 pts</strong> &bull;
                Draw: <strong class="text-amber-700">1 pt</strong> &bull;
                Loss: <strong class="text-slate-500">0 pts</strong>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Team</th>
                        <th class="py-3 px-4 text-center w-16">P</th>
                        <th class="py-3 px-4 text-center w-16">W</th>
                        <th class="py-3 px-4 text-center w-16">D</th>
                        <th class="py-3 px-4 text-center w-16">L</th>
                        <th class="py-3 px-4 text-center w-20">GF</th>
                        <th class="py-3 px-4 text-center w-20">GA</th>
                        <th class="py-3 px-4 text-center w-20">GD</th>
                        <th class="py-3 px-4 text-center w-24 bg-indigo-50/50 font-bold text-indigo-900">PTS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse ($standings as $index => $row)
                        @php
                            $rank = $index + 1;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $rank === 1 ? 'bg-amber-50/20' : '' }}">
                            <!-- Rank -->
                            <td class="py-3.5 px-4 text-center font-bold">
                                @if ($rank === 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-900 text-xs font-black shadow-xs">
                                        1
                                    </span>
                                @elseif ($rank === 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-300 text-slate-800 text-xs font-black shadow-xs">
                                        2
                                    </span>
                                @elseif ($rank === 3)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-700/20 text-amber-900 text-xs font-black">
                                        3
                                    </span>
                                @else
                                    <span class="text-slate-500 font-semibold text-xs">{{ $rank }}</span>
                                @endif
                            </td>

                            <!-- Team -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $row['team']->name }}</div>
                                <div class="text-xs text-slate-500">{{ $row['team']->department->name ?? 'General' }}</div>
                            </td>

                            <!-- Played -->
                            <td class="py-3.5 px-4 text-center text-slate-700 font-medium">
                                {{ $row['played'] }}
                            </td>

                            <!-- Won -->
                            <td class="py-3.5 px-4 text-center font-semibold text-emerald-700">
                                {{ $row['won'] }}
                            </td>

                            <!-- Drawn -->
                            <td class="py-3.5 px-4 text-center font-medium text-amber-700">
                                {{ $row['drawn'] }}
                            </td>

                            <!-- Lost -->
                            <td class="py-3.5 px-4 text-center font-medium text-rose-600">
                                {{ $row['lost'] }}
                            </td>

                            <!-- Goals/Points For -->
                            <td class="py-3.5 px-4 text-center text-slate-600">
                                {{ $row['score_for'] }}
                            </td>

                            <!-- Goals/Points Against -->
                            <td class="py-3.5 px-4 text-center text-slate-600">
                                {{ $row['score_against'] }}
                            </td>

                            <!-- Difference -->
                            <td class="py-3.5 px-4 text-center font-semibold {{ $row['score_diff'] > 0 ? 'text-emerald-600' : ($row['score_diff'] < 0 ? 'text-rose-600' : 'text-slate-500') }}">
                                {{ $row['score_diff'] > 0 ? '+' : '' }}{{ $row['score_diff'] }}
                            </td>

                            <!-- Total Points -->
                            <td class="py-3.5 px-4 text-center font-black text-base text-indigo-700 bg-indigo-50/50">
                                {{ $row['points'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-semibold text-slate-900">No teams registered</h4>
                                    <p class="text-xs text-slate-500">
                                        No teams found for the selected edition and sport discipline.
                                    </p>
                                    <a href="{{ route('admin.teams.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                                        Add Teams
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

