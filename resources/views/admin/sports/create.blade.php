@extends('layouts.admin')

@section('header_title', 'Create Sport')
@section('header_subtitle', 'Register a new sport discipline with category and rules.')

@section('header_actions')
    <a href="{{ route('admin.sports.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Sports
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800">Sport Information</h2>
        <p class="text-xs text-slate-500 mt-0.5">Note: Sports must have a unique name per category (Boys / Girls).</p>
    </div>

    <form method="POST" action="{{ route('admin.sports.store') }}" class="p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sport Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Sport Name <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       required
                       value="{{ old('name') }}"
                       placeholder="e.g. Football, Cricket, Badminton"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Category <span class="text-rose-500">*</span>
                </label>
                <select name="category"
                        id="category"
                        required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category') border-rose-500 @enderror">
                    <option value="boys" {{ old('category') === 'boys' ? 'selected' : '' }}>Boys</option>
                    <option value="girls" {{ old('category') === 'girls' ? 'selected' : '' }}>Girls</option>
                </select>
                @error('category')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Rules -->
        <div>
            <label for="rules" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                Official Rules & Regulations
            </label>
            <textarea name="rules"
                      id="rules"
                      rows="6"
                      placeholder="Specify rules, player counts, scoring guidelines, match duration..."
                      class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('rules') border-rose-500 @enderror">{{ old('rules') }}</textarea>
            @error('rules')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.sports.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                Save Sport
            </button>
        </div>
    </form>
</div>
@endsection
