@extends('layouts.public')

@section('title', $player->name . ' - Official Match Pass & Player ID | KIU Sports Gala')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Toolbar (Hidden in Print) -->
    <div class="print:hidden flex flex-col sm:flex-row items-center justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
        <div class="flex items-center space-x-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center font-bold text-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
            </span>
            <div>
                <h1 class="text-base font-bold text-slate-900 leading-tight">Digital Athlete Pass & Accreditation</h1>
                <p class="text-xs text-slate-500">Official Directorate of Sports identification badge</p>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            @if ($player->team)
                <a href="{{ route('teams.public', ['edition_id' => $player->team->edition_id, 'sport_id' => $player->team->sport_id]) }}"
                   class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                    &larr; Team Squad
                </a>
                <a href="{{ route('teams.id-cards', $player->team) }}"
                   class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-colors">
                    All Squad Passes
                </a>
            @endif
            <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-blue-900 text-amber-300 hover:bg-blue-800 border border-blue-700 shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print / Save Pass
            </button>
        </div>
    </div>

    <!-- Official Player Credential Pass Card (Printable Layout) -->
    <div class="flex justify-center">
        <div id="player-id-card" class="w-full max-w-md bg-white border-2 border-slate-300 rounded-3xl shadow-xl overflow-hidden print:border-2 print:shadow-none print:max-w-sm">

            <!-- Card Lanyard Hole Slot Aesthetic -->
            <div class="bg-blue-950 pt-3 pb-1 text-center">
                <div class="w-16 h-3 mx-auto bg-blue-900 rounded-full border border-blue-800 shadow-inner"></div>
            </div>

            <!-- Pass Header (KIU Official Royal Blue & Gold) -->
            <div class="bg-gradient-to-r from-blue-950 via-indigo-950 to-blue-900 p-5 text-white text-center border-b-2 border-amber-400 relative">
                <div class="flex items-center justify-center space-x-2 mb-1">
                    <div class="w-7 h-7 rounded-lg bg-amber-400 text-blue-950 flex items-center justify-center font-black text-sm">
                        KIU
                    </div>
                    <span class="text-xs uppercase font-sports font-black tracking-widest text-amber-400">
                        Directorate of Sports
                    </span>
                </div>
                <h2 class="text-base font-black tracking-wide uppercase text-white">
                    {{ $player->team->edition->title ?? 'KIU Sports Gala' }} ({{ $player->team->edition->year ?? date('Y') }})
                </h2>
                <span class="inline-block mt-1 text-[10px] font-bold uppercase tracking-widest bg-amber-400/20 text-amber-300 px-3 py-0.5 rounded-full border border-amber-400/40">
                    Official Athlete Accreditation Pass
                </span>
            </div>

            <!-- Card Body -->
            <div class="p-6 space-y-5">

                <!-- Player Avatar & Verification Pill -->
                <div class="flex items-center space-x-4">
                    <!-- Silhouette / Photo Placeholder -->
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-950 to-indigo-900 p-0.5 shadow-md shrink-0 flex items-center justify-center text-amber-400 relative overflow-hidden">
                        @if($player->photo_url)
                            <img src="{{ $player->photo_url }}" alt="{{ $player->name }}" class="w-full h-full rounded-2xl object-cover">
                        @else
                            <span class="text-3xl font-black">
                                {{ strtoupper(substr($player->name, 0, 1)) }}
                            </span>
                        @endif
                        <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-white text-[9px] font-black" title="Verified">
                            ✓
                        </span>
                    </div>

                    <!-- Name & Reg Number -->
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Registered Athlete</span>
                        <h3 class="text-xl font-black text-slate-900 leading-tight truncate">
                            {{ $player->name }}
                        </h3>
                        <div class="mt-1 flex items-center gap-2">
                            @if ($player->reg_no)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $player->reg_no }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400 italic">No Reg No</span>
                            @endif

                            @if($player->team && $player->team->sport)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ ucfirst($player->team->sport->category) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Department & Team Details Grid -->
                <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Department</span>
                        <span class="font-bold text-slate-900 block truncate mt-0.5">
                            {{ $player->team->department->name ?? 'Department' }}
                        </span>
                        <span class="text-[10px] font-semibold text-indigo-600 font-mono">
                            [{{ $player->team->department->code ?? 'DEP' }}]
                        </span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Discipline</span>
                        <span class="font-bold text-slate-900 block truncate mt-0.5">
                            {{ $player->team->sport->name ?? 'Discipline' }}
                        </span>
                        <span class="text-[10px] text-slate-500">
                            Team: {{ $player->team->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <!-- Verification Status Box -->
                <div class="flex items-center justify-between p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-sm shrink-0">
                            ✓
                        </div>
                        <div>
                            <div class="text-xs font-black uppercase tracking-wider text-emerald-800">
                                Officially Verified
                            </div>
                            <div class="text-[11px] text-emerald-700">
                                Eligible for Gala Match Play
                            </div>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono font-bold text-emerald-700 bg-white px-2 py-1 rounded-lg border border-emerald-200">
                        VALID 2026
                    </span>
                </div>

                <!-- QR Code & Scan Prompt -->
                <div class="flex items-center space-x-4 p-3.5 rounded-2xl bg-blue-950 text-white border border-blue-900">
                    <div class="w-24 h-24 bg-white p-1 rounded-xl shrink-0 flex items-center justify-center shadow-md">
                        {!! $qrSvg !!}
                    </div>

                    <div class="text-xs space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 block">
                            Security Verification
                        </span>
                        <p class="text-slate-300 text-[11px] leading-snug">
                            Scan with any smartphone camera to verify athlete credentials on the official portal.
                        </p>
                        <div class="pt-1">
                            <span class="text-[9px] font-mono text-slate-400 block truncate">
                                ID: KIU-GALA-{{ $player->team_id }}-{{ $player->id }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Signature & Stamp Section -->
                <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-500">
                    <div>
                        <div class="font-bold text-slate-800 uppercase">Director of Sports</div>
                        <div class="text-[9px] text-slate-400">Karakoram International University</div>
                    </div>
                    <div class="text-right">
                        <div class="font-mono text-emerald-600 font-bold uppercase">● Security Verified</div>
                        <div class="text-[9px] text-slate-400">Match Official Pass</div>
                    </div>
                </div>

            </div>

            <!-- Card Bottom Bar -->
            <div class="bg-slate-100 px-5 py-2 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-400 font-mono">
                <span>KIU SPORTS GALA</span>
                <span>AUTHENTIC PASS</span>
            </div>

        </div>
    </div>

</div>

<!-- Print Styles -->
<style>
@media print {
    body {
        background: white !important;
        color: black !important;
    }
    header, footer, .print\:hidden {
        display: none !important;
    }
    #player-id-card {
        border: 2px solid #334155 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
        margin: 20px auto !important;
    }
}
</style>
@endsection

