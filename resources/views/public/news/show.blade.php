@extends('layouts.public')

@section('title', $news->title . ' - KIU Sports Gala News')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center gap-2 text-xs md:text-sm text-emerald-200/70">
        <a href="{{ route('home') }}" class="hover:text-amber-400 transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('news') }}" class="hover:text-amber-400 transition-colors">News</a>
        <span>/</span>
        <span class="text-amber-400 font-medium truncate max-w-xs">{{ $news->title }}</span>
    </div>

    <!-- Article Main Container -->
    <article class="bg-[#053d2b]/70 border border-emerald-800/80 rounded-3xl p-6 sm:p-10 shadow-2xl mb-12">
        <!-- Article Header -->
        <header class="mb-8 pb-6 border-b border-emerald-800/60">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                @if($news->edition)
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-400 text-slate-950">
                        {{ $news->edition->title }} ({{ $news->edition->year }})
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-900 border border-emerald-700 text-emerald-200">
                        Official Bulletin
                    </span>
                @endif

                <span class="text-xs text-emerald-300/80">
                    Published: {{ $news->published_at ? $news->published_at->format('l, F j, Y \a\t h:i A') : 'Recent' }}
                </span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight">
                {{ $news->title }}
            </h1>
        </header>

        <!-- Cover Image -->
        @if($news->image_url)
            <div class="mb-8 rounded-2xl overflow-hidden border border-emerald-700/60 shadow-lg bg-black/40">
                <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full max-h-[500px] object-cover">
            </div>
        @endif

        <!-- Article Body -->
        <div class="prose prose-invert prose-emerald max-w-none text-emerald-100 text-base leading-relaxed space-y-4">
            {!! nl2br(e($news->body)) !!}
        </div>

        <!-- Article Footer / Share / Back Link -->
        <div class="mt-10 pt-6 border-t border-emerald-800/60 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('news') }}"
               class="inline-flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-xl bg-[#022116] hover:bg-emerald-900 border border-emerald-800 text-amber-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to All News
            </a>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.news.edit', $news) }}"
                       class="text-xs font-semibold text-emerald-300 hover:text-amber-300 transition-colors">
                        Edit Article in Admin &rarr;
                    </a>
                @endif
            @endauth
        </div>
    </article>

    <!-- Recent Stories Section -->
    @if($recentNews->isNotEmpty())
        <div>
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                Recent News & Bulletins
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach($recentNews as $rec)
                    <div class="bg-[#053d2b]/50 border border-emerald-800/70 rounded-xl p-4 flex flex-col justify-between hover:border-amber-400/50 transition-all">
                        <div>
                            <span class="text-[10px] text-emerald-300/80 uppercase font-semibold">
                                {{ $rec->published_at ? $rec->published_at->format('M d, Y') : '' }}
                            </span>
                            <h4 class="font-bold text-white text-sm mt-1 line-clamp-2">
                                <a href="{{ route('news.show', $rec) }}" class="hover:text-amber-300 transition-colors">
                                    {{ $rec->title }}
                                </a>
                            </h4>
                        </div>
                        <div class="mt-3 pt-2 border-t border-emerald-900/60">
                            <a href="{{ route('news.show', $rec) }}" class="text-xs font-bold text-amber-400 hover:text-amber-300">
                                Read story &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
