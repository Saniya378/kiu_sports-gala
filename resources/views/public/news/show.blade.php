@extends('layouts.public')

@section('title', $news->title . ' - KIU Sports Gala News')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs md:text-sm text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('news') }}" class="hover:text-blue-600 transition-colors">News</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold truncate max-w-xs">{{ $news->title }}</span>
    </div>

    <!-- Article Main Container -->
    <article class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xs">
        <!-- Article Header -->
        <header class="mb-8 pb-6 border-b border-slate-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                @if($news->edition)
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $news->edition->title }} ({{ $news->edition->year }})
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                        Official Bulletin
                    </span>
                @endif

                <span class="text-xs text-slate-400">
                    Published: {{ $news->published_at ? $news->published_at->format('l, F j, Y \a\t h:i A') : 'Recent' }}
                </span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                {{ $news->title }}
            </h1>
        </header>

        <!-- Cover Image -->
        @if($news->image_url)
            <div class="mb-8 rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-slate-900">
                <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full max-h-[500px] object-cover">
            </div>
        @endif

        <!-- Article Body -->
        <div class="text-slate-700 text-base leading-relaxed space-y-4">
            {!! nl2br(e($news->body)) !!}
        </div>

        <!-- Article Footer / Share / Back Link -->
        <div class="mt-10 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('news') }}"
               class="inline-flex items-center gap-2 text-xs font-sports font-bold uppercase tracking-wider px-5 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-amber-300 border border-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to All News
            </a>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.news.edit', $news) }}"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                        Edit Article in Admin &rarr;
                    </a>
                @endif
            @endauth
        </div>
    </article>

    <!-- Recent Stories Section -->
    @if($recentNews->isNotEmpty())
        <div class="space-y-4">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <span>Recent News & Bulletins</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach($recentNews as $rec)
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col justify-between hover:border-blue-400 transition-all shadow-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">
                                {{ $rec->published_at ? $rec->published_at->format('M d, Y') : '' }}
                            </span>
                            <h4 class="font-bold text-slate-900 text-sm mt-1 line-clamp-2">
                                <a href="{{ route('news.show', $rec) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $rec->title }}
                                </a>
                            </h4>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-100">
                            <a href="{{ route('news.show', $rec) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
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
