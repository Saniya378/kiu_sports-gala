@extends('layouts.public')

@section('title', ($selectedEdition ? $selectedEdition->title . ' (' . $selectedEdition->year . ')' : 'Gala Archive') . ' - KIU Sports Gala')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Archive Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 border-b border-emerald-900/60 pb-6">
        <div>
            <span class="text-xs uppercase tracking-widest font-bold text-amber-400 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/20">
                Historical Records
            </span>
            <h1 class="text-3xl md:text-4xl font-black text-white mt-3 tracking-tight">
                Gala Archive & History
            </h1>
            <p class="text-emerald-200/80 text-sm md:text-base mt-1">
                Explore past editions of the KIU Annual Sports Gala, participating sports, champions, and photo chronicles.
            </p>
        </div>

        <!-- Year Selector -->
        <div class="w-full md:w-auto min-w-[240px]">
            <form method="GET" action="{{ route('archive') }}" id="archive-year-form">
                <label for="year" class="block text-xs font-semibold uppercase tracking-wider text-emerald-300 mb-1.5">
                    Select Gala Year
                </label>
                <div class="relative">
                    <select name="year" id="year" onchange="this.form.submit()"
                            class="w-full bg-[#022116] border border-amber-400/60 rounded-xl px-4 py-2.5 text-white font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 pr-10 shadow-lg">
                        @forelse($editions as $edition)
                            <option value="{{ $edition->year }}" {{ $selectedEdition && $selectedEdition->id === $edition->id ? 'selected' : '' }}>
                                {{ $edition->year }} &bull; {{ $edition->title }} ({{ ucfirst($edition->status) }})
                            </option>
                        @empty
                            <option value="">No Gala Editions</option>
                        @endforelse
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if(!$selectedEdition)
        <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-2xl p-12 text-center">
            <h3 class="text-xl font-bold text-white mb-2">No Archive Records Available</h3>
            <p class="text-sm text-emerald-200/70">Check back once past gala tournaments have been completed and cataloged.</p>
        </div>
    @else
        <!-- Selected Edition Hero Summary -->
        <div class="bg-gradient-to-br from-[#053d2b] to-[#022116] border border-emerald-800 rounded-2xl overflow-hidden shadow-2xl mb-12 relative">
            <div class="p-6 md:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                <div class="space-y-3 max-w-3xl">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-amber-400 text-slate-950">
                            Edition {{ $selectedEdition->year }}
                        </span>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-950/80 text-emerald-300 border border-emerald-800">
                            Status: {{ ucfirst($selectedEdition->status) }}
                        </span>
                        @if($selectedEdition->start_date && $selectedEdition->end_date)
                            <span class="text-xs text-emerald-300/80">
                                📅 {{ $selectedEdition->start_date->format('M d') }} - {{ $selectedEdition->end_date->format('M d, Y') }}
                            </span>
                        @endif
                    </div>

                    <h2 class="text-2xl md:text-3xl font-extrabold text-white">
                        {{ $selectedEdition->title }}
                    </h2>

                    @if($selectedEdition->description)
                        <p class="text-sm text-emerald-200/90 leading-relaxed">
                            {{ $selectedEdition->description }}
                        </p>
                    @endif
                </div>

                @if($selectedEdition->banner_url)
                    <div class="w-full lg:w-72 h-44 rounded-xl overflow-hidden border border-emerald-700/60 shadow-md shrink-0 bg-black/30">
                        <img src="{{ $selectedEdition->banner_url }}" alt="{{ $selectedEdition->title }}" class="w-full h-full object-cover">
                    </div>
                @endif
            </div>

            <!-- Quick Stats Bar -->
            <div class="bg-[#022116]/80 border-t border-emerald-800/80 px-6 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-2xl font-black text-amber-400">{{ $sports->count() }}</div>
                    <div class="text-xs text-emerald-300/80 font-medium">Sports Disciplines</div>
                </div>
                <div>
                    <div class="text-2xl font-black text-amber-400">{{ $winners->count() }}</div>
                    <div class="text-xs text-emerald-300/80 font-medium">Champions Crowned</div>
                </div>
                <div>
                    <div class="text-2xl font-black text-amber-400">{{ $results->count() }}</div>
                    <div class="text-xs text-emerald-300/80 font-medium">Recorded Results</div>
                </div>
                <div>
                    <div class="text-2xl font-black text-amber-400">{{ $editionPhotos->count() + $matchPhotos->count() }}</div>
                    <div class="text-xs text-emerald-300/80 font-medium">Archived Photos</div>
                </div>
            </div>
        </div>

        <!-- 1. Tournament Winners & Champions Section -->
        <section class="mb-14">
            <div class="flex items-center justify-between mb-6 pb-2 border-b border-emerald-800/60">
                <h3 class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                    Champions & Winners of {{ $selectedEdition->year }}
                </h3>
                <span class="text-xs font-semibold text-amber-400 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/20">
                    Winners Table
                </span>
            </div>

            @if($winners->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($winners as $winner)
                        <div class="bg-[#053d2b]/70 border border-emerald-800/80 rounded-2xl p-5 shadow-lg hover:border-amber-400/50 transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-emerald-800/60 text-xs text-emerald-300">
                                    <span class="font-bold uppercase tracking-wider text-amber-400">
                                        {{ $winner->sport->name }}
                                        @if($winner->sport->category)
                                            <span class="font-normal text-emerald-300">({{ ucfirst($winner->sport->category) }})</span>
                                        @endif
                                    </span>
                                    @if($winner->final_score)
                                        <span class="font-mono font-bold bg-[#022116] text-amber-300 px-2 py-0.5 rounded border border-emerald-800">
                                            Score: {{ $winner->final_score }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Champion -->
                                <div class="mt-4 p-3 rounded-xl bg-gradient-to-r from-amber-500/20 to-amber-400/5 border border-amber-400/40">
                                    <div class="text-[11px] uppercase font-bold tracking-wider text-amber-300 flex items-center gap-1.5">
                                        <span>🏆</span> Champion
                                    </div>
                                    <div class="text-base font-extrabold text-white mt-1">
                                        {{ $winner->winnerDepartment->name ?? 'Department Champion' }}
                                    </div>
                                </div>

                                <!-- Runner Up -->
                                @if($winner->runnerUpDepartment)
                                    <div class="mt-3 p-3 rounded-xl bg-[#022116]/60 border border-emerald-800/80">
                                        <div class="text-[11px] uppercase font-bold tracking-wider text-slate-300 flex items-center gap-1.5">
                                            <span>🥈</span> Runner-Up
                                        </div>
                                        <div class="text-sm font-semibold text-slate-200 mt-1">
                                            {{ $winner->runnerUpDepartment->name }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-[#053d2b]/40 border border-emerald-800/60 rounded-xl p-8 text-center">
                    <p class="text-sm text-emerald-200/80">No winner records cataloged yet for the {{ $selectedEdition->year }} gala.</p>
                </div>
            @endif
        </section>

        <!-- 2. Sports Disciplines in this Edition -->
        <section class="mb-14">
            <div class="flex items-center justify-between mb-6 pb-2 border-b border-emerald-800/60">
                <h3 class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Participating Sports ({{ $sports->count() }})
                </h3>
            </div>

            @if($sports->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($sports as $sport)
                        <div class="bg-[#053d2b]/50 border border-emerald-800/70 rounded-xl p-4 flex items-center justify-between hover:border-emerald-700 transition-colors">
                            <div>
                                <h4 class="font-bold text-white text-base">{{ $sport->name }}</h4>
                                <span class="text-xs text-amber-400 uppercase font-semibold">
                                    {{ ucfirst($sport->category) }} Category
                                </span>
                                @if($sport->rules)
                                    <p class="text-xs text-emerald-200/70 mt-1 line-clamp-1" title="{{ $sport->rules }}">
                                        Rules: {{ $sport->rules }}
                                    </p>
                                @endif
                            </div>
                            <span class="w-9 h-9 rounded-full bg-emerald-900/80 flex items-center justify-center text-amber-400 shrink-0 font-bold text-sm">
                                ⚽
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-[#053d2b]/40 border border-emerald-800/60 rounded-xl p-8 text-center">
                    <p class="text-sm text-emerald-200/80">No sports recorded for this edition.</p>
                </div>
            @endif
        </section>

        <!-- 3. Completed Match Results Section -->
        <section class="mb-14">
            <div class="flex items-center justify-between mb-6 pb-2 border-b border-emerald-800/60">
                <h3 class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Archived Match Results ({{ $results->count() }})
                </h3>
                <a href="{{ route('results', ['edition_id' => $selectedEdition->id]) }}" class="text-xs font-bold text-amber-400 hover:text-amber-300">
                    Full Standings Table &rarr;
                </a>
            </div>

            @if($results->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($results as $fixture)
                        <div class="bg-[#053d2b]/60 border border-emerald-800/80 rounded-xl p-4 flex flex-col justify-between hover:border-emerald-700 transition-colors">
                            <div class="flex items-center justify-between text-xs text-emerald-300 pb-2 mb-2 border-b border-emerald-800/50">
                                <span class="font-bold text-amber-300">{{ $fixture->sport->name }} &bull; {{ $fixture->round ?? 'Match #' . $fixture->id }}</span>
                                <span>{{ $fixture->match_date ? \Carbon\Carbon::parse($fixture->match_date)->format('M d, Y') : 'Date N/A' }}</span>
                            </div>

                            <div class="space-y-2 py-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-white text-sm {{ $fixture->result && $fixture->result->winner_team_id === $fixture->team1_id ? 'text-amber-300' : '' }}">
                                        {{ $fixture->team1->name ?? 'Team 1' }}
                                    </span>
                                    <span class="font-black text-lg text-white">{{ $fixture->result->team1_score ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-white text-sm {{ $fixture->result && $fixture->result->winner_team_id === $fixture->team2_id ? 'text-amber-300' : '' }}">
                                        {{ $fixture->team2->name ?? 'Team 2' }}
                                    </span>
                                    <span class="font-black text-lg text-white">{{ $fixture->result->team2_score ?? 0 }}</span>
                                </div>
                            </div>

                            <div class="mt-3 pt-2 border-t border-emerald-800/40 flex items-center justify-between text-xs">
                                <span class="text-emerald-300/70 truncate">📍 {{ $fixture->venue->name ?? 'Main Ground' }}</span>
                                <a href="{{ route('matches.show', $fixture) }}" class="font-bold text-amber-400 hover:text-amber-300">
                                    Details &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-[#053d2b]/40 border border-emerald-800/60 rounded-xl p-8 text-center">
                    <p class="text-sm text-emerald-200/80">No match-by-match results recorded for this edition.</p>
                </div>
            @endif
        </section>

        <!-- 4. Photos Archive Section -->
        <section class="mb-8">
            <div class="flex items-center justify-between mb-6 pb-2 border-b border-emerald-800/60">
                <h3 class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Edition Photo Chronicle ({{ $editionPhotos->count() + $matchPhotos->count() }})
                </h3>
                <a href="{{ route('gallery', ['year' => $selectedEdition->year]) }}" class="text-xs font-bold text-amber-400 hover:text-amber-300">
                    Open in Full Lightbox Gallery &rarr;
                </a>
            </div>

            @php
                $allEditionPhotos = collect();
                foreach($editionPhotos as $ep) {
                    $allEditionPhotos->push([
                        'url' => $ep->image_url,
                        'caption' => $ep->caption ?: ($selectedEdition->title . ' - Official Photo'),
                        'tag' => $ep->sport ? $ep->sport->name : 'Gala Archive',
                    ]);
                }
                foreach($matchPhotos as $mp) {
                    $allEditionPhotos->push([
                        'url' => $mp->image_url,
                        'caption' => $mp->caption ?: 'Match Action Photo',
                        'tag' => $mp->fixture->sport ? $mp->fixture->sport->name : 'Match Action',
                    ]);
                }
            @endphp

            @if($allEditionPhotos->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($allEditionPhotos as $photo)
                        <div class="group bg-[#053d2b]/60 border border-emerald-800/80 rounded-xl overflow-hidden shadow-md hover:border-amber-400/60 transition-all">
                            <div class="aspect-video bg-black/40 overflow-hidden relative">
                                <img src="{{ $photo['url'] }}" alt="{{ $photo['caption'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                <a href="{{ $photo['url'] }}" target="_blank" rel="noopener noreferrer" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold uppercase tracking-wider">
                                    View Full
                                </a>
                            </div>
                            <div class="p-3">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20">
                                    {{ $photo['tag'] }}
                                </span>
                                <p class="text-xs text-emerald-100 font-medium mt-1.5 truncate">
                                    {{ $photo['caption'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-[#053d2b]/40 border border-emerald-800/60 rounded-xl p-8 text-center">
                    <p class="text-sm text-emerald-200/80">No photos archived yet for the {{ $selectedEdition->year }} edition.</p>
                </div>
            @endif
        </section>
    @endif
</div>
@endsection

