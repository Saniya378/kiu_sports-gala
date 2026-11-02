@extends('layouts.public')

@section('title', $team->name . ' - Team Player ID Cards & Accreditation Passes | KIU Sports Gala')

@section('content')
<div class="space-y-6">

    <!-- Top Action Toolbar (Hidden in Print) -->
    <div class="print:hidden flex flex-col md:flex-row items-center justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
        <div class="flex items-center space-x-3">
            <span class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center font-black text-2xl">
                🪪
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800 px-2.5 py-0.5 rounded-full">
                        {{ $team->sport->name ?? 'Sport' }} &bull; {{ ucfirst($team->sport->category ?? 'Open') }}
                    </span>
                    <span class="text-xs text-slate-500 font-medium">
                        {{ $team->edition->title ?? 'KIU Gala' }}
                    </span>
                </div>
                <h1 class="text-xl font-black text-slate-900 mt-1">
                    {{ $team->name }} &mdash; Squad Accreditation ID Cards
                </h1>
                <p class="text-xs text-slate-500">
                    {{ $team->department->name ?? 'Department' }} &bull; {{ $playersWithQr->count() }} Registered Athletes
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            <a href="{{ route('teams.public', ['edition_id' => $team->edition_id, 'sport_id' => $team->sport_id]) }}"
               class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                &larr; Return to Teams
            </a>
            <button onclick="window.print()"
                    class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold bg-[#0a1226] text-amber-300 hover:bg-[#060b18] shadow-md transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print All ID Passes (A4 Sheet)
            </button>
        </div>
    </div>

    <!-- Empty State -->
    @if ($playersWithQr->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-lg mx-auto shadow-xs">
            <h3 class="text-lg font-bold text-slate-900">No Athletes Registered Yet</h3>
            <p class="text-xs text-slate-500 mt-1 mb-4">
                No players have been registered for {{ $team->name }}. Add players in the administrative squad panel to generate passes.
            </p>
            <a href="{{ route('teams.public') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 text-amber-300 rounded-xl text-xs font-bold">
                View All Teams
            </a>
        </div>
    @else
        <!-- Printable Grid of Athlete ID Passes (2 Columns on A4 Paper) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:grid-cols-2 print:gap-4 print:p-0">
            @foreach ($playersWithQr as $player)
                <div class="athlete-pass-card bg-white border-2 border-slate-300 rounded-2xl overflow-hidden shadow-sm flex flex-col justify-between print:border-slate-800 print:shadow-none print:break-inside-avoid print:rounded-xl">

                    <!-- Pass Header Bar -->
                    <div class="bg-[#0a1226] text-white p-3 border-b-2 border-amber-400 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded bg-amber-400 text-[#0a1226] flex items-center justify-center font-black text-xs">
                                KIU
                            </span>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-wider text-amber-400 leading-tight">
                                    KIU Sports Gala {{ $team->edition->year ?? '2026' }}
                                </div>
                                <div class="text-[9px] text-slate-300">
                                    Official Athlete Pass
                                </div>
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            Verified ✓
                        </span>
                    </div>

                    <!-- Pass Body Content -->
                    <div class="p-4 flex gap-3 items-center">
                        <!-- Left: Avatar & QR Code -->
                        <div class="flex flex-col items-center space-y-2 shrink-0">
                            <div class="w-14 h-14 rounded-xl bg-slate-800 text-amber-400 font-black text-xl flex items-center justify-center shadow-xs">
                                {{ strtoupper(substr($player->name, 0, 1)) }}
                            </div>
                            <div class="w-16 h-16 bg-white p-0.5 border border-slate-200 rounded-lg flex items-center justify-center shadow-xs">
                                {!! $player->qr_svg !!}
                            </div>
                        </div>

                        <!-- Right: Athlete Info -->
                        <div class="min-w-0 flex-1 space-y-1">
                            <div>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Athlete Name</span>
                                <h3 class="text-base font-black text-slate-900 leading-tight truncate">
                                    {{ $player->name }}
                                </h3>
                            </div>

                            <div class="grid grid-cols-2 gap-1 text-[11px] pt-1">
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Reg. Number</span>
                                    <span class="font-mono font-bold text-slate-800 text-[10px]">
                                        {{ $player->reg_no ?? 'N/A' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Discipline</span>
                                    <span class="font-bold text-indigo-700 text-[10px] truncate block">
                                        {{ $team->sport->name ?? 'Sport' }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-1 border-t border-slate-100 text-[10px]">
                                <span class="text-slate-500 block truncate">
                                    <strong class="text-slate-700">{{ $team->department->name ?? 'Dept' }}</strong> ({{ $team->department->code ?? 'DEP' }})
                                </span>
                                <span class="text-slate-400 text-[9px] block">
                                    Team: {{ $team->name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Pass Footer & Authorized Signature -->
                    <div class="bg-slate-50 px-3.5 py-1.5 border-t border-slate-200 flex items-center justify-between text-[9px] text-slate-500">
                        <div>
                            <span class="font-mono text-slate-400">ID: KIU-{{ $team->id }}-{{ $player->id }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-700">Directorate of Sports</span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</div>

<!-- Print Styles for Team ID Passes Sheet -->
<style>
@media print {
    body {
        background: white !important;
        color: black !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    header, footer, .print\:hidden {
        display: none !important;
    }
    .athlete-pass-card {
        border: 1.5px dashed #475569 !important;
        box-shadow: none !important;
        page-break-inside: avoid !important;
        margin-bottom: 12px !important;
    }
}
</style>
@endsection

