@extends('layouts.admin')

@section('header_title', 'Edit Team')
@section('header_subtitle', 'Update ' . $team->name . ' (' . ($team->department->name ?? 'Team') . ')')

@section('header_actions')
    <a href="{{ route('admin.teams.index', ['edition_id' => $team->edition_id]) }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Teams
    </a>
@endsection

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">

    <!-- Card 1: Team Configuration -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Team Details</h2>
                <p class="text-xs text-slate-500 mt-0.5">Modify department, sport category, or approval status.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                Team #{{ $team->id }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.teams.update', $team) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Edition Selection -->
            <div>
                <label for="edition_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Sports Gala Edition <span class="text-rose-500">*</span>
                </label>
                <select name="edition_id"
                        id="edition_id"
                        required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('edition_id') border-rose-500 @enderror">
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}" {{ old('edition_id', $team->edition_id) == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }}) &mdash; {{ ucfirst($edition->status) }}
                        </option>
                    @endforeach
                </select>
                @error('edition_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sport Selection -->
                <div>
                    <label for="sport_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Sport Discipline <span class="text-rose-500">*</span>
                    </label>
                    <select name="sport_id"
                            id="sport_id"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('sport_id') border-rose-500 @enderror">
                        @foreach ($sports as $sport)
                            <option value="{{ $sport->id }}" {{ old('sport_id', $team->sport_id) == $sport->id ? 'selected' : '' }}>
                                {{ $sport->name }} ({{ ucfirst($sport->category) }})
                            </option>
                        @endforeach
                    </select>
                    @error('sport_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department Selection -->
                <div>
                    <label for="department_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Department <span class="text-rose-500">*</span>
                    </label>
                    <select name="department_id"
                            id="department_id"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('department_id') border-rose-500 @enderror">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $team->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Team Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Team Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           required
                           value="{{ old('name', $team->name) }}"
                           placeholder="e.g. CS Warriors, Tigers"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-rose-500 @enderror">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Registration Status <span class="text-rose-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-rose-500 @enderror">
                        <option value="approved" {{ old('status', $team->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ old('status', $team->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ old('status', $team->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    Update Team Info
                </button>
            </div>
        </form>
    </div>

    <!-- Card 2: Inline Players Roster Management -->
    <div id="players-section" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    🏃
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Team Players Roster</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Manage athletes registered in this team. Add or remove players inline.</p>
                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                {{ $team->players->count() }} {{ Str::plural('Player', $team->players->count()) }}
            </span>
        </div>

        <!-- Add Player Inline Form -->
        <div class="p-6 bg-slate-50 border-b border-slate-100">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-3">Add New Player</h3>
            <form method="POST" action="{{ route('admin.teams.players.store', $team) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf

                <!-- Player Name -->
                <div class="md:col-span-2">
                    <label for="player_name" class="block text-xs font-semibold text-slate-600 mb-1">
                        Player Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="player_name"
                           required
                           value="{{ old('name') }}"
                           placeholder="Full Name"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-rose-500 @enderror">
                </div>

                <!-- Registration Number -->
                <div>
                    <label for="player_reg_no" class="block text-xs font-semibold text-slate-600 mb-1">
                        Reg / Roll No.
                    </label>
                    <input type="text"
                           name="reg_no"
                           id="player_reg_no"
                           value="{{ old('reg_no') }}"
                           placeholder="e.g. KIU-2023-CS-042"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('reg_no') border-rose-500 @enderror">
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="player_contact" class="block text-xs font-semibold text-slate-600 mb-1">
                        Contact / Phone
                    </label>
                    <input type="text"
                           name="contact"
                           id="player_contact"
                           value="{{ old('contact') }}"
                           placeholder="e.g. 0300-1234567"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact') border-rose-500 @enderror">
                </div>

                <div class="md:col-span-4 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Player
                    </button>
                </div>
            </form>
        </div>

        <!-- Players List Table -->
        <div class="overflow-x-auto">
            @if ($team->players->isEmpty())
                <div class="p-8 text-center text-slate-500">
                    <p class="text-sm">No players added to this team yet. Use the form above to add roster members.</p>
                </div>
            @else
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-3 px-6">#</th>
                            <th class="py-3 px-6">Player Name</th>
                            <th class="py-3 px-6">Reg. Number</th>
                            <th class="py-3 px-6">Contact Info</th>
                            <th class="py-3 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach ($team->players as $index => $player)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-6 text-xs text-slate-400 font-mono">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3 px-6">
                                    <span class="font-bold text-slate-900 text-sm">
                                        {{ $player->name }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-xs text-slate-700">
                                    {{ $player->reg_no ?? '-' }}
                                </td>
                                <td class="py-3 px-6 text-xs text-slate-700">
                                    {{ $player->contact ?? '-' }}
                                </td>
                                <td class="py-3 px-6 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.teams.players.destroy', [$team, $player]) }}" onsubmit="return confirm('Remove player {{ $player->name }} from this team?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded text-rose-600 hover:bg-rose-50 transition-colors">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
