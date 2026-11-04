@extends('layouts.public')

@section('title', 'KIU Sports Gala | Official Portal & Live Coverage')

@section('hero')
    @if ($latestEdition)
        <div class="relative bg-gradient-to-br from-[#060b18] via-[#0c1630] to-[#0a1226] text-white overflow-hidden border-b-2 border-amber-400 shadow-xl">
            @if ($latestEdition->banner)
                <div class="absolute inset-0 opacity-20 bg-cover bg-center mix-blend-overlay"
                     style="background-image: url('{{ $latestEdition->banner_url }}');"></div>
            @endif

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                <div class="max-w-3xl space-y-4">
                    <div class="flex items-center space-x-2">
                        @if ($latestEdition->status === 'running')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                <span class="w-2 h-2 mr-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Live Tournament in Progress
                            </span>
                        @elseif ($latestEdition->status === 'completed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-500/20 text-slate-300 border border-slate-500/40">
                                Tournament Completed
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                Upcoming Edition &bull; {{ $latestEdition->year }}
                            </span>
                        @endif
                        <span class="text-xs text-amber-400 font-semibold uppercase tracking-wider">
                            Year {{ $latestEdition->year }}
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                        {{ $latestEdition->title }}
                    </h1>

                    @if ($latestEdition->description)
                        <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl">
                            {{ $latestEdition->description }}
                        </p>
                    @endif

                    <!-- Tournament Dates & Fast Actions -->
                    <div class="pt-4 flex flex-wrap items-center gap-4">
                        @if ($latestEdition->start_date && $latestEdition->end_date)
                            <div class="flex items-center space-x-2 text-xs text-slate-300 bg-slate-900/70 px-3.5 py-2 rounded-xl border border-slate-700/80">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $latestEdition->start_date->format('M d') }} &ndash; {{ $latestEdition->end_date->format('M d, Y') }}</span>
                            </div>
                        @endif

                        @if ($latestEdition->status === 'running')
                            <a href="{{ route('today') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 shadow-md shadow-amber-500/20 transition-all">
                                <span>Today's Matches</span> &rarr;
                            </a>
                        @endif

                        <a href="{{ route('schedule') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition-all">
                            <span>Full Schedule</span>
                        </a>

                        <a href="{{ route('results') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition-all">
                            <span>Points Table</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('content')
<div class="space-y-12">

    @if ($latestEdition && $latestEdition->status === 'running')
        <!-- RUNNING STATE: Today's Action -->
        <section class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <h2 class="text-2xl font-black text-slate-900">Today's Live & Scheduled Matches</h2>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ now()->format('l, F j, Y') }} &bull; Live updates from KIU grounds
                    </p>
                </div>
                <a href="{{ route('today') }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                    View full board &rarr;
                </a>
            </div>

            @if ($todayMatches->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($todayMatches as $match)
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col justify-between group">
                            <!-- Card Header -->
                            <div class="bg-slate-50/80 p-4 border-b border-slate-100 flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-700">
                                    {{ $match->sport->name }} ({{ ucfirst($match->sport->category) }})
                                </span>
                                <div data-status-badge="{{ $match->id }}">
                                    @if ($match->status === 'live')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-600 animate-pulse"></span>
                                            Live
                                        </span>
                                    @elseif ($match->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-200 text-slate-700">
                                            Final
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/50">
                                            {{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('g:i A') : 'Scheduled' }}
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
                                            {{ $match->team1->name }}
                                        </span>
                                        <span class="text-xs text-slate-500 truncate">{{ $match->team1->department->name ?? 'Dept' }}</span>
                                    </div>
                                    <span data-team1-score="{{ $match->id }}" class="text-xl font-black text-slate-900 font-mono transition-transform duration-300">
                                        {{ $match->result ? $match->formatTeamScore(1) : '-' }}
                                    </span>
                                </div>

                                <div class="border-t border-slate-100"></div>

                                <!-- Team 2 -->
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-col min-w-0 pr-2">
                                        <span class="font-bold text-base text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                            {{ $match->team2->name }}
                                        </span>
                                        <span class="text-xs text-slate-500 truncate">{{ $match->team2->department->name ?? 'Dept' }}</span>
                                    </div>
                                    <span data-team2-score="{{ $match->id }}" class="text-xl font-black text-slate-900 font-mono transition-transform duration-300">
                                        {{ $match->result ? $match->formatTeamScore(2) : '-' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="bg-slate-50/70 p-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span class="truncate">
                                    {{ $match->venue->name ?? 'Venue TBD' }}
                                    @if ($match->round) &bull; {{ $match->round }} @endif
                                </span>
                                <a href="{{ route('matches.show', $match) }}" class="font-bold text-blue-600 hover:text-blue-800 transition-colors shrink-0 ml-2">
                                    Details &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center max-w-lg mx-auto shadow-sm">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No Matches Scheduled for Today</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">
                        Games resume tomorrow. View upcoming fixtures or previous results on the schedule.
                    </p>
                    <a href="{{ route('schedule') }}" class="inline-flex items-center px-4 py-2 bg-[#0a1226] hover:bg-[#060b18] text-amber-300 font-bold text-xs rounded-xl transition-colors">
                        Browse Full Schedule
                    </a>
                </div>
            @endif
        </section>

        @if($topMedalTally->isNotEmpty())
            <!-- General Trophy Leaderboard Preview -->
            <section class="bg-gradient-to-br from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400/70 rounded-2xl p-6 sm:p-8 text-white shadow-xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-700/60 pb-4">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-400 bg-amber-400/20 px-2.5 py-0.5 rounded-full border border-amber-400/30">
                            Tournament Leaderboard
                        </span>
                        <h3 class="text-xl sm:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
                            <span>🏆 General Trophy Championship Race</span>
                        </h3>
                        <p class="text-xs text-slate-300 mt-0.5">
                            Leading academic departments competing for the ultimate University Sports Trophy.
                        </p>
                    </div>
                    <a href="{{ route('results') }}#general-trophy" class="inline-flex items-center text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors">
                        View Full Standings &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($topMedalTally as $item)
                        <div class="bg-slate-900/80 border {{ $item['rank'] === 1 ? 'border-amber-400 bg-amber-950/20 ring-1 ring-amber-400/50' : 'border-slate-800' }} rounded-xl p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black uppercase tracking-wider {{ $item['rank'] === 1 ? 'text-amber-400' : 'text-slate-400' }}">
                                        @if($item['rank'] === 1) 🥇 Rank #1 @elseif($item['rank'] === 2) 🥈 Rank #2 @else 🥉 Rank #3 @endif
                                    </span>
                                    <span class="text-sm font-black text-amber-400 font-mono">{{ $item['points'] }} PTS</span>
                                </div>
                                <h4 class="font-bold text-white text-base">{{ $item['department']->name }}</h4>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-300">
                                <span>🥇 {{ $item['gold'] }}</span>
                                <span>🥈 {{ $item['silver'] }}</span>
                                <span>🥉 {{ $item['bronze'] }}</span>
                                <span class="font-bold text-slate-400">{{ $item['total_medals'] }} Medals</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    @else
        <!-- NON-RUNNING STATE: "Next Gala Coming Soon" & Past Highlights -->
        <section class="space-y-10">

            <!-- "Next Gala Coming Soon" Banner Card -->
            <div class="bg-gradient-to-r from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400/80 rounded-2xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10 max-w-2xl space-y-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-400/20 text-amber-300 border border-amber-400/40">
                        Stay Tuned
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                        Next Gala Coming Soon
                    </h2>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Preparations for the upcoming Karakoram International University Sports Gala are actively underway! Check back soon for official announcements, participating department rosters, and complete match schedules.
                    </p>
                    <div class="pt-2 flex flex-wrap gap-3">
                        <a href="{{ route('schedule') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-sm transition-colors">
                            Explore Schedule
                        </a>
                        <a href="{{ route('results') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl border border-white/20 transition-colors">
                            View Past Results
                        </a>
                    </div>
                </div>
            </div>

            <!-- Highlights of the Latest Completed Edition -->
            @if ($completedEdition)
                <div class="space-y-6">
                    <div class="border-b border-slate-200 pb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-6 h-6 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.789l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <h3 class="text-2xl font-black text-slate-900">
                                Highlights of {{ $completedEdition->title }}
                            </h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Relive the championship records and memorable moments from the {{ $completedEdition->year }} gala.
                        </p>
                    </div>

                    <!-- Champions Grid -->
                    @if ($completedEdition->winners->count() > 0)
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Tournament Champions</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($completedEdition->winners as $winner)
                                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex items-center justify-between">
                                        <div>
                                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                                {{ $winner->sport->name }} ({{ ucfirst($winner->sport->category) }})
                                            </span>
                                            <div class="font-extrabold text-slate-900 text-sm mt-0.5 flex items-center space-x-1.5">
                                                <span class="text-amber-500 font-black">🏆</span>
                                                <span>{{ $winner->winnerDepartment->name ?? 'Champion Dept' }}</span>
                                            </div>
                                            @if ($winner->runnerUpDepartment)
                                                <div class="text-[11px] text-slate-500 mt-1">
                                                    Runner-Up: <span class="font-medium text-slate-700">{{ $winner->runnerUpDepartment->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if ($winner->final_score)
                                            <span class="font-mono text-xs font-bold px-2 py-1 bg-slate-100 rounded text-slate-800">
                                                {{ $winner->final_score }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Photo Gallery Highlights -->
                    @if ($completedEdition->photos->count() > 0)
                        <div class="pt-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Photo Moments</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach ($completedEdition->photos->take(8) as $photo)
                                    <div class="group relative rounded-xl overflow-hidden aspect-video bg-slate-900 border border-slate-200 shadow-xs">
                                        <img src="{{ $photo->image_url }}" alt="{{ $photo->caption ?? 'Edition Highlight' }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        @if ($photo->caption)
                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-2 text-white text-[11px] font-medium truncate">
                                                {{ $photo->caption }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </section>
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

        async function pollHomeScores() {
            try {
                const response = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) return;
                const data = await response.json();
                if (!data.matches || !Array.isArray(data.matches)) return;

                data.matches.forEach(match => {
                    // Update Score 1
                    const s1El = document.querySelector(`[data-team1-score="${match.id}"]`);
                    if (s1El) {
                        const newT1 = match.team1_score !== null ? String(match.team1_score) : '-';
                        if (s1El.textContent.trim() !== newT1) {
                            s1El.textContent = newT1;
                            flashUpdate(s1El);
                        }
                    }

                    // Update Score 2
                    const s2El = document.querySelector(`[data-team2-score="${match.id}"]`);
                    if (s2El) {
                        const newT2 = match.team2_score !== null ? String(match.team2_score) : '-';
                        if (s2El.textContent.trim() !== newT2) {
                            s2El.textContent = newT2;
                            flashUpdate(s2El);
                        }
                    }

                    // Update Status Badge
                    const badgeContainer = document.querySelector(`[data-status-badge="${match.id}"]`);
                    if (badgeContainer) {
                        if (match.status === 'live') {
                            badgeContainer.innerHTML = `
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-600 animate-pulse"></span>
                                    Live
                                </span>
                            `;
                        } else if (match.status === 'completed') {
                            badgeContainer.innerHTML = `
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-200 text-slate-700">
                                    Final
                                </span>
                            `;
                        }
                    }
                });
            } catch (err) {
                console.error("Home scores poll error:", err);
            }
        }

        // Poll every 8 seconds if there are running matches on the page
        if (document.querySelector('[data-team1-score]')) {
            setInterval(pollHomeScores, 8000);
        }
    })();
</script>
@endpush
@endsection

