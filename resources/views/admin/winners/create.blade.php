@extends('layouts.admin')

@section('header_title', 'Record Winner')
@section('header_subtitle', 'Record the champion and runner-up department for an edition and sport discipline.')

@section('header_actions')
    <a href="{{ route('admin.winners.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Winners
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-base font-semibold text-slate-900">Winner Information</h2>
            <p class="text-xs text-slate-500 mt-1">Ideal for preserving historical sports gala results where match-by-match scores are unavailable.</p>
        </div>

        <form method="POST" action="{{ route('admin.winners.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Edition & Sport -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="edition_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Edition <span class="text-rose-500">*</span>
                    </label>
                    <select name="edition_id" id="edition_id" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('edition_id') border-rose-300 @enderror">
                        <option value="">Select Edition</option>
                        @foreach ($editions as $edition)
                            <option value="{{ $edition->id }}" {{ old('edition_id') == $edition->id ? 'selected' : '' }}>
                                {{ $edition->title }} ({{ $edition->year }})
                            </option>
                        @endforeach
                    </select>
                    @error('edition_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sport_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Sport Discipline <span class="text-rose-500">*</span>
                    </label>
                    <select name="sport_id" id="sport_id" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('sport_id') border-rose-300 @enderror">
                        <option value="">Select Sport</option>
                        @foreach ($sports as $sport)
                            <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                                {{ $sport->name }} ({{ ucfirst($sport->category) }})
                            </option>
                        @endforeach
                    </select>
                    @error('sport_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Winner & Runner-up Departments -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="winner_department_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Winner (Champion) Department
                    </label>
                    <select name="winner_department_id" id="winner_department_id"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('winner_department_id') border-rose-300 @enderror">
                        <option value="">None / Not recorded</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('winner_department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('winner_department_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="runner_up_department_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Runner-Up Department
                    </label>
                    <select name="runner_up_department_id" id="runner_up_department_id"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('runner_up_department_id') border-rose-300 @enderror">
                        <option value="">None / Not recorded</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('runner_up_department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('runner_up_department_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Final Score -->
            <div>
                <label for="final_score" class="block text-sm font-medium text-slate-700 mb-1">
                    Final Match Score / Details <span class="text-slate-400 font-normal text-xs">(optional)</span>
                </label>
                <input type="text" name="final_score" id="final_score"
                       placeholder="e.g. 3 - 1, or 175/6 vs 142/9 (Won by 33 runs)"
                       value="{{ old('final_score') }}"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('final_score') border-rose-300 @enderror">
                @error('final_score')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.winners.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    Save Winner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

