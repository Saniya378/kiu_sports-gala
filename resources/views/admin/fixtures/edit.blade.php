@extends('layouts.admin')

@section('header_title', 'Edit Fixture')
@section('header_subtitle', 'Update matchup details, date, time, venue, or status.')

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
            <h2 class="text-base font-semibold text-slate-900">Edit Fixture</h2>
            <p class="text-xs text-slate-500 mt-1">
                Currently: <strong>{{ $fixture->team1->name ?? 'Team 1' }}</strong> vs <strong>{{ $fixture->team2->name ?? 'Team 2' }}</strong>
            </p>
        </div>

        <form method="POST" action="{{ route('admin.fixtures.update', $fixture) }}" class="p-6 space-y-6" id="fixture-form">
            @csrf
            @method('PUT')

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
                            <option value="{{ $edition->id }}" {{ old('edition_id', $fixture->edition_id) == $edition->id ? 'selected' : '' }}>
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
                            <option value="{{ $sport->id }}" {{ old('sport_id', $fixture->sport_id) == $sport->id ? 'selected' : '' }}>
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
                                        {{ old('team1_id', $fixture->team1_id) == $team->id ? 'selected' : '' }}>
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
                                        {{ old('team2_id', $fixture->team2_id) == $team->id ? 'selected' : '' }}>
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
                            <option value="{{ $venue->id }}" {{ old('venue_id', $fixture->venue_id) == $venue->id ? 'selected' : '' }}>
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
                           value="{{ old('round', $fixture->round) }}"
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
                           value="{{ old('match_date', $fixture->match_date ? $fixture->match_date->format('Y-m-d') : '') }}"
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
                           value="{{ old('match_time', $fixture->match_time ? substr($fixture->match_time, 0, 5) : '') }}"
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
                        <option value="scheduled" {{ old('status', $fixture->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="live" {{ old('status', $fixture->status) === 'live' ? 'selected' : '' }}>Live</option>
                        <option value="completed" {{ old('status', $fixture->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $fixture->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                               value="{{ old('stream_url', $fixture->stream_url) }}"
                               class="w-full pl-10 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('stream_url') border-rose-300 @enderror">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Supports YouTube Live, YouTube video links, or Facebook Live streams. Embed player will automatically appear on the public match page.</p>
                    @if($fixture->stream_url)
                        <div class="mt-2 flex items-center gap-2 text-xs text-indigo-600">
                            <span>Detected Platform: <strong>{{ ucfirst($fixture->stream_platform ?? 'Stream') }}</strong></span>
                            <span>&bull;</span>
                            <a href="{{ $fixture->stream_url }}" target="_blank" class="underline hover:text-indigo-800">Test open stream link &rarr;</a>
                        </div>
                    @endif
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
                    Update Fixture
                </button>
            </div>
        </form>
    </div>

    <!-- Match Photos Section -->
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-base font-bold text-slate-900">Match Photos</h3>
                <p class="text-xs text-slate-500 mt-0.5">Upload multiple match highlights or action photos (automatically resized to max width 1200px).</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-200 text-slate-700 self-start sm:self-auto">
                {{ $fixture->photos->count() }} Photo(s)
            </span>
        </div>

        <div class="p-6 space-y-6">
            <!-- Upload Form -->
            <form method="POST" action="{{ route('admin.fixtures.photos.store', $fixture) }}" enctype="multipart/form-data" class="bg-indigo-50/30 border border-indigo-100 rounded-xl p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <!-- Multiple Photos Input -->
                    <div class="md:col-span-2">
                        <label for="photos" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Select Photos (Multiple allowed) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" name="photos[]" id="photos" multiple required accept="image/*"
                               class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer border border-slate-300 rounded-lg p-1 bg-white focus:outline-none focus:border-indigo-500">
                        @error('photos')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('photos.*')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Button -->
                    <div>
                        <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition-colors flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Upload Photos</span>
                        </button>
                    </div>
                </div>

                <!-- Caption Input -->
                <div>
                    <label for="caption" class="block text-xs font-semibold text-slate-600 mb-1">
                        Caption <span class="text-slate-400 font-normal">(optional, applied to this batch)</span>
                    </label>
                    <input type="text" name="caption" id="caption" placeholder="e.g. Winning celebration, Second half kickoff"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                </div>
            </form>

            <!-- Photos Gallery -->
            @if ($fixture->photos->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($fixture->photos as $photo)
                        <div class="group relative bg-slate-100 rounded-xl overflow-hidden border border-slate-200 flex flex-col justify-between shadow-xs">
                            <div class="aspect-video w-full overflow-hidden bg-slate-900">
                                <a href="{{ $photo->image_url }}" target="_blank" title="View Full Image">
                                    <img src="{{ $photo->image_url }}" alt="{{ $photo->caption ?? 'Match Photo' }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                </a>
                            </div>

                            <div class="p-3 bg-white flex flex-col justify-between flex-1">
                                <div>
                                    @if ($photo->caption)
                                        <p class="text-xs font-medium text-slate-800 line-clamp-2">{{ $photo->caption }}</p>
                                    @else
                                        <p class="text-xs text-slate-400 italic">No caption</p>
                                    @endif
                                    <div class="text-[11px] text-slate-400 mt-1">
                                        By {{ $photo->uploader?->name ?? 'User' }} &bull; {{ $photo->created_at->format('M d, H:i') }}
                                    </div>
                                </div>

                                <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between">
                                    <a href="{{ $photo->image_url }}" target="_blank" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium">
                                        View full &rarr;
                                    </a>

                                    <form method="POST" action="{{ route('admin.fixtures.photos.destroy', $photo) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this photo?');"
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-semibold" title="Delete Photo">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-xs text-slate-500">
                    No match photos uploaded for this fixture yet.
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editionSelect = document.getElementById('edition_id');
        const sportSelect = document.getElementById('sport_id');
        const team1Select = document.getElementById('team1_id');
        const team2Select = document.getElementById('team2_id');
        const countIndicator = document.getElementById('teams-count-indicator');

        function filterTeams(isInitial = false) {
            const selectedEdition = editionSelect.value;
            const selectedSport = sportSelect.value;

            [team1Select, team2Select].forEach(select => {
                const currentVal = select.value;

                Array.from(select.options).forEach((option, index) => {
                    if (index === 0) return; // Skip placeholder option

                    const optEdition = option.getAttribute('data-edition-id');
                    const optSport = option.getAttribute('data-sport-id');

                    const matches = (!selectedEdition || optEdition === selectedEdition) &&
                                    (!selectedSport || optSport === selectedSport);

                    if (matches) {
                        option.hidden = false;
                        option.disabled = false;
                    } else {
                        option.hidden = true;
                        option.disabled = true;
                        if (!isInitial && option.value === currentVal) {
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

        editionSelect.addEventListener('change', () => filterTeams(false));
        sportSelect.addEventListener('change', () => filterTeams(false));

        // Initial setup preserving pre-selected value
        filterTeams(true);
    });
</script>
@endsection

