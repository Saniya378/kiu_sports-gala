@extends('layouts.admin')

@section('header_title', 'Score Entry')
@section('header_subtitle', 'Record match results, auto-calculate winners, and update fixture status.')

@section('content')
<div class="space-y-6">

    <!-- Step 1: Fixture Picker & Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Filter: Edition -->
            <div class="w-full md:w-52">
                <label for="filter_edition" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Edition
                </label>
                <select id="filter_edition"
                        onchange="updateFilters()"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Editions</option>
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}" {{ request('edition_id') == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter: Sport -->
            <div class="w-full md:w-52">
                <label for="filter_sport" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Sport
                </label>
                <select id="filter_sport"
                        onchange="updateFilters()"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Sports</option>
                    @foreach ($sports as $sport)
                        <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                            {{ $sport->name }} ({{ ucfirst($sport->category) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Select Fixture -->
            <div class="w-full md:flex-1">
                <label for="fixture_picker" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Select Fixture to Score <span class="text-rose-500">*</span>
                </label>
                <select id="fixture_picker"
                        onchange="selectFixture(this.value)"
                        class="w-full rounded-lg border-indigo-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 font-medium text-slate-900 bg-indigo-50/20">
                    <option value="">-- Choose a Matchup --</option>
                    @foreach ($fixtures as $fix)
                        <option value="{{ $fix->id }}" {{ (old('fixture_id', $selectedFixture?->id) == $fix->id) ? 'selected' : '' }}>
                            [{{ strtoupper($fix->status) }}]
                            {{ $fix->team1->name ?? 'Team 1' }} vs {{ $fix->team2->name ?? 'Team 2' }}
                            — {{ $fix->sport->name ?? 'Sport' }}
                            @if($fix->match_date) ({{ $fix->match_date->format('M d') }}) @endif
                            @if($fix->result) • Scored: {{ $fix->result->team1_score }}-{{ $fix->result->team2_score }} @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if ($selectedFixture)
        <!-- Step 2: Score Entry Workspace -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Match Header Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 text-white">
                <div class="flex flex-wrap items-center justify-between gap-3 text-xs mb-3">
                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-1 rounded-md bg-white/10 backdrop-blur font-medium text-indigo-200">
                            {{ $selectedFixture->edition->title ?? 'Edition' }}
                        </span>
                        <span class="px-2.5 py-1 rounded-md bg-indigo-500/20 text-indigo-300 font-medium">
                            {{ $selectedFixture->sport->name ?? 'Sport' }} ({{ ucfirst($selectedFixture->sport->category ?? '') }})
                        </span>
                        @if($selectedFixture->round)
                            <span class="px-2.5 py-1 rounded-md bg-amber-500/20 text-amber-300 font-medium">
                                {{ $selectedFixture->round }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.score-entry.pad', $selectedFixture) }}"
                           class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black bg-amber-400 text-slate-950 hover:bg-amber-300 shadow-sm transition-all">
                            ⚡ Quick Scorer Pad
                        </a>

                        @if ($selectedFixture->status === 'live')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                <span class="w-2 h-2 mr-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Match is Live
                            </span>
                        @elseif ($selectedFixture->status === 'completed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-500/20 text-slate-300 border border-slate-500/40">
                                Match Completed
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/40">
                                Scheduled
                            </span>
                        @endif
                    </div>
                </div>

                <div class="text-xs text-slate-400 flex items-center space-x-4">
                    @if($selectedFixture->venue)
                        <span class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $selectedFixture->venue->name }}
                        </span>
                    @endif
                    @if($selectedFixture->match_date)
                        <span class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $selectedFixture->match_date->format('l, M d, Y') }}
                            @if($selectedFixture->match_time)
                                &bull; {{ \Carbon\Carbon::parse($selectedFixture->match_time)->format('g:i A') }}
                            @endif
                        </span>
                    @endif
                </div>
            </div>

            <!-- Score Form -->
            <form method="POST" action="{{ route('admin.score-entry.store') }}" class="p-6 md:p-8 space-y-8" id="score-form">
                @csrf
                <input type="hidden" name="fixture_id" value="{{ $selectedFixture->id }}">

                <!-- Teams Scorecard -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-center">
                    <!-- Team 1 Card -->
                    <div class="md:col-span-2 bg-slate-50 border border-slate-200 rounded-xl p-5 text-center flex flex-col items-center justify-between">
                        <div>
                            <span class="inline-block px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 rounded-full mb-2">
                                Team 1 (Home)
                            </span>
                            <h3 class="text-xl font-bold text-slate-900">{{ $selectedFixture->team1->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $selectedFixture->team1->department->name ?? 'General' }}</p>
                        </div>

                        <div class="mt-5 w-full max-w-[140px]">
                            <label for="team1_score" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Score / Points
                            </label>
                            <input type="number" min="0" name="team1_score" id="team1_score" required
                                   value="{{ old('team1_score', $selectedFixture->result?->team1_score ?? 0) }}"
                                   class="w-full text-center text-3xl font-black py-3 rounded-xl border-slate-300 focus:border-indigo-600 focus:ring-indigo-600 shadow-sm @error('team1_score') border-rose-500 @enderror">
                            @error('team1_score')
                                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- VS / Live Winner Centerpiece -->
                    <div class="md:col-span-1 flex flex-col items-center justify-center text-center space-y-2">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 font-extrabold text-sm border border-slate-200">
                            VS
                        </div>
                        <div id="live-outcome-pill" class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                            Calculating...
                        </div>
                    </div>

                    <!-- Team 2 Card -->
                    <div class="md:col-span-2 bg-slate-50 border border-slate-200 rounded-xl p-5 text-center flex flex-col items-center justify-between">
                        <div>
                            <span class="inline-block px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-slate-200 text-slate-700 rounded-full mb-2">
                                Team 2 (Away)
                            </span>
                            <h3 class="text-xl font-bold text-slate-900">{{ $selectedFixture->team2->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $selectedFixture->team2->department->name ?? 'General' }}</p>
                        </div>

                        <div class="mt-5 w-full max-w-[140px]">
                            <label for="team2_score" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Score / Points
                            </label>
                            <input type="number" min="0" name="team2_score" id="team2_score" required
                                   value="{{ old('team2_score', $selectedFixture->result?->team2_score ?? 0) }}"
                                   class="w-full text-center text-3xl font-black py-3 rounded-xl border-slate-300 focus:border-indigo-600 focus:ring-indigo-600 shadow-sm @error('team2_score') border-rose-500 @enderror">
                            @error('team2_score')
                                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Player of the Match (Optional MVP) -->
                <div>
                    <label for="player_of_the_match_id" class="block text-sm font-semibold text-slate-700 mb-1">
                        ⭐ Player of the Match (MVP) <span class="text-slate-400 font-normal text-xs">(optional)</span>
                    </label>
                    <select name="player_of_the_match_id" id="player_of_the_match_id"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">None / Not awarded yet</option>

                        @if($selectedFixture->team1 && $selectedFixture->team1->players && $selectedFixture->team1->players->isNotEmpty())
                            <optgroup label="{{ $selectedFixture->team1->name }} ({{ $selectedFixture->team1->department->name ?? '' }})">
                                @foreach($selectedFixture->team1->players as $p)
                                    <option value="{{ $p->id }}" {{ old('player_of_the_match_id', $selectedFixture->result?->player_of_the_match_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} @if($p->reg_no) ({{ $p->reg_no }}) @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($selectedFixture->team2 && $selectedFixture->team2->players && $selectedFixture->team2->players->isNotEmpty())
                            <optgroup label="{{ $selectedFixture->team2->name }} ({{ $selectedFixture->team2->department->name ?? '' }})">
                                @foreach($selectedFixture->team2->players as $p)
                                    <option value="{{ $p->id }}" {{ old('player_of_the_match_id', $selectedFixture->result?->player_of_the_match_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} @if($p->reg_no) ({{ $p->reg_no }}) @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    @error('player_of_the_match_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Match Summary / Commentary -->
                <div>
                    <label for="summary" class="block text-sm font-semibold text-slate-700 mb-1">
                        Match Summary & Notes <span class="text-slate-400 font-normal text-xs">(optional)</span>
                    </label>
                    <textarea name="summary" id="summary" rows="3"
                              placeholder="e.g. Won by 4 wickets / 2 goals in extra time. Star player remarks, highlights..."
                              class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('summary', $selectedFixture->result?->summary) }}</textarea>
                    @error('summary')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Live Video Stream Broadcast URL -->
                <div>
                    <label for="stream_url" class="block text-sm font-semibold text-slate-700 mb-1">
                        🎥 Live Video Stream URL <span class="text-slate-400 font-normal text-xs">(YouTube Live / Facebook Live)</span>
                    </label>
                    <input type="url" name="stream_url" id="stream_url"
                           value="{{ old('stream_url', $selectedFixture->stream_url) }}"
                           placeholder="https://www.youtube.com/watch?v=... or Facebook Live URL"
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <p class="text-xs text-slate-500 mt-1">If streaming live from ground, paste URL to embed video player on public match page.</p>
                    @error('stream_url')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Match Status Option (Live vs Completed) -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Match Status
                    </label>
                    <div class="flex flex-wrap items-center gap-6 text-sm font-medium text-slate-700">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="live" {{ old('status', $selectedFixture->status) === 'live' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                                Live (In Progress)
                            </span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="completed" {{ old('status', $selectedFixture->status) === 'completed' || old('status', $selectedFixture->status) === 'scheduled' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-800">
                                ✓ Completed (Final Score)
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button & Auto-completion Notice -->
                <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center text-xs text-slate-500">
                        <svg class="w-4 h-4 text-emerald-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Live scores sync in real-time to public viewers without page reloads.</span>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors duration-150 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Save Score</span>
                    </button>
                </div>
            </form>

            <!-- Match Photos & Gallery for Selected Fixture -->
            <div class="border-t border-slate-200 p-6 md:p-8 bg-slate-50/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Match Photos</h4>
                        <p class="text-xs text-slate-500">Upload multiple photos from this match (auto-scaled to max 1200px width).</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-200 text-slate-700 self-start sm:self-auto">
                        {{ $selectedFixture->photos->count() }} Photo(s)
                    </span>
                </div>

                <!-- Photo Upload Form -->
                <form method="POST" action="{{ route('admin.fixtures.photos.store', $selectedFixture) }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-xl p-4 space-y-4 mb-6 shadow-xs">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label for="photos_scorer" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Add Photos <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" name="photos[]" id="photos_scorer" multiple required accept="image/*"
                                   class="w-full text-sm text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer border border-slate-300 rounded-lg p-1 bg-white">
                        </div>

                        <div>
                            <button type="submit" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors flex items-center justify-center space-x-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Upload Photos</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <input type="text" name="caption" placeholder="Optional caption for this batch of photos..."
                               class="w-full rounded-lg border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </form>

                <!-- Photos Gallery Grid -->
                @if ($selectedFixture->photos->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach ($selectedFixture->photos as $photo)
                            <div class="group bg-white rounded-xl overflow-hidden border border-slate-200 flex flex-col justify-between shadow-xs">
                                <div class="aspect-video w-full overflow-hidden bg-slate-900">
                                    <a href="{{ $photo->image_url }}" target="_blank">
                                        <img src="{{ $photo->image_url }}" alt="{{ $photo->caption ?? 'Match Photo' }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                    </a>
                                </div>
                                <div class="p-2.5 flex flex-col justify-between flex-1">
                                    <p class="text-xs font-medium text-slate-800 line-clamp-1">{{ $photo->caption ?? 'Match photo' }}</p>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        {{ $photo->uploader?->name ?? 'User' }} &bull; {{ $photo->created_at->format('M d, H:i') }}
                                    </div>
                                    <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                        <a href="{{ $photo->image_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                                        <form method="POST" action="{{ route('admin.fixtures.photos.destroy', $photo) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this photo?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-500 italic">No photos uploaded for this match yet.</p>
                @endif
            </div>
        </div>
    @else
        <!-- No fixture selected prompt -->
        <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-8 text-center max-w-lg mx-auto">
            <div class="w-14 h-14 mx-auto rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Choose a Matchup to Enter Scores</h3>
            <p class="text-xs text-slate-600 mt-1 mb-4">
                Use the dropdown above or select from the list of fixtures below to record scores and declare the winner.
            </p>
        </div>
    @endif

    <!-- All Fixtures Overview Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Fixtures Roster</h3>
                <p class="text-xs text-slate-500">Pick any fixture below to view or update its score.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                {{ $fixtures->count() }} Matchup(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">Matchup</th>
                        <th class="py-3 px-4">Sport & Edition</th>
                        <th class="py-3 px-4">Venue & Date</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Result / Winner</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse ($fixtures as $fix)
                        <tr class="hover:bg-slate-50/60 transition-colors {{ $selectedFixture?->id == $fix->id ? 'bg-indigo-50/40' : '' }}">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $fix->team1->name }} vs {{ $fix->team2->name }}</div>
                                <div class="text-xs text-slate-500">{{ $fix->round ?? 'Match' }}</div>
                            </td>

                            <td class="py-3 px-4">
                                <div class="text-slate-800 font-medium">{{ $fix->sport->name }}</div>
                                <div class="text-xs text-slate-500">{{ $fix->edition->title }}</div>
                            </td>

                            <td class="py-3 px-4 text-xs text-slate-600">
                                <div>{{ $fix->venue->name ?? 'Venue TBD' }}</div>
                                <div class="text-slate-400">{{ $fix->match_date ? $fix->match_date->format('M d, Y') : 'Date TBD' }}</div>
                            </td>

                            <td class="py-3 px-4">
                                @if ($fix->status === 'live')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Live
                                    </span>
                                @elseif ($fix->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                        Scheduled
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4">
                                @if ($fix->result)
                                    <div class="font-bold text-slate-900">
                                        {{ $fix->result->team1_score }} - {{ $fix->result->team2_score }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        @if($fix->result->winner_team_id)
                                            Winner: <span class="text-emerald-700 font-semibold">{{ $fix->result->winnerTeam->name ?? 'Winner' }}</span>
                                        @else
                                            <span class="text-slate-500 italic">Draw</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Not entered</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.score-entry.index', array_merge(request()->query(), ['fixture_id' => $fix->id])) }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $selectedFixture?->id == $fix->id ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    {{ $fix->result ? 'Edit Score' : 'Score Entry' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-slate-500">
                                No fixtures found for the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function selectFixture(fixtureId) {
        const url = new URL(window.location.href);
        if (fixtureId) {
            url.searchParams.set('fixture_id', fixtureId);
        } else {
            url.searchParams.delete('fixture_id');
        }
        window.location.href = url.toString();
    }

    function updateFilters() {
        const url = new URL(window.location.href);
        const ed = document.getElementById('filter_edition').value;
        const sp = document.getElementById('filter_sport').value;

        if (ed) url.searchParams.set('edition_id', ed);
        else url.searchParams.delete('edition_id');

        if (sp) url.searchParams.set('sport_id', sp);
        else url.searchParams.delete('sport_id');

        url.searchParams.delete('fixture_id'); // reset selection on filter change
        window.location.href = url.toString();
    }

    @if ($selectedFixture)
        const team1Name = @json($selectedFixture->team1->name);
        const team2Name = @json($selectedFixture->team2->name);
        const team1Input = document.getElementById('team1_score');
        const team2Input = document.getElementById('team2_score');
        const outcomePill = document.getElementById('live-outcome-pill');

        function updateLiveOutcome() {
            const s1 = parseInt(team1Input.value, 10);
            const s2 = parseInt(team2Input.value, 10);

            if (isNaN(s1) || isNaN(s2)) {
                outcomePill.textContent = 'Enter scores';
                outcomePill.className = 'text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600';
                return;
            }

            if (s1 > s2) {
                outcomePill.textContent = `${team1Name} leading / Winner`;
                outcomePill.className = 'text-xs font-semibold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800';
            } else if (s2 > s1) {
                outcomePill.textContent = `${team2Name} leading / Winner`;
                outcomePill.className = 'text-xs font-semibold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800';
            } else {
                outcomePill.textContent = 'Draw';
                outcomePill.className = 'text-xs font-semibold px-3 py-1 rounded-full bg-amber-100 text-amber-800';
            }
        }

        team1Input.addEventListener('input', updateLiveOutcome);
        team2Input.addEventListener('input', updateLiveOutcome);
        updateLiveOutcome();
    @endif
</script>
@endsection

