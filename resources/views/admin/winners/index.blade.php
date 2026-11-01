@extends('layouts.admin')

@section('header_title', 'Winners Archive')
@section('header_subtitle', 'Record and manage champions and runners-up for past tournaments and sports disciplines.')

@section('header_actions')
    <a href="{{ route('admin.winners.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Record Winner
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.winners.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Filter: Edition -->
            <div class="w-full md:w-64">
                <label for="filter_edition" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Edition
                </label>
                <select name="edition_id" id="filter_edition" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Editions</option>
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}" {{ request('edition_id') == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter: Sport -->
            <div class="w-full md:w-64">
                <label for="filter_sport" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Sport
                </label>
                <select name="sport_id" id="filter_sport" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Sports</option>
                    @foreach ($sports as $sport)
                        <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                            {{ $sport->name }} ({{ ucfirst($sport->category) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['edition_id', 'sport_id']))
                    <a href="{{ route('admin.winners.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Winners Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">Edition</th>
                        <th class="py-3.5 px-4">Sport</th>
                        <th class="py-3.5 px-4">Champion (Winner)</th>
                        <th class="py-3.5 px-4">Runner-Up</th>
                        <th class="py-3.5 px-4">Final Score</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse ($winners as $winner)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $winner->edition->title }}</div>
                                <div class="text-xs text-slate-500">Year {{ $winner->edition->year }}</div>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-800">{{ $winner->sport->name }}</span>
                                <span class="inline-block ml-1 px-1.5 py-0.5 rounded text-[11px] font-medium {{ $winner->sport->category === 'girls' ? 'bg-pink-50 text-pink-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ ucfirst($winner->sport->category) }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                @if ($winner->winnerDepartment)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        <svg class="w-3.5 h-3.5 mr-1 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.789l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $winner->winnerDepartment->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Not recorded</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                @if ($winner->runnerUpDepartment)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $winner->runnerUpDepartment->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Not recorded</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                @if ($winner->final_score)
                                    <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                        {{ $winner->final_score }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center space-x-1">
                                    <a href="{{ route('admin.winners.edit', $winner) }}"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors"
                                       title="Edit Record">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.winners.destroy', $winner) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this winner record?');"
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-semibold text-slate-900">No winners recorded</h4>
                                    <p class="text-xs text-slate-500">Record champions and runner-up results for gala editions.</p>
                                    <a href="{{ route('admin.winners.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                                        Record Winner
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($winners->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $winners->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

