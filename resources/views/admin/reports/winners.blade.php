@extends('layouts.admin')

@section('title', 'Winners Report by Edition')

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
        .print-header {
            display: block !important;
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
    <!-- Action Bar & Filter (Hidden on Print) -->
    <div class="no-print bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reports.winners') }}" class="flex items-center gap-3 w-full sm:w-auto">
            <label for="edition_id" class="text-xs font-semibold text-slate-600 uppercase whitespace-nowrap">Select Edition:</label>
            <select name="edition_id" id="edition_id" onchange="this.form.submit()" class="text-sm border-slate-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                @foreach($editions as $ed)
                    <option value="{{ $ed->id }}" {{ $selectedEdition && $selectedEdition->id === $ed->id ? 'selected' : '' }}>
                        {{ $ed->title }} ({{ $ed->year }})
                    </option>
                @endforeach
            </select>
        </form>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <a href="{{ route('admin.reports.winners.export', ['edition_id' => $selectedEdition?->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV / Excel
            </a>
            <!-- Prominent Print Button -->
            <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-lg shadow transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Report
            </button>
            <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                &larr; Hub
            </a>
        </div>
    </div>

    <!-- Printable Report Sheet -->
    <div class="print-container bg-white p-6 sm:p-10 rounded-xl border border-slate-200 shadow-sm">
        <!-- Formal University Letterhead (Shown prominently in print and web) -->
        <div class="border-b-2 border-slate-800 pb-4 mb-6 text-center">
            <div class="text-xs font-bold uppercase tracking-widest text-slate-600">
                Karakoram International University &bull; Gilgit-Baltistan
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight mt-1">
                Directorate of Sports & Physical Education
            </h1>
            <div class="text-sm font-semibold text-emerald-800 mt-1">
                Official Winners & Championship Honors Report
            </div>
            @if($selectedEdition)
                <div class="text-xs font-medium text-slate-600 mt-1">
                    Gala Edition: <strong>{{ $selectedEdition->title }} ({{ $selectedEdition->year }})</strong>
                    @if($selectedEdition->status)
                        &bull; Status: {{ ucfirst($selectedEdition->status) }}
                    @endif
                </div>
            @endif
            <div class="text-[11px] text-slate-400 mt-2">
                Report Generated: {{ now()->format('l, F j, Y \a\t h:i A') }}
            </div>
        </div>

        @if($winners->isNotEmpty())
            <table class="w-full text-left text-sm border-collapse border border-slate-300">
                <thead>
                    <tr class="bg-slate-100 text-xs font-bold uppercase text-slate-800 border-b border-slate-300">
                        <th class="py-2.5 px-3 border border-slate-300 text-center w-12">#</th>
                        <th class="py-2.5 px-4 border border-slate-300">Sport Discipline</th>
                        <th class="py-2.5 px-3 border border-slate-300 text-center w-24">Division</th>
                        <th class="py-2.5 px-4 border border-slate-300 font-bold text-emerald-900">Champion Department (🏆 Gold)</th>
                        <th class="py-2.5 px-4 border border-slate-300">Runner-Up Department (🥈 Silver)</th>
                        <th class="py-2.5 px-3 border border-slate-300 text-center">Final Score</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($winners as $index => $w)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="py-2.5 px-3 border border-slate-300 text-center font-bold text-xs">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">
                                {{ $w->sport->name ?? 'Sport' }}
                            </td>
                            <td class="py-2.5 px-3 border border-slate-300 text-center text-xs uppercase font-medium">
                                {{ $w->sport->category ?? '-' }}
                            </td>
                            <td class="py-2.5 px-4 border border-slate-300 font-extrabold text-slate-900">
                                {{ $w->winnerDepartment->name ?? 'N/A' }}
                            </td>
                            <td class="py-2.5 px-4 border border-slate-300 font-medium text-slate-700">
                                {{ $w->runnerUpDepartment->name ?? 'None' }}
                            </td>
                            <td class="py-2.5 px-3 border border-slate-300 text-center font-mono text-xs font-bold">
                                {{ $w->final_score ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Signatures Section for Official Validation -->
            <div class="mt-16 pt-8 grid grid-cols-3 gap-6 text-center text-xs text-slate-700">
                <div>
                    <div class="border-t border-slate-400 pt-2 w-4/5 mx-auto font-bold">Sports Coordinator</div>
                    <div class="text-[10px] text-slate-500">Prepared By</div>
                </div>
                <div>
                    <div class="border-t border-slate-400 pt-2 w-4/5 mx-auto font-bold">Convener Sports Gala</div>
                    <div class="text-[10px] text-slate-500">Verified By</div>
                </div>
                <div>
                    <div class="border-t border-slate-400 pt-2 w-4/5 mx-auto font-bold">Director Sports & Physical Ed.</div>
                    <div class="text-[10px] text-slate-500">Approved By</div>
                </div>
            </div>
        @else
            <div class="p-8 text-center text-slate-500 border border-dashed border-slate-300 rounded-xl">
                No winners records entered for this edition.
            </div>
        @endif
    </div>
</div>
@endsection

