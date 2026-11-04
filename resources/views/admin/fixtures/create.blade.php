@extends('layouts.admin')

@section('header_title', 'Schedule Fixture')
@section('header_subtitle', 'Set up a new matchup between two teams for a specific edition and sport.')

@section('header_actions')
    <a href="{{ route('admin.fixtures.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Fixtures
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-base font-semibold text-slate-900">Fixture Details</h2>
            <p class="text-xs text-slate-500 mt-1">Configure the tournament context, participating teams, schedule, and venue.</p>
        </div>

        <form method="POST" action="{{ route('admin.fixtures.store') }}" class="p-6 space-y-6" id="fixture-form">
            @csrf

            <!-- Section 1: Edition & Sport Context -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                <!-- Edition -->
                <div>
                    <label for="edition_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Edition <span class="text-rose-500">*</span>
                    </label>
                    <select name="edition_id" id="edition_id" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('edition_id') border-rose-300 @enderror">
                        <option value="">Select Edition</option>
                        @foreach ($editions as $edition)
                            <option value="{{ $edition->id }}" {{ old('edition_id', request('edition_id')) == $edition->id ? 'selected' : '' }}>
                                {{ $edition->title }} ({{ $edition->year }})
                            </option>
                        @endforeach
                    </select>
                    @error('edition_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sport -->
                <div>
                    <label for="sport_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Sport Discipline <span class="text-rose-500">*</span>
                    </label>
                    <select name="sport_id" id="sport_id" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('sport_id') border-rose-300 @enderror">
                        <option value="">Select Sport</option>
                        @foreach ($sports as $sport)
                            <option value="{{ $sport->id }}" {{ old('sport_id', request('sport_id')) == $sport->id ? 'selected' : '' }}>
                                {{ $sport->name }} ({{ ucfirst($sport->category) }})
                            </option>
                        @endforeach
                    </select>
                    @error('sport_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 2: Teams Selection -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-slate-800">Participating Teams</h3>
                    <span id="teams-count-indicator" class="text-xs text-slate-500"></span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Team 1 -->
                    <div>
                        <label for="team1_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Team 1 (Home) <span class="text-rose-500">*</span>
                        </label>
                        <select name="team1_id" id="team1_id" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('team1_id') border-rose-300 @enderror">
                            <option value="">Select Team 1</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}"
                                        data-edition-id="{{ $team->edition_id }}"
                                        data-sport-id="{{ $team->sport_id }}"
                                        {{ old('team1_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }} ({{ $team->department->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('team1_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Team 2 -->
                    <div>
                        <label for="team2_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Team 2 (Away) <span class="text-rose-500">*</span>
                        </label>
                        <select name="team2_id" id="team2_id" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('team2_id') border-rose-300 @enderror">
                            <option value="">Select Team 2</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}"
                                        data-edition-id="{{ $team->edition_id }}"
                                        data-sport-id="{{ $team->sport_id }}"
                                        {{ old('team2_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }} ({{ $team->department->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('team2_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Schedule, Venue, Round & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <!-- Venue -->
                <div>
                    <label for="venue_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Venue
                    </label>
                    <select name="venue_id" id="venue_id"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('venue_id') border-rose-300 @enderror">
                        <option value="">No venue assigned (TBD)</option>
                        @foreach ($venues as $venue)
                            <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>
                                {{ $venue->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('venue_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Round -->
                <div>
                    <label for="round" class="block text-sm font-medium text-slate-700 mb-1">
                        Round / Stage
                    </label>
                    <input type="text" name="round" id="round" list="round-suggestions"
                           placeholder="e.g. Group Stage, Quarter Final, Final"
                           value="{{ old('round') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('round') border-rose-300 @enderror">
                    <datalist id="round-suggestions">
                        <option value="Group Stage">
                        <option value="Round of 16">
                        <option value="Quarter Final">
                        <option value="Semi Final">
                        <option value="3rd Place Playoff">
                        <option value="Final">
                    </datalist>
                    @error('round')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Match Date -->
                <div>
                    <label for="match_date" class="block text-sm font-medium text-slate-700 mb-1">
                        Match Date
                    </label>
                    <input type="date" name="match_date" id="match_date"
                           value="{{ old('match_date') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('match_date') border-rose-300 @enderror">
                    @error('match_date')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Match Time -->
                <div>
                    <label for="match_time" class="block text-sm font-medium text-slate-700 mb-1">
                        Match Time
                    </label>
                    <input type="time" name="match_time" id="match_time"
                           value="{{ old('match_time') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('match_time') border-rose-300 @enderror">
                    @error('match_time')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1">
                        Match Status <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-rose-300 @enderror">
                        <option value="scheduled" {{ old('status', 'scheduled') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="live" {{ old('status') === 'live' ? 'selected' : '' }}>Live</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Live Video Stream URL -->
                <div class="md:col-span-2">
                    <label for="stream_url" class="block text-sm font-medium text-slate-700 mb-1">
                        Live Video Stream URL (Optional)
                    </label>
                    <div class="relative rounded-lg shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            🎥
                        </div>
                        <input type="url" name="stream_url" id="stream_url"
                               placeholder="https://www.youtube.com/watch?v=... or Facebook Live / Video URL"
                               value="{{ old('stream_url') }}"
                               class="w-full pl-10 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('stream_url') border-rose-300 @enderror">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Supports YouTube Live, YouTube video links, or Facebook Live streams. Embed player will automatically appear on the public match page.</p>
                    @error('stream_url')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.fixtures.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    Save Fixture
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editionSelect = document.getElementById('edition_id');
        const sportSelect = document.getElementById('sport_id');
        const team1Select = document.getElementById('team1_id');
        const team2Select = document.getElementById('team2_id');
        const countIndicator = document.getElementById('teams-count-indicator');

        function filterTeams() {
            const selectedEdition = editionSelect.value;
            const selectedSport = sportSelect.value;

            [team1Select, team2Select].forEach(select => {
                const currentVal = select.value;
                let visibleCount = 0;

                Array.from(select.options).forEach((option, index) => {
                    if (index === 0) return; // Skip placeholder option

                    const optEdition = option.getAttribute('data-edition-id');
                    const optSport = option.getAttribute('data-sport-id');

                    const matches = (!selectedEdition || optEdition === selectedEdition) &&
                                    (!selectedSport || optSport === selectedSport);

                    if (matches) {
                        option.hidden = false;
                        option.disabled = false;
                        visibleCount++;
                    } else {
                        option.hidden = true;
                        option.disabled = true;
                        if (option.value === currentVal) {
                            select.value = '';
                        }
                    }
                });
            });

            if (selectedEdition && selectedSport) {
                const visibleCount = Array.from(team1Select.options).filter(o => !o.hidden && o.value).length;
                countIndicator.textContent = `${visibleCount} available team(s) match selected edition & sport`;
            } else {
                countIndicator.textContent = 'Select edition and sport to filter available teams';
            }
        }

        editionSelect.addEventListener('change', filterTeams);
        sportSelect.addEventListener('change', filterTeams);

        // Run on page load if edition/sport are prefilled
        filterTeams();
    });
</script>
@endsection

