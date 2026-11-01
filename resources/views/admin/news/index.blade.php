@extends('layouts.admin')

@section('title', 'News & Bulletins')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">News & Announcements</h1>
            <p class="text-sm text-slate-500">Publish articles, press releases, and gala bulletins for public viewers.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-medium text-sm rounded-lg shadow transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Write News Article
        </a>
    </div>

    <!-- Filter by Edition -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.news.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1">
                <label for="edition_id" class="block text-xs font-semibold text-slate-600 uppercase mb-1">Filter by Edition</label>
                <select name="edition_id" id="edition_id" onchange="this.form.submit()" class="w-full text-sm border-slate-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All Gala Editions (or General News)</option>
                    @foreach($editions as $edition)
                        <option value="{{ $edition->id }}" {{ request('edition_id') == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }})
                        </option>
                    @endforeach
                </select>
            </div>
            @if(request('edition_id'))
                <a href="{{ route('admin.news.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                    Clear Filter
                </a>
            @endif
        </form>
    </div>

    <!-- News Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5 w-20">Cover</th>
                        <th class="px-6 py-3.5">Title</th>
                        <th class="px-6 py-3.5">Edition Tag</th>
                        <th class="px-6 py-3.5">Published At</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($newsList as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3">
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-14 h-10 object-cover rounded-md border border-slate-200">
                                @else
                                    <div class="w-14 h-10 bg-slate-100 border border-slate-200 rounded-md flex items-center justify-center text-slate-400 text-[10px] font-bold">
                                        No Image
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-3 font-semibold text-slate-800">
                                <a href="{{ route('news.show', $item) }}" target="_blank" class="hover:text-emerald-700 transition flex items-center gap-1.5">
                                    <span>{{ $item->title }}</span>
                                    <svg class="w-3.5 h-3.5 opacity-60 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </td>
                            <td class="px-6 py-3">
                                @if($item->edition)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $item->edition->title }} ({{ $item->edition->year }})
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">General / All-Time</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-xs text-slate-500 whitespace-nowrap">
                                {{ $item->published_at ? $item->published_at->format('M d, Y h:i A') : 'Draft' }}
                            </td>
                            <td class="px-6 py-3 text-right whitespace-nowrap text-xs">
                                <a href="{{ route('admin.news.edit', $item) }}" class="font-medium text-emerald-600 hover:text-emerald-900 mr-3">Edit</a>
                                <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this news article?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-rose-600 hover:text-rose-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                No news articles published yet. Click "Write News Article" to create your first announcement.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($newsList->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $newsList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
