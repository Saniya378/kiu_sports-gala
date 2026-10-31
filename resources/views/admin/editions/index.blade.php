@extends('layouts.admin')

@section('header_title', 'Editions')
@section('header_subtitle', 'Manage sports gala years, dates, statuses, and promotional banners.')

@section('header_actions')
    <a href="{{ route('admin.editions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Edition
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    @if ($editions->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-2xl">
                🏆
            </div>
            <h3 class="text-lg font-bold text-slate-800">No Editions Found</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                Get started by creating your first Sports Gala edition with a year, dates, and banner.
            </p>
            <a href="{{ route('admin.editions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Edition
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Banner</th>
                        <th class="py-3.5 px-4">Title & Year</th>
                        <th class="py-3.5 px-4">Timeline</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach ($editions as $edition)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Banner -->
                            <td class="py-3 px-4 w-28">
                                @if ($edition->banner)
                                    <img src="{{ asset('storage/' . $edition->banner) }}" alt="{{ $edition->title }}" class="w-20 h-12 object-cover rounded-lg border border-slate-200 shadow-sm" />
                                @else
                                    <div class="w-20 h-12 rounded-lg bg-slate-100 border border-dashed border-slate-300 flex items-center justify-center text-xs text-slate-400">
                                        No Image
                                    </div>
                                @endif
                            </td>

                            <!-- Title & Year -->
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-base flex items-center gap-2">
                                    <span>{{ $edition->title }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $edition->year }}
                                    </span>
                                </div>
                                @if ($edition->description)
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1 max-w-md">
                                        {{ $edition->description }}
                                    </p>
                                @endif
                            </td>

                            <!-- Dates -->
                            <td class="py-3 px-4 text-xs text-slate-600">
                                @if ($edition->start_date || $edition->end_date)
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>
                                            {{ $edition->start_date ? $edition->start_date->format('M d, Y') : 'TBD' }}
                                            &mdash;
                                            {{ $edition->end_date ? $edition->end_date->format('M d, Y') : 'TBD' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Dates not scheduled</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4">
                                @if ($edition->status === 'running')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Running
                                    </span>
                                @elseif ($edition->status === 'upcoming')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-amber-500"></span>
                                        Upcoming
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        Completed
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('admin.editions.edit', $edition) }}"
                                       class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg transition-colors">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.editions.destroy', $edition) }}" onsubmit="return confirm('Are you sure you want to delete this edition? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-medium rounded-lg transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($editions->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $editions->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
