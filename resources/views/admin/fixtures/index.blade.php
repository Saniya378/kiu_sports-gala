@extends('layouts.admin')

@section('header_title', 'Fixtures')
@section('header_subtitle', 'Schedule matches, manage game venues, dates, rounds, and live status.')

@section('header_actions')
    <a href="{{ route('admin.fixtures.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Schedule Fixture
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.fixtures.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Filter: Edition -->
            <div class="w-full md:w-56">
                <label for="filter_edition" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Edition
                </label>
                <select name="edition_id" id="filter_edition" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Editions</option>
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}" {{ request('edition_id') == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter: Sport -->
            <div class="w-full md:w-56">
                <label for="filter_sport" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Sport
                </label>
                <select name="sport_id" id="filter_sport" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Sports</option>
                    @foreach ($sports as $sport)
                        <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                            {{ $sport->name }} ({{ ucfirst($sport->category) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter: Date -->
            <div class="w-full md:w-48">
                <label for="filter_date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Match Date
                </label>
                <input type="date" name="date" id="filter_date" value="{{ request('date') }}"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
                    Filter
                </button>

                @if(request()->hasAny(['edition_id', 'sport_id', 'date']))
                    <a href="{{ route('admin.fixtures.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors duration-150">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Fixtures Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">Matchup</th>
                        <th class="py-3.5 px-4">Sport & Edition</th>
                        <th class="py-3.5 px-4">Venue</th>
                        <th class="py-3.5 px-4">Schedule</th>
                        <th class="py-3.5 px-4">Round</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse ($fixtures as $fixture)
                        <tr class="hover:bg-slate-50/50 transition-colors duration-100">
                            <!-- Matchup -->
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-2">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900">{{ $fixture->team1->name ?? 'Team 1' }}</span>
                                        <span class="text-xs text-slate-500">{{ $fixture->team1->department->name ?? 'No Dept' }}</span>
                                    </div>
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 text-xs font-bold uppercase">vs</span>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900">{{ $fixture->team2->name ?? 'Team 2' }}</span>
                                        <span class="text-xs text-slate-500">{{ $fixture->team2->department->name ?? 'No Dept' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Sport & Edition -->
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $fixture->sport->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium {{ ($fixture->sport->category ?? '') === 'girls' ? 'bg-pink-50 text-pink-700' : 'bg-blue-50 text-blue-700' }}">
                                        {{ ucfirst($fixture->sport->category ?? '') }}
                                    </span>
                                    • {{ $fixture->edition->title ?? '-' }}
                                </div>
                            </td>

                            <!-- Venue -->
                            <td class="py-3 px-4 text-slate-600">
                                @if($fixture->venue)
                                    <div class="flex items-center space-x-1.5">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ $fixture->venue->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">TBD</span>
                                @endif
                            </td>

                            <!-- Schedule (Date & Time) -->
                            <td class="py-3 px-4">
                                @if($fixture->match_date)
                                    <div class="font-medium text-slate-800">{{ $fixture->match_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $fixture->match_time ? \Carbon\Carbon::parse($fixture->match_time)->format('g:i A') : 'Time TBD' }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Not scheduled</span>
                                @endif
                            </td>

                            <!-- Round -->
                            <td class="py-3 px-4 text-slate-700">
                                @if($fixture->round)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $fixture->round }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4">
                                @if ($fixture->status === 'live')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Live
                                    </span>
                                @elseif ($fixture->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                        Completed
                                    </span>
                                @elseif ($fixture->status === 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        Scheduled
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center space-x-1">
                                    <a href="{{ route('admin.score-entry.index', ['fixture_id' => $fixture->id]) }}"
                                       class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                       title="Score Entry">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.fixtures.edit', $fixture) }}"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors"
                                       title="Edit Fixture">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.fixtures.destroy', $fixture) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this fixture?');"
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                                title="Delete Fixture">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto text-center space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-slate-900">No fixtures found</h3>
                                    <p class="text-xs text-slate-500">
                                        @if(request()->hasAny(['edition_id', 'sport_id', 'date']))
                                            Try adjusting your filters to find what you're looking for.
                                        @else
                                            Get started by scheduling your first fixture.
                                        @endif
                                    </p>
                                    <a href="{{ route('admin.fixtures.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                                        Schedule Fixture
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($fixtures->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $fixtures->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

