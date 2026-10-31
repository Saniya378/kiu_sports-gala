@extends('layouts.admin')

@section('header_title', 'Create Venue')
@section('header_subtitle', 'Add a sports ground, hall, or stadium location.')

@section('header_actions')
    <a href="{{ route('admin.venues.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Venues
    </a>
@endsection

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800">Venue Information</h2>
        <p class="text-xs text-slate-500 mt-0.5">Enter the unique name of the venue location.</p>
    </div>

    <form method="POST" action="{{ route('admin.venues.store') }}" class="p-6 space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                Venue Name <span class="text-rose-500">*</span>
            </label>
            <input type="text"
                   name="name"
                   id="name"
                   required
                   value="{{ old('name') }}"
                   placeholder="e.g. Main Cricket Ground, Indoor Sports Complex"
                   class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-rose-500 @enderror">
            @error('name')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.venues.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                Save Venue
            </button>
        </div>
    </form>
</div>
@endsection
