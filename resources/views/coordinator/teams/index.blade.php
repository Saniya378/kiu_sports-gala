@extends('layouts.admin')

@section('header_title', $department->name . ' - Sports Coordinator Portal')
@section('header_subtitle', 'Manage departmental squads, register athletes, and track gala participation approvals.')

@section('header_actions')
    <a href="{{ route('coordinator.teams.create') }}"
       class="inline-flex items-center px-4 py-2 bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-black rounded-xl shadow-xs transition-colors duration-150">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Register New Squad
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Department Header Banner -->
    <div class="bg-gradient-to-r from-[#060b18] via-[#0c1630] to-[#0a1226] border-2 border-amber-400/80 rounded-2xl p-6 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-400 text-slate-950 font-black text-xl flex items-center justify-center shadow-lg shadow-amber-400/20">
                    🎓
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-amber-300 bg-amber-400/20 px-2.5 py-0.5 rounded-full border border-amber-400/30">
                        Official Department Portal
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black text-white mt-1">
                        {{ $department->name }}
                    </h2>
                    <p class="text-xs text-slate-300 mt-0.5">
                        Logged in as Coordinator: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('home') }}" target="_blank"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
                    View Public Portal &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <span class="text-[11px] font-bold uppercase text-slate-400">Total Squads</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-200 bg-emerald-50/20 p-4 shadow-xs">
            <span class="text-[11px] font-bold uppercase text-emerald-600">Approved</span>
            <p class="text-2xl font-black text-emerald-800 mt-1">{{ $stats['approved'] }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-amber-200 bg-amber-50/20 p-4 shadow-xs">
            <span class="text-[11px] font-bold uppercase text-amber-600">Pending Review</span>
            <p class="text-2xl font-black text-amber-800 mt-1">{{ $stats['pending'] }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-rose-200 bg-rose-50/20 p-4 shadow-xs">
            <span class="text-[11px] font-bold uppercase text-rose-600">Action Needed</span>
            <p class="text-2xl font-black text-rose-800 mt-1">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
        <form method="GET" action="{{ route('coordinator.teams.index') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <select name="edition_id" onchange="this.form.submit()"
                        class="rounded-xl border border-slate-200 text-xs py-2 px-3 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 font-semibold">
                    <option value="">All Editions</option>
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}" {{ request('edition_id') == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }})
                        </option>
                    @endforeach
                </select>

                <select name="sport_id" onchange="this.form.submit()"
                        class="rounded-xl border border-slate-200 text-xs py-2 px-3 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 font-semibold">
                    <option value="">All Sports</option>
                    @foreach ($sports as $sport)
                        <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                            {{ $sport->name }} ({{ ucfirst($sport->category) }})
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()"
                        class="rounded-xl border border-slate-200 text-xs py-2 px-3 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 font-semibold">
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved Only</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Only</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Action Needed (Rejected)</option>
                </select>
            </div>

            @if(request()->hasAny(['edition_id', 'sport_id', 'status']))
                <a href="{{ route('coordinator.teams.index') }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors text-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Squads Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($teams as $team)
            <div class="bg-white rounded-2xl border-2 {{ $team->status === 'approved' ? 'border-emerald-200 shadow-xs' : ($team->status === 'rejected' ? 'border-rose-300 shadow-sm' : 'border-amber-200 shadow-xs') }} p-5 flex flex-col justify-between hover:shadow-md transition-all">
                <div>
                    <!-- Top Status & Sport Badges -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                            {{ $team->edition->year ?? 'Gala' }} &bull; {{ $team->sport->name ?? 'Sport' }} ({{ ucfirst($team->sport->category ?? '') }})
                        </span>

                        @if ($team->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">
                                ✓ Approved
                            </span>
                        @elseif ($team->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                                ⏳ Pending Review
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-rose-100 text-rose-800">
                                ⚠️ Needs Action
                            </span>
                        @endif
                    </div>

                    <h3 class="text-lg font-black text-slate-900 truncate">
                        {{ $team->name }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $team->players_count }} Athletes Registered
                    </p>

                    @if ($team->status === 'rejected' && $team->rejection_reason)
                        <div class="mt-3 p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800">
                            <strong>Feedback:</strong> {{ $team->rejection_reason }}
                        </div>
                    @endif
                </div>

                <!-- Card Actions -->
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('coordinator.teams.show', $team) }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white transition-colors">
                        Manage Squad &rarr;
                    </a>

                    @if ($team->players_count > 0)
                        <a href="{{ route('teams.id-cards', $team) }}" target="_blank"
                           class="px-3 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors inline-flex items-center gap-1.5"
                           title="Download printable athlete pass sheet">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            <span>ID Cards</span>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl font-bold mb-3">
                    🏃
                </div>
                <h3 class="text-base font-bold text-slate-800">No Squads Registered Yet</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Register your department's teams for Cricket, Football, Volleyball or other gala disciplines to begin roster accreditation.
                </p>
                <div class="mt-4">
                    <a href="{{ route('coordinator.teams.create') }}"
                       class="inline-flex items-center px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-black rounded-xl shadow-xs transition-colors">
                        + Register First Squad
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if ($teams->hasPages())
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            {{ $teams->links() }}
        </div>
    @endif

</div>
@endsection

