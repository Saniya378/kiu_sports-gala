@extends('layouts.admin')

@section('header_title', 'Teams')
@section('header_subtitle', 'Manage participating teams by edition, sport discipline, and department.')

@section('header_actions')
    <button type="button"
            onclick="document.getElementById('copy-teams-modal').classList.remove('hidden')"
            class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
        </svg>
        Copy Teams From Previous Edition
    </button>

    <a href="{{ route('admin.teams.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Team
    </a>
@endsection

@section('content')
<div class="space-y-6">

    @if ($pendingCount > 0)
        <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-amber-900 shadow-xs">
            <div class="flex items-center space-x-3">
                <span class="text-2xl animate-pulse">⏳</span>
                <div>
                    <h3 class="font-black text-sm">Squads Awaiting Approval</h3>
                    <p class="text-xs text-amber-800 mt-0.5">
                        There are <strong>{{ $pendingCount }} department squad(s)</strong> submitted by Department Sports Coordinators pending review.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.teams.index', ['status' => 'pending']) }}"
               class="px-3.5 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs transition-colors self-start sm:self-auto">
                Review Pending Squads ({{ $pendingCount }}) &rarr;
            </a>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.teams.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Filter: Edition -->
            <div class="w-full md:w-56">
                <label for="filter_edition" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Filter by Edition
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
            <div class="w-full md:w-56">
                <label for="filter_sport" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Filter by Sport
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

            <!-- Filter: Status -->
            <div class="w-full md:w-48">
                <label for="filter_status" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Approval Status
                </label>
                <select name="status" id="filter_status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review ({{ $pendingCount }})</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    Filter
                </button>
                @if (request()->hasAny(['edition_id', 'sport_id', 'status']))
                    <a href="{{ route('admin.teams.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Teams Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        @if ($teams->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-2xl">
                    👥
                </div>
                <h3 class="text-lg font-bold text-slate-800">No Teams Found</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                    @if (request()->hasAny(['edition_id', 'sport_id']))
                        No teams match the selected filter criteria. Try adjusting your filters.
                    @else
                        Get started by registering a department team for a sport in an edition.
                    @endif
                </p>
                <div class="flex items-center justify-center space-x-3">
                    <a href="{{ route('admin.teams.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Team
                    </a>
                    @if ($editions->count() > 1)
                        <button type="button"
                                onclick="document.getElementById('copy-teams-modal').classList.remove('hidden')"
                                class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
                            Copy From Previous Edition
                        </button>
                    @endif
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6">Team Name</th>
                            <th class="py-3.5 px-6">Edition</th>
                            <th class="py-3.5 px-6">Sport</th>
                            <th class="py-3.5 px-6">Department</th>
                            <th class="py-3.5 px-6">Roster</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach ($teams as $team)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-slate-900 text-base">
                                        {{ $team->name }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-xs text-slate-700">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $team->edition->title ?? 'N/A' }} ({{ $team->edition->year ?? '' }})
                                    </span>
                                </td>
                                <td class="py-3.5 px-6">
                                    @if ($team->sport)
                                        <div class="flex items-center space-x-1.5">
                                            <span class="font-semibold text-slate-900">{{ $team->sport->name }}</span>
                                            @if ($team->sport->category === 'boys')
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">Boys</span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-pink-50 text-pink-700 border border-pink-200">Girls</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">No Sport</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-slate-800">
                                    {{ $team->department->name ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <a href="{{ route('admin.teams.edit', $team) }}#players-section"
                                       class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $team->players_count }} {{ Str::plural('player', $team->players_count) }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-6">
                                    @if ($team->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span>
                                            Approved
                                        </span>
                                    @elseif ($team->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-amber-500"></span>
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-2">
                                        @if ($team->status === 'pending')
                                            <form method="POST" action="{{ route('admin.teams.approve', $team) }}" class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition-colors"
                                                        title="Approve team for gala">
                                                    ✓ Approve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.teams.reject', $team) }}"
                                                  onsubmit="return prompt('Enter rejection reason for {{ $team->name }}:') ? true : false;"
                                                  class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold rounded-lg transition-colors"
                                                        title="Reject team">
                                                    ✕ Reject
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('teams.id-cards', $team) }}" target="_blank"
                                           class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg border border-indigo-200 transition-colors inline-flex items-center"
                                           title="Print Official Player ID Passes">
                                            🪪 Passes
                                        </a>

                                        <a href="{{ route('admin.teams.edit', $team) }}"
                                           class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg transition-colors">
                                            Edit & Players
                                        </a>

                                        <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" onsubmit="return confirm('Are you sure you want to delete this team? All its players will also be deleted.');">
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

            @if ($teams->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $teams->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

<!-- Modal: Copy Teams From Previous Edition -->
<div id="copy-teams-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 hidden">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    📋
                </span>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Copy Teams From Previous Edition</h3>
                    <p class="text-xs text-slate-500">Copies department teams (excluding player rosters). Duplicate sport+department pairings are skipped.</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('copy-teams-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.teams.copy') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="source_edition_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Source Edition (Copy From) <span class="text-rose-500">*</span>
                </label>
                <select name="source_edition_id" id="source_edition_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select previous edition...</option>
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}">
                            {{ $edition->title }} ({{ $edition->year }}) &mdash; {{ $edition->status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="target_edition_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Target Edition (Copy Into) <span class="text-rose-500">*</span>
                </label>
                <select name="target_edition_id" id="target_edition_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select target edition...</option>
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}" {{ request('edition_id') == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }}) &mdash; {{ $edition->status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800">
                Teams that already exist in the target edition for the same sport and department will be automatically skipped to prevent duplicates.
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="button"
                        onclick="document.getElementById('copy-teams-modal').classList.add('hidden')"
                        class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    Start Copying Teams
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

