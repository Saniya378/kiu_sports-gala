@extends('layouts.admin')

@section('header_title', 'Create Edition')
@section('header_subtitle', 'Add a new edition to the KIU Sports Gala roster.')

@section('header_actions')
    <a href="{{ route('admin.editions.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Editions
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800">Edition Details</h2>
        <p class="text-xs text-slate-500 mt-0.5">Please provide the edition year, dates, banner graphic, and status.</p>
    </div>

    <form method="POST" action="{{ route('admin.editions.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Edition Title <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="title"
                       id="title"
                       required
                       value="{{ old('title') }}"
                       placeholder="e.g. 15th KIU Annual Sports Gala"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-rose-500 @enderror">
                @error('title')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Year -->
            <div>
                <label for="year" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Year <span class="text-rose-500">*</span>
                </label>
                <input type="number"
                       name="year"
                       id="year"
                       required
                       min="1900"
                       max="2100"
                       value="{{ old('year', date('Y')) }}"
                       placeholder="2026"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('year') border-rose-500 @enderror">
                @error('year')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Start Date -->
            <div>
                <label for="start_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Start Date
                </label>
                <input type="date"
                       name="start_date"
                       id="start_date"
                       value="{{ old('start_date') }}"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('start_date') border-rose-500 @enderror">
                @error('start_date')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- End Date -->
            <div>
                <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    End Date
                </label>
                <input type="date"
                       name="end_date"
                       id="end_date"
                       value="{{ old('end_date') }}"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('end_date') border-rose-500 @enderror">
                @error('end_date')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Status <span class="text-rose-500">*</span>
                </label>
                <select name="status"
                        id="status"
                        required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-rose-500 @enderror">
                    <option value="upcoming" {{ old('status', 'upcoming') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="running" {{ old('status') === 'running' ? 'selected' : '' }}>Running</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Banner File Upload -->
        <div>
            <label for="banner" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                Banner Graphic
            </label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-indigo-400 transition-colors">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-slate-600 justify-center">
                        <label for="banner" class="relative cursor-pointer bg-white rounded-md font-semibold text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                            <span>Upload a file</span>
                            <input id="banner" name="banner" type="file" accept="image/*" class="sr-only" onchange="previewBanner(event)">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-slate-500">PNG, JPG, WEBP up to 4MB</p>
                    <div id="banner-preview-wrapper" class="hidden mt-3">
                        <img id="banner-preview" class="max-h-40 mx-auto rounded-lg shadow-sm border border-slate-200" />
                    </div>
                </div>
            </div>
            @error('banner')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                Description
            </label>
            <textarea name="description"
                      id="description"
                      rows="4"
                      placeholder="Theme, details, participating faculties or general information about this gala edition..."
                      class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit buttons -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.editions.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                Save Edition
            </button>
        </div>
    </form>
</div>

<script>
function previewBanner(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('banner-preview');
            const wrapper = document.getElementById('banner-preview-wrapper');
            preview.src = e.target.result;
            wrapper.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
