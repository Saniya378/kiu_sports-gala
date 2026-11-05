@extends('layouts.admin')

@section('header_title', $department->name . ' - Team Trials & Tryouts')
@section('header_subtitle', 'Review student trial applications, shortlist candidates, and draft top athletes directly into team squads.')

@section('header_actions')
    <a href="{{ route('trials.create') }}" target="_blank"
       class="inline-flex items-center px-4 py-2 bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-black rounded-xl shadow-xs transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
        Open Public Trial Form &rarr;
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Counters -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Applicants</div>
            <div class="text-2xl font-black font-sports text-slate-900 mt-1">{{ $stats['total'] }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <div class="text-[11px] font-bold text-amber-500 uppercase tracking-wider">Pending Review</div>
            <div class="text-2xl font-black font-sports text-amber-600 mt-1">{{ $stats['pending'] }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <div class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Shortlisted for Ground</div>
            <div class="text-2xl font-black font-sports text-blue-600 mt-1">{{ $stats['shortlisted'] }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <div class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">Selected / Drafted</div>
            <div class="text-2xl font-black font-sports text-emerald-600 mt-1">{{ $stats['selected'] }}</div>
        </div>
    </div>

    <!-- Filter Form Bar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
        <form method="GET" action="{{ route('coordinator.trials.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label for="sport_id" class="sr-only">Filter by Sport</label>
                <select name="sport_id" id="sport_id" onchange="this.form.submit()"
                        class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2">
                    <option value="">All Sports</option>
                    @foreach($sports as $sp)
                        <option value="{{ $sp->id }}" {{ request('sport_id') == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }} ({{ ucfirst($sp->category) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="sr-only">Filter by Status</label>
                <select name="status" id="status" onchange="this.form.submit()"
                        class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="shortlisted" {{ request('status') === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                    <option value="selected" {{ request('status') === 'selected' ? 'selected' : '' }}>Selected</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            @if(request()->hasAny(['sport_id', 'status']))
                <div>
                    <a href="{{ route('coordinator.trials.index') }}" class="inline-flex items-center justify-center w-full py-2 px-3 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">
                        Reset Filters
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Trial Applicants Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        @if($trials->isEmpty())
            <div class="text-center py-12 px-4 text-slate-400 text-xs">
                No trial applications received yet for {{ $department->name }}. Students can register via the public portal.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Student Athlete</th>
                            <th class="py-3 px-3">Sport Discipline</th>
                            <th class="py-3 px-3">Position / Role</th>
                            <th class="py-3 px-3">Experience</th>
                            <th class="py-3 px-3">WhatsApp / Mobile</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-4 text-right">Actions &amp; Draft</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($trials as $trial)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Student Name & Reg -->
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 text-sm block">{{ $trial->student_name }}</span>
                                    <span class="font-mono text-[11px] text-slate-500">{{ $trial->reg_no }}</span>
                                </td>

                                <!-- Sport -->
                                <td class="py-3 px-3 font-semibold text-slate-800">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-900 border border-blue-200">
                                        {{ $trial->sport->name ?? 'Sport' }}
                                    </span>
                                </td>

                                <!-- Position -->
                                <td class="py-3 px-3 text-slate-600">
                                    {{ $trial->playing_position ?: '-' }}
                                </td>

                                <!-- Experience -->
                                <td class="py-3 px-3 font-semibold uppercase text-[10px] text-slate-500">
                                    {{ $trial->experience_level }}
                                </td>

                                <!-- Contact -->
                                <td class="py-3 px-3 font-mono font-medium text-slate-700">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $trial->contact) }}" target="_blank" class="hover:text-emerald-600 inline-flex items-center gap-1">
                                        <span>{{ $trial->contact }}</span>
                                    </a>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3 px-3">
                                    @if($trial->status === 'selected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Selected
                                        </span>
                                    @elseif($trial->status === 'shortlisted')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                            Shortlisted
                                        </span>
                                    @elseif($trial->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions & Draft Button -->
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Update Status Form -->
                                        <form method="POST" action="{{ route('coordinator.trials.status', $trial) }}" class="inline-flex items-center">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()"
                                                    class="rounded-lg border-slate-300 text-[11px] font-semibold py-1 px-2 focus:border-amber-400 focus:ring-amber-400">
                                                <option value="pending" {{ $trial->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="shortlisted" {{ $trial->status === 'shortlisted' ? 'selected' : '' }}>Shortlist</option>
                                                <option value="selected" {{ $trial->status === 'selected' ? 'selected' : '' }}>Select</option>
                                                <option value="rejected" {{ $trial->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                            </select>
                                        </form>

                                        <!-- Draft to Squad Button -->
                                        <form method="POST" action="{{ route('coordinator.trials.draft', $trial) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-400 hover:bg-amber-300 text-slate-950 shadow-xs transition-colors"
                                                    title="Draft into department team squad">
                                                + Draft
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($trials->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $trials->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

