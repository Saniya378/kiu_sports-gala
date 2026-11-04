@extends('layouts.admin')

@section('header_title', $team->name)
@section('header_subtitle', $team->sport->name . ' (' . ucfirst($team->sport->category) . ') &bull; ' . $team->edition->title)

@section('header_actions')
    <div class="flex items-center space-x-2">
        @if ($team->players->count() > 0)
            <a href="{{ route('teams.id-cards', $team) }}" target="_blank"
               class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                🪪 Print Squad ID Cards
            </a>
        @endif
        <a href="{{ route('coordinator.teams.index') }}"
           class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
            &larr; Back to Squads
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Status Alert Banner -->
    @if ($team->status === 'approved')
        <div class="bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-4 sm:p-5 flex items-center justify-between text-emerald-900 shadow-xs">
            <div class="flex items-center space-x-3">
                <span class="text-3xl">✅</span>
                <div>
                    <h3 class="font-black text-sm text-emerald-900">Official Participation Approved</h3>
                    <p class="text-xs text-emerald-700 mt-0.5">
                        This squad has been verified and cleared by the KIU Sports Directorate for official gala fixtures.
                    </p>
                </div>
            </div>
            <span class="hidden sm:inline-flex px-3 py-1 rounded-full text-xs font-black bg-emerald-200 text-emerald-900">
                ACTIVE IN GALA
            </span>
        </div>
    @elseif ($team->status === 'pending')
        <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-4 sm:p-5 flex items-center justify-between text-amber-900 shadow-xs">
            <div class="flex items-center space-x-3">
                <span class="text-3xl animate-bounce">⏳</span>
                <div>
                    <h3 class="font-black text-sm text-amber-900">Awaiting Directorate Approval</h3>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Your squad roster is currently in review. You can continue adding or updating players below.
                    </p>
                </div>
            </div>
            <span class="hidden sm:inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-200 text-amber-900">
                PENDING REVIEW
            </span>
        </div>
    @else
        <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-4 sm:p-5 text-rose-900 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-3xl">⚠️</span>
                    <div>
                        <h3 class="font-black text-sm text-rose-900">Action Required: Squad Not Approved</h3>
                        <p class="text-xs text-rose-700 mt-0.5">
                            <strong>Directorate Feedback:</strong> {{ $team->rejection_reason ?? 'Please verify athlete credentials and eligibility.' }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('coordinator.teams.resubmit', $team) }}">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black shadow-xs transition-colors">
                        Re-Submit for Approval &rarr;
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Add Athlete & CSV Import Forms -->
        <div class="space-y-6">

            <!-- Add Single Player Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <h3 class="text-sm font-black text-slate-900 mb-1 flex items-center">
                    <span class="mr-2">➕</span> Add Athlete to Roster
                </h3>
                <p class="text-xs text-slate-500 mb-4">Register a student athlete with photo accreditation.</p>

                <form method="POST" action="{{ route('coordinator.teams.players.store', $team) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="player_name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Athlete Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="player_name" required placeholder="e.g., Nasir Hussain"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                    </div>

                    <div>
                        <label for="reg_no" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            KIU Student Reg. No.
                        </label>
                        <input type="text" name="reg_no" id="reg_no" placeholder="e.g., KIU-2022-CS-1042"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-amber-400 focus:ring-1 focus:ring-amber-400 font-mono">
                    </div>

                    <div>
                        <label for="contact" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Phone / Contact No.
                        </label>
                        <input type="text" name="contact" id="contact" placeholder="e.g., 0312-3456789"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                    </div>

                    <div>
                        <label for="photo" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Athlete Photo (Pass & ID Card)
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100">
                        <span class="text-[10px] text-slate-400 block mt-1">JPEG, PNG or WEBP (Max 2MB).</span>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition-colors">
                        Add Athlete
                    </button>
                </form>
            </div>

            <!-- CSV Bulk Import Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <h3 class="text-sm font-black text-slate-900 mb-1 flex items-center">
                    <span class="mr-2">📄</span> CSV Bulk Import
                </h3>
                <p class="text-xs text-slate-500 mb-3">Upload squad roster from Excel or CSV sheet (Format: Name, RegNo, Contact).</p>

                <form method="POST" action="{{ route('coordinator.teams.players.import', $team) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="file" name="csv_file" required accept=".csv,.txt"
                           class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200">

                    <button type="submit"
                            class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors">
                        Upload CSV Roster
                    </button>
                </form>
            </div>

        </div>

        <!-- Right Column: Registered Players Roster -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-sm text-slate-900">Athlete Roster</h3>
                        <span class="text-xs text-slate-500">{{ $team->players->count() }} athletes registered in squad</span>
                    </div>

                    @if ($team->players->count() > 0)
                        <a href="{{ route('teams.id-cards', $team) }}" target="_blank"
                           class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                            View All ID Cards &rarr;
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 uppercase text-[10px] font-bold text-slate-500 border-b border-slate-200 tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Photo & Athlete</th>
                                <th class="px-5 py-3">Reg. Number</th>
                                <th class="px-5 py-3">Contact</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($team->players as $player)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-5 py-3.5 font-bold text-slate-900">
                                        <div class="flex items-center space-x-3">
                                            @if ($player->photo_url)
                                                <img src="{{ $player->photo_url }}" alt="{{ $player->name }}"
                                                     class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                            @else
                                                <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center font-black text-xs">
                                                    {{ strtoupper(substr($player->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <span>{{ $player->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-slate-700">
                                        {{ $player->reg_no ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600">
                                        {{ $player->contact ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="inline-flex items-center space-x-2">
                                            <a href="{{ route('players.card', $player) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                                                Digital Pass
                                            </a>

                                            <form method="POST" action="{{ route('coordinator.teams.players.destroy', [$team, $player]) }}"
                                                  onsubmit="return confirm('Remove athlete \'{{ $player->name }}\' from squad?');"
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2 py-1 rounded-lg text-[11px] font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 transition-colors">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                                        <p class="font-bold text-slate-700">No athletes added to this squad yet.</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Use the form on the left to add players or upload a CSV file.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

