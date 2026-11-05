@extends('layouts.public')

@section('title', "Today's Matches | KIU Sports Gala")

@section('content')
<div class="space-y-8">

    <!-- Header & Date Banner (Unified Royal Blue & Athletic Gold) -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-sports font-black uppercase tracking-wider text-amber-300">
                        {{ $runningEdition ? $runningEdition->title : 'No Active Running Gala' }}
                    </span>
                    <span id="today-sync-pill" class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-300 bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-500/30 ml-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        Live Auto-Sync
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black font-sports uppercase tracking-wide text-white">
                    Today's Matches
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 mt-1 font-medium">
                    {{ now()->format('l, F j, Y') }} &bull; Live updates from ground venues
                </p>
            </div>

            <!-- Sport Filter Form -->
            @if ($runningEdition && $sports->count() > 0)
                <div class="self-start sm:self-auto w-full sm:w-auto">
                    <form method="GET" action="{{ route('today') }}" class="flex items-center space-x-2">
                        <label for="sport_id" class="sr-only">Filter by Sport</label>
                        <select name="sport_id" id="sport_id" onchange="this.form.submit()"
                                class="rounded-xl border-blue-800 bg-blue-950 text-amber-300 text-xs font-semibold focus:border-amber-400 focus:ring-amber-400 py-2.5 px-4 shadow-sm">
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
                    <div data-fixture-card="{{ $fixture->id }}" class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)] hover:shadow-[0_12px_28px_-6px_rgba(15,23,42,0.14)] hover:-translate-y-1 transition-all duration-200 overflow-hidden flex flex-col justify-between group">
                        <!-- Premier League / FIFA Header Bar (Unified Athletic Navy/Blue) -->
                        <div class="bg-gradient-to-r from-blue-950 via-indigo-950 to-blue-900 px-4 py-2.5 border-b border-blue-800/80 flex items-center justify-between text-xs text-white">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span class="font-sports font-bold uppercase tracking-wider text-amber-300 text-xs">
                                    {{ $fixture->sport->name }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $fixture->sport->category === 'girls' ? 'bg-pink-500/20 text-pink-300 border border-pink-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' }}">
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

                        <!-- Matchup Body with Athletic Department Crests -->
                        <div class="p-4 sm:p-5 space-y-3.5 bg-gradient-to-b from-white via-white to-slate-50/50">
                            <!-- Team 1 -->
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <!-- Athletic Department Crest Badge -->
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-900 to-indigo-900 border border-blue-700/60 text-amber-400 font-sports font-black flex items-center justify-center text-xs shrink-0 shadow-xs">
                                        {{ $fixture->team1->department->code ?? substr($fixture->team1->name, 0, 3) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-sports font-extrabold text-base text-slate-900 group-hover:text-blue-700 transition-colors truncate uppercase tracking-tight">
                                            {{ $fixture->team1->name }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 font-medium truncate">{{ $fixture->team1->department->name ?? 'Dept' }}</p>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span data-team1-score="{{ $fixture->id }}" class="px-3 py-1 rounded-xl bg-blue-950 text-amber-400 font-sports font-black text-xl min-w-[48px] inline-block text-center shadow-xs tracking-tight border border-blue-800/60 transition-transform duration-300">
                                        {{ $fixture->result ? $fixture->formatTeamScore(1) : '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 flex items-center justify-center -my-1">
                                <span class="bg-slate-100 px-2 py-0.5 rounded-full text-[9px] font-sports font-black uppercase text-slate-400 tracking-widest border border-slate-200">VS</span>
                            </div>

                            <!-- Team 2 -->
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <!-- Athletic Department Crest Badge -->
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-950 to-slate-900 border border-blue-800/60 text-amber-400 font-sports font-black flex items-center justify-center text-xs shrink-0 shadow-xs">
                                        {{ $fixture->team2->department->code ?? substr($fixture->team2->name, 0, 3) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-sports font-extrabold text-base text-slate-900 group-hover:text-blue-700 transition-colors truncate uppercase tracking-tight">
                                            {{ $fixture->team2->name }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 font-medium truncate">{{ $fixture->team2->department->name ?? 'Dept' }}</p>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span data-team2-score="{{ $fixture->id }}" class="px-3 py-1 rounded-xl bg-blue-950 text-amber-400 font-sports font-black text-xl min-w-[48px] inline-block text-center shadow-xs tracking-tight border border-blue-800/60 transition-transform duration-300">
                                        {{ $fixture->result ? $fixture->formatTeamScore(2) : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-slate-100/80 px-4 py-3 border-t border-slate-200/80 flex items-center justify-between text-xs text-slate-600">
                            <div class="truncate flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate font-medium text-[11px]">{{ $fixture->venue->name ?? 'Venue TBD' }}</span>
                            </div>

                            <div class="flex items-center gap-2 shrink-0 ml-3">
                                <a href="{{ $fixture->whatsapp_share_url }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-[#25D366]/10 text-emerald-800 hover:bg-[#25D366] hover:text-slate-950 transition-colors"
                                   title="Share on WhatsApp">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.044c.101-.116.433-.506.549-.68.116-.174.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.392-10.416c-5.523 0-10 4.477-10 10 0 1.765.459 3.424 1.261 4.871l-1.341 4.897 5.024-1.317c1.401.764 3.003 1.196 4.707 1.196 5.523 0 10-4.477 10-10s-4.477-10-10-10z"/></svg>
                                    <span>Share</span>
                                </a>
                                <a href="{{ route('matches.show', $fixture) }}" class="inline-flex items-center gap-1 font-sports font-bold text-indigo-700 hover:text-indigo-900 uppercase tracking-wide text-xs">
                                    <span>Details</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
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
                <a href="{{ route('schedule') }}" class="inline-flex items-center px-4 py-2 bg-blue-900 hover:bg-blue-800 text-amber-300 font-bold text-xs rounded-xl shadow-xs transition-colors">
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
                <a href="{{ route('schedule') }}" class="inline-flex items-center px-4 py-2 bg-blue-900 text-amber-300 font-bold text-xs rounded-xl shadow-sm hover:bg-blue-800 transition-colors">
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
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs shadow-rose-950/40 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    LIVE
                                </span>
                            `;
                        } else if (match.status === 'completed') {
                            badgeContainer.innerHTML = `
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-amber-400 border border-slate-700">
                                    FINAL
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

