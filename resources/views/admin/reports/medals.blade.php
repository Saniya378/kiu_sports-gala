@extends('layouts.admin')

@section('title', 'Overall Department Medal Tally Report')

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
        <form method="GET" action="{{ route('admin.reports.medals') }}" class="flex items-center gap-3 w-full sm:w-auto">
            <label for="edition_id" class="text-xs font-semibold text-slate-600 uppercase whitespace-nowrap">Select Edition:</label>
            <select name="edition_id" id="edition_id" onchange="this.form.submit()" class="text-sm border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($editions as $ed)
                    <option value="{{ $ed->id }}" {{ $selectedEdition && $selectedEdition->id === $ed->id ? 'selected' : '' }}>
                        {{ $ed->title }} ({{ $ed->year }})
                    </option>
                @endforeach
            </select>
        </form>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <a href="{{ route('admin.reports.medals.export', ['edition_id' => $selectedEdition?->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV / Excel
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Standings
            </button>
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                &larr; Hub
            </a>
        </div>
    </div>

    <!-- Printable Report Sheet -->
    <div class="print-container bg-white p-8 rounded-2xl border border-slate-200 shadow-sm max-w-5xl mx-auto">
        <!-- University Letterhead (Visible on screen and print) -->
        <div class="text-center pb-6 mb-6 border-b-2 border-slate-900">
            <h1 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-slate-900">
                Karakoram International University, Gilgit
            </h1>
            <h2 class="text-base sm:text-lg font-bold text-slate-700 mt-1 uppercase tracking-wide">
                Directorate of Sports & Physical Education
            </h2>
            <div class="inline-block mt-2 px-4 py-1 rounded-full bg-slate-100 border border-slate-300 text-xs font-black uppercase tracking-widest text-slate-900">
                Official General Trophy & Department Medal Tally
            </div>
            <p class="text-xs text-slate-500 mt-2">
                Tournament: <strong class="text-slate-800">{{ $selectedEdition->title ?? 'KIU Sports Gala' }} ({{ $selectedEdition->year ?? date('Y') }})</strong>
                &bull; Generated on: {{ now()->format('d M Y, h:i A') }}
            </p>
        </div>

        <!-- Standings Table -->
        @if($medalTally->isNotEmpty())
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b-2 border-slate-400 text-xs uppercase tracking-wider text-slate-900">
                        <th class="py-3 px-3 text-center w-14 font-black">Rank</th>
                        <th class="py-3 px-4 font-black">Department</th>
                        <th class="py-3 px-3 text-center font-bold text-amber-700">🥇 Gold</th>
                        <th class="py-3 px-3 text-center font-bold text-slate-700">🥈 Silver</th>
                        <th class="py-3 px-3 text-center font-bold text-amber-900">🥉 Bronze</th>
                        <th class="py-3 px-3 text-center font-bold">Total Medals</th>
                        <th class="py-3 px-4 text-center font-black bg-slate-200">Total Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($medalTally as $row)
                        <tr class="{{ $row['rank'] === 1 ? 'bg-amber-50/60 font-medium' : '' }}">
                            <td class="py-3 px-3 text-center font-black">
                                @if($row['rank'] === 1)
                                    🏆 1
                                @else
                                    {{ $row['rank'] }}
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $row['department']->name }}</div>
                                @if(!empty($row['breakdown']['gold']))
                                    <div class="text-[11px] text-amber-700 font-medium mt-0.5">
                                        Gold: {{ implode(', ', $row['breakdown']['gold']) }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center font-black text-amber-600">{{ $row['gold'] }}</td>
                            <td class="py-3 px-3 text-center font-bold text-slate-600">{{ $row['silver'] }}</td>
                            <td class="py-3 px-3 text-center font-bold text-amber-800">{{ $row['bronze'] }}</td>
                            <td class="py-3 px-3 text-center font-bold text-slate-800">{{ $row['total_medals'] }}</td>
                            <td class="py-3 px-4 text-center font-black text-slate-950 text-base bg-slate-100">
                                {{ $row['points'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 text-xs text-slate-500 italic">
                * Points System: Gold Medal = 5 Points &bull; Silver Medal = 3 Points &bull; Bronze Medal = 1 Point.
            </div>

            <!-- Authority Signatures (Print Ready) -->
            <div class="mt-16 pt-8 border-t border-slate-300 grid grid-cols-2 gap-8 text-center text-xs">
                <div>
                    <div class="h-10"></div>
                    <div class="border-t border-slate-400 w-48 mx-auto"></div>
                    <p class="font-bold text-slate-800 mt-1">Convener / Sports Secretary</p>
                    <p class="text-slate-500">KIU Sports Committee</p>
                </div>
                <div>
                    <div class="h-10"></div>
                    <div class="border-t border-slate-400 w-48 mx-auto"></div>
                    <p class="font-bold text-slate-800 mt-1">Director Sports & Athletics</p>
                    <p class="text-slate-500">Karakoram International University</p>
                </div>
            </div>
        @else
            <div class="text-center py-12 text-slate-500">
                <p class="text-sm">No winners or medal records have been entered yet for this edition.</p>
            </div>
        @endif
    </div>
</div>
@endsection

