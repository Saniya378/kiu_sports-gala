@extends('layouts.public')

@section('title', "Today's Matches | KIU Sports Gala")

@section('content')
<div class="space-y-8">

    <!-- Header & Date Banner -->
    <div class="bg-gradient-to-r from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400/80 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-300">
                        {{ $runningEdition ? $runningEdition->title : 'No Active Running Gala' }}
                    </span>
                    <span id="today-sync-pill" class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-300 bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-500/30 ml-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        Live Auto-Sync
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                    Today's Matches
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    {{ now()->format('l, F j, Y') }} &bull; Live updates from ground venues
                </p>
            </div>

            <!-- Sport Filter Form -->
            @if ($runningEdition && $sports->count() > 0)
                <div class="self-start sm:self-auto w-full sm:w-auto">
                    <form method="GET" action="{{ route('today') }}" class="flex items-center space-x-2">
                        <label for="sport_id" class="sr-only">Filter by Sport</label>
                        <select name="sport_id" id="sport_id" onchange="this.form.submit()"
                                class="rounded-xl border-slate-700 bg-slate-900/90 text-amber-300 text-xs font-semibold focus:border-amber-400 focus:ring-amber-400 py-2.5 px-4 shadow-sm">
                            <option value="">All Sports (Today)</option>
                            @foreach ($sports as $sport)
                                <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                                    {{ $sport->name }} ({{ ucfirst($sport->category) }})
                                </option>
                            @endforeach
                        </select>
                        @if(request('sport_id'))
                            <a href="{{ route('today') }}" class="p-2.5 text-xs text-slate-300 hover:text-white bg-white/10 hover:bg-white/20 rounded-xl transition-colors" title="Reset filter">
                                &times;
                            </a>
                        @endif
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Match Cards Grid -->
    @if ($runningEdition)
        @if ($fixtures->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($fixtures as $fixture)
                    <div data-fixture-card="{{ $fixture->id }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col justify-between group">
                        <!-- Card Header -->
                        <div class="bg-slate-50 p-4 border-b border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-slate-800">{{ $fixture->sport->name }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $fixture->sport->category === 'girls' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst($fixture->sport->category) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($fixture->stream_url)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200" title="Live Video Stream available">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-pulse"></span>
                                        Stream
                                    </span>
                                @endif

                                <div data-status-badge="{{ $fixture->id }}">
                                @if ($fixture->status === 'live')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Live
                                    </span>
                                @elseif ($fixture->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-200 text-slate-700">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700">
                                        {{ $fixture->match_time ? \Carbon\Carbon::parse($fixture->match_time)->format('g:i A') : 'Scheduled' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Matchup Body -->
                        <div class="p-5 space-y-4">
                            <!-- Team 1 -->
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col min-w-0 pr-2">
                                    <span class="font-bold text-base text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                        {{ $fixture->team1->name }}
                                    </span>
                                    <span class="text-xs text-slate-500 truncate">{{ $fixture->team1->department->name ?? 'Dept' }}</span>
                                </div>
                                <span data-team1-score="{{ $fixture->id }}" class="text-2xl font-black text-slate-900 font-mono transition-transform duration-300">
                                    {{ $fixture->result ? $fixture->result->team1_score : '-' }}
                                </span>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- Team 2 -->
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col min-w-0 pr-2">
                                    <span class="font-bold text-base text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                        {{ $fixture->team2->name }}
                                    </span>
                                    <span class="text-xs text-slate-500 truncate">{{ $fixture->team2->department->name ?? 'Dept' }}</span>
                                </div>
                                <span data-team2-score="{{ $fixture->id }}" class="text-2xl font-black text-slate-900 font-mono transition-transform duration-300">
                                    {{ $fixture->result ? $fixture->result->team2_score : '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-slate-50/80 p-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <div class="truncate flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">{{ $fixture->venue->name ?? 'Venue TBD' }}</span>
                            </div>

                            <a href="{{ route('matches.show', $fixture) }}" class="font-bold text-blue-600 hover:text-blue-800 transition-colors shrink-0 ml-3">
                                Match details &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-md mx-auto shadow-sm">
                <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">No Matches Scheduled for Today</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">
                    There are no games scheduled for {{ now()->format('M d, Y') }}. Check the full schedule for upcoming dates.
                </p>
                <a href="{{ route('schedule') }}" class="inline-flex items-center px-4 py-2 bg-[#0a1226] hover:bg-[#060b18] text-amber-300 font-bold text-xs rounded-xl shadow-xs transition-colors">
                    View Full Schedule
                </a>
            </div>
        @endif
    @else
        <!-- No running edition -->
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-lg mx-auto shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-full bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">No Running Sports Gala Active</h3>
            <p class="text-xs text-slate-500 mt-1 mb-5">
                Today's live matches are only active during an ongoing tournament edition. You can browse the complete schedule or view past results below.
            </p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('schedule') }}" class="inline-flex items-center px-4 py-2 bg-[#0a1226] text-amber-300 font-bold text-xs rounded-xl shadow-sm hover:bg-[#060b18] transition-colors">
                    Tournament Schedule
                </a>
                <a href="{{ route('results') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs rounded-xl transition-colors">
                    Browse Results
                </a>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    (function() {
        const endpoint = "{{ route('api.live-scores.index') }}";

        function flashUpdate(el) {
            if (!el) return;
            el.classList.add('scale-125', 'text-amber-500', 'font-black');
            setTimeout(() => {
                el.classList.remove('scale-125', 'text-amber-500', 'font-black');
            }, 600);
        }

        async function pollTodayScores() {
            try {
                const response = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) return;

                const data = await response.json();

                if (!data.matches || !Array.isArray(data.matches)) return;

                data.matches.forEach(match => {
                    // Update Score 1
                    const score1El = document.querySelector(`[data-team1-score="${match.id}"]`);
                    if (score1El) {
                        const newScore1 = match.team1_score !== null ? String(match.team1_score) : '-';
                        if (score1El.textContent.trim() !== newScore1) {
                            score1El.textContent = newScore1;
                            flashUpdate(score1El);
                        }
                    }

                    // Update Score 2
                    const score2El = document.querySelector(`[data-team2-score="${match.id}"]`);
                    if (score2El) {
                        const newScore2 = match.team2_score !== null ? String(match.team2_score) : '-';
                        if (score2El.textContent.trim() !== newScore2) {
                            score2El.textContent = newScore2;
                            flashUpdate(score2El);
                        }
                    }

                    // Update Status Badge
                    const badgeContainer = document.querySelector(`[data-status-badge="${match.id}"]`);
                    if (badgeContainer) {
                        if (match.status === 'live') {
                            badgeContainer.innerHTML = `
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-600 animate-pulse"></span>
                                    Live
                                </span>
                            `;
                        } else if (match.status === 'completed') {
                            badgeContainer.innerHTML = `
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-200 text-slate-700">
                                    Completed
                                </span>
                            `;
                        }
                    }
                });
            } catch (err) {
                console.error("Today matches poll error:", err);
            }
        }

        // Poll every 8 seconds
        setInterval(pollTodayScores, 8000);
    })();
</script>
@endpush
@endsection

