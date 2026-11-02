@extends('layouts.admin')

@section('title', 'Teams & Squad Roster Report')

@push('styles')
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm 12mm 15mm 12mm;
        }
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-size: 11pt !important;
        }
        aside, nav, header, #sidebar, .no-print, button, form {
            display: none !important;
        }
        main {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }
        .print-container {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        th, td {
            border: 1px solid #444444 !important;
            padding: 6px 10px !important;
        }
        th {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Filter & Print Action Bar (Hidden on Print) -->
    <div class="no-print bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reports.teams') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div>
                <label for="edition_id" class="text-xs font-semibold text-slate-600 uppercase mr-2">Edition:</label>
                <select name="edition_id" id="edition_id" onchange="this.form.submit()"
                        class="text-sm border-slate-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @foreach($editions as $ed)
                        <option value="{{ $ed->id }}" {{ $selectedEdition && $selectedEdition->id === $ed->id ? 'selected' : '' }}>
                            {{ $ed->title }} ({{ $ed->year }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sport_id" class="text-xs font-semibold text-slate-600 uppercase mr-2">Sport Discipline:</label>
                <select name="sport_id" id="sport_id" onchange="this.form.submit()"
                        class="text-sm border-slate-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @foreach($sports as $sp)
                        <option value="{{ $sp->id }}" {{ $selectedSport && $selectedSport->id === $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }} ({{ ucfirst($sp->category) }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                &larr; Reports Hub
            </a>
            <!-- Prominent Print Button -->
            <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm rounded-lg shadow transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Official Report
            </button>
        </div>
    </div>

    <!-- Printable Report Sheet -->
    <div class="print-container bg-white p-6 sm:p-10 rounded-xl border border-slate-200 shadow-sm">
        <!-- Formal University Letterhead -->
        <div class="border-b-2 border-slate-800 pb-4 mb-6 text-center">
            <div class="text-xs font-bold uppercase tracking-widest text-slate-600">
                Karakoram International University &bull; Gilgit-Baltistan
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight mt-1">
                Directorate of Sports & Physical Education
            </h1>
            <div class="text-sm font-semibold text-emerald-800 mt-1">
                Participating Teams & Departmental Squad Rosters Report
            </div>
            @if($selectedSport && $selectedEdition)
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    Sport: <strong>{{ $selectedSport->name }} ({{ ucfirst($selectedSport->category) }})</strong> &bull;
                    Gala: <strong>{{ $selectedEdition->title }} ({{ $selectedEdition->year }})</strong>
                </div>
            @endif
            <div class="text-[11px] text-slate-400 mt-2">
                Report Generated: {{ now()->format('l, F j, Y \a\t h:i A') }}
            </div>
        </div>

        @if($teams->isNotEmpty())
            <table class="w-full text-left text-sm border-collapse border border-slate-300">
                <thead>
                    <tr class="bg-slate-100 text-xs font-bold uppercase text-slate-800 border-b border-slate-300">
                        <th class="py-2.5 px-3 border border-slate-300 text-center w-12">#</th>
                        <th class="py-2.5 px-4 border border-slate-300 w-1/4">Team Name & Dept</th>
                        <th class="py-2.5 px-3 border border-slate-300 text-center w-20">Squad Size</th>
                        <th class="py-2.5 px-4 border border-slate-300">Registered Players (Registration No.)</th>
                        <th class="py-2.5 px-3 border border-slate-300 text-center w-24">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($teams as $index => $team)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="py-2.5 px-3 border border-slate-300 text-center font-bold text-xs">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-4 border border-slate-300">
                                <div class="font-extrabold text-slate-900">{{ $team->name }}</div>
                                <div class="text-xs text-slate-500">{{ $team->department->name ?? 'Department' }}</div>
                            </td>
                            <td class="py-2.5 px-3 border border-slate-300 text-center font-bold text-xs">
                                {{ $team->players->count() }}
                            </td>
                            <td class="py-2.5 px-4 border border-slate-300 text-xs">
                                @if($team->players->isNotEmpty())
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($team->players as $p)
                                            <span class="inline-block px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800">
                                                {{ $p->name }}
                                                @if($p->reg_no)
                                                    <span class="text-slate-500 text-[10px]">({{ $p->reg_no }})</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">No players registered</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 border border-slate-300 text-center text-xs uppercase font-bold text-slate-700">
                                {{ $team->status }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Signatures Section -->
            <div class="mt-16 pt-8 grid grid-cols-3 gap-6 text-center text-xs text-slate-700">
                <div>
                    <div class="border-t border-slate-400 pt-2 w-4/5 mx-auto font-bold">Department Team Manager</div>
                    <div class="text-[10px] text-slate-500">Submitted By</div>
                </div>
                <div>
                    <div class="border-t border-slate-400 pt-2 w-4/5 mx-auto font-bold">Eligibility Scrutiny Committee</div>
                    <div class="text-[10px] text-slate-500">Endorsed By</div>
                </div>
                <div>
                    <div class="border-t border-slate-400 pt-2 w-4/5 mx-auto font-bold">Director Sports & Physical Ed.</div>
                    <div class="text-[10px] text-slate-500">Approved By</div>
                </div>
            </div>
        @else
            <div class="p-8 text-center text-slate-500 border border-dashed border-slate-300 rounded-xl">
                No teams registered for {{ $selectedSport->name ?? 'selected sport' }} in this edition.
            </div>
        @endif
    </div>
</div>
@endsection

