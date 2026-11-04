@extends('layouts.admin')

@section('header_title', 'Register Department Squad')
@section('header_subtitle', 'Submit a new sports team for ' . $department->name . ' for official participation approval.')

@section('header_actions')
    <a href="{{ route('coordinator.teams.index') }}"
       class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors duration-150">
        &larr; Back to Squads
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('coordinator.teams.store') }}" class="space-y-6">
            @csrf

            <!-- Fixed Department Info Badge -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center space-x-3 text-xs text-amber-900">
                <span class="text-2xl">🏛️</span>
                <div>
                    <span class="font-bold uppercase tracking-wider text-[10px] text-amber-700 block">Department</span>
                    <strong class="text-sm font-black text-slate-900">{{ $department->name }}</strong>
                    <span class="block text-slate-500 mt-0.5">This squad will officially compete under your academic department.</span>
                </div>
            </div>

            <!-- Edition Selector -->
            <div>
                <label for="edition_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Sports Gala Edition <span class="text-rose-500">*</span>
                </label>
                <select name="edition_id" id="edition_id" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-400 font-semibold @error('edition_id') border-rose-500 @enderror">
                    @foreach ($editions as $edition)
                        <option value="{{ $edition->id }}" {{ old('edition_id') == $edition->id ? 'selected' : '' }}>
                            {{ $edition->title }} ({{ $edition->year }}) &bull; {{ ucfirst($edition->status) }}
                        </option>
                    @endforeach
                </select>
                @error('edition_id')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sport Discipline Selector -->
            <div>
                <label for="sport_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Sport Discipline & Category <span class="text-rose-500">*</span>
                </label>
                <select name="sport_id" id="sport_id" required onchange="suggestTeamName()"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-400 font-semibold @error('sport_id') border-rose-500 @enderror">
                    <option value="">-- Choose Sport Discipline --</option>
                    @foreach ($sports as $sport)
                        <option value="{{ $sport->id }}" data-sport-name="{{ $sport->name }}" data-category="{{ $sport->category }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                            {{ $sport->name }} ({{ ucfirst($sport->category) }})
                        </option>
                    @endforeach
                </select>
                @error('sport_id')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Team Squad Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Squad / Team Display Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       placeholder="e.g. CS Cricket XI or Computer Science (Boys)"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-400 @error('name') border-rose-500 @enderror">
                <p class="text-[11px] text-slate-400 mt-1">This official name will be shown on match schedules, live scorecards, and certificates.</p>
                @error('name')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notice on Approval Process -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-800">
                ℹ️ <strong>Note:</strong> Once registered, your team will have <strong>Pending</strong> status until verified by the Sports Directorate. You can immediately add your athletes and upload student accreditation photos.
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('coordinator.teams.index') }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-black shadow-xs transition-colors">
                    Register Squad & Continue &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function suggestTeamName() {
        const sportSelect = document.getElementById('sport_id');
        const nameInput = document.getElementById('name');
        const selectedOpt = sportSelect.options[sportSelect.selectedIndex];

        if (!selectedOpt || !selectedOpt.value) return;

        const sportName = selectedOpt.getAttribute('data-sport-name');
        const deptName = "{{ $department->name }}";

        if (!nameInput.value || nameInput.value.includes(deptName)) {
            nameInput.value = deptName + ' ' + sportName;
        }
    }
</script>
@endpush
@endsection

