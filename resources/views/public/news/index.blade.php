@extends('layouts.public')

@section('title', 'News & Bulletins - KIU Sports Gala')

@section('content')
<div class="space-y-8">
    <!-- News Header Banner (Unified Royal Blue & Athletic Gold) -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest font-sports font-black text-amber-300 bg-amber-400/20 px-3 py-1 rounded-full border border-amber-400/40">
                    Official Updates
                </span>
                <h1 class="text-2xl sm:text-4xl font-black font-sports uppercase tracking-wide text-white mt-3">
                    News & Gala Bulletins
                </h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-1 font-medium">
                    Stay updated with tournament schedules, results announcements, team rosters, and official communique.
                </p>
            </div>

            <!-- Edition Filter -->
            <div class="w-full md:w-auto min-w-[240px]">
                <form method="GET" action="{{ route('news') }}">
                    <label for="edition_id" class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">
                        Filter by Gala
                    </label>
                    <select name="edition_id" id="edition_id" onchange="this.form.submit()"
                            class="w-full bg-blue-950 border border-blue-800 rounded-xl px-4 py-2.5 text-amber-300 font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <option value="">All Announcements</option>
                        @foreach($editions as $edition)
                            <option value="{{ $edition->id }}" {{ request('edition_id') == $edition->id ? 'selected' : '' }}>
                                {{ $edition->title }} ({{ $edition->year }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- News Grid -->
    @if($newsList->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($newsList as $item)
                <article class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs hover:shadow-md hover:border-blue-400 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Cover Image -->
                        <div class="relative aspect-video bg-black/40 overflow-hidden">
                            @if($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center text-slate-400 p-6 text-center">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            @endif

                            @if($item->edition)
                                <div class="absolute top-3 left-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/70 backdrop-blur-md text-amber-300 border border-amber-400/40">
                                        {{ $item->edition->year }} Gala
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Card Content -->
                        <div class="p-5">
                            <div class="text-[11px] text-slate-400 mb-2 flex items-center gap-2">
                                <span>📅 {{ $item->published_at ? $item->published_at->format('M d, Y') : 'Recent' }}</span>
                                @if($item->edition)
                                    <span>&bull;</span>
                                    <span>{{ $item->edition->title }}</span>
                                @endif
                            </div>

                            <h2 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                <a href="{{ route('news.show', $item) }}">
                                    {{ $item->title }}
                                </a>
                            </h2>

                            <p class="text-xs text-slate-600 mt-2 line-clamp-3 leading-relaxed">
                                {{ Str::limit(strip_tags($item->body), 140) }}
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="p-5 pt-0">
                        <a href="{{ route('news.show', $item) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                            <span>Read Full Story</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        @if($newsList->hasPages())
            <div class="py-4">
                {{ $newsList->links() }}
            </div>
        @endif
    @else
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center max-w-lg mx-auto shadow-xs">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-amber-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">No News Bulletins Available</h3>
            <p class="text-sm text-slate-500">
                Official tournament bulletins and updates will be published here as events unfold.
            </p>
        </div>
    @endif
</div>
@endsection
