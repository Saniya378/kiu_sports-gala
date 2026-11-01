@extends('layouts.admin')

@section('header_title', 'Create Team')
@section('header_subtitle', 'Register a new team for an edition, sport discipline, and department.')

@section('header_actions')
    <a href="{{ route('admin.teams.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Teams
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800">Team Details</h2>
        <p class="text-xs text-slate-500 mt-0.5">Each department can register one team per sport in a given gala edition.</p>
    </div>

    <form method="POST" action="{{ route('admin.teams.store') }}" class="p-6 space-y-6">
        @csrf

        <!-- Edition Selection -->
        <div>
            <label for="edition_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                Sports Gala Edition <span class="text-rose-500">*</span>
            </label>
            <select name="edition_id"
                    id="edition_id"
                    required
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('edition_id') border-rose-500 @enderror">
                <option value="">Select an edition...</option>
                @foreach ($editions as $edition)
                    <option value="{{ $edition->id }}" {{ old('edition_id') == $edition->id ? 'selected' : '' }}>
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
                    <option value="">Select sport...</option>
                    @foreach ($sports as $sport)
                        <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
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
                    <option value="">Select department...</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                       value="{{ old('name') }}"
                       placeholder="e.g. CS Warriors, Tigers, Rovers"
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
                    <option value="approved" {{ old('status', 'approved') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('status')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.teams.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                Save & Continue to Players
            </button>
        </div>
    </form>
</div>
@endsection

