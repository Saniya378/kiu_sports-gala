@extends('layouts.admin')

@section('header_title', $edition->title)
@section('header_subtitle', 'Sports Gala Edition (' . $edition->year . ')')

@section('header_actions')
    <a href="{{ route('admin.editions.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Editions
    </a>
    <a href="{{ route('admin.editions.edit', $edition) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        Edit Edition
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        @if ($edition->banner)
            <div class="w-full h-64 bg-slate-100 overflow-hidden">
                <img src="{{ asset('storage/' . $edition->banner) }}" alt="{{ $edition->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Year {{ $edition->year }}
                    </span>
                    <h2 class="text-2xl font-bold text-slate-900 mt-2">{{ $edition->title }}</h2>
                </div>

                <div>
                    @if ($edition->status === 'running')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            <span class="w-2 h-2 mr-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Running
                        </span>
                    @elseif ($edition->status === 'upcoming')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            Upcoming
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                            Completed
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm">
                <div>
                    <span class="text-xs uppercase font-bold text-slate-400">Start Date</span>
                    <p class="font-medium text-slate-800 mt-0.5">
                        {{ $edition->start_date ? $edition->start_date->format('F d, Y') : 'Not specified' }}
                    </p>
                </div>
                <div>
                    <span class="text-xs uppercase font-bold text-slate-400">End Date</span>
                    <p class="font-medium text-slate-800 mt-0.5">
                        {{ $edition->end_date ? $edition->end_date->format('F d, Y') : 'Not specified' }}
                    </p>
                </div>
            </div>

            @if ($edition->description)
                <div>
                    <h3 class="text-xs uppercase font-bold text-slate-400 mb-2">Description</h3>
                    <div class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                        {{ $edition->description }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
