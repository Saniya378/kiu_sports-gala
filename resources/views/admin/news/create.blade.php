@extends('layouts.admin')

@section('title', 'Create News Article')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Write News Article</h1>
            <p class="text-sm text-slate-500">Publish a new sports bulletin, schedule update, or official communique.</p>
        </div>
        <a href="{{ route('admin.news.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 flex items-center">
            &larr; Back to News
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">Article Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="w-full text-sm border-slate-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('title') border-rose-500 @enderror"
                       placeholder="e.g. Grand Opening Ceremony Scheduled for October 15th">
                @error('title')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Edition Association (optional) -->
                <div>
                    <label for="edition_id" class="block text-sm font-semibold text-slate-700 mb-1">Related Gala Edition</label>
                    <select name="edition_id" id="edition_id"
                            class="w-full text-sm border-slate-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">-- General / University-Wide --</option>
                        @foreach($editions as $edition)
                            <option value="{{ $edition->id }}" {{ old('edition_id') == $edition->id ? 'selected' : '' }}>
                                {{ $edition->title }} ({{ $edition->year }})
                            </option>
                        @endforeach
                    </select>
                    @error('edition_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Published At -->
                <div>
                    <label for="published_at" class="block text-sm font-semibold text-slate-700 mb-1">Publication Date & Time</label>
                    <input type="datetime-local" name="published_at" id="published_at"
                           value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full text-sm border-slate-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <p class="text-[11px] text-slate-400 mt-1">Leave as current time to publish immediately.</p>
                    @error('published_at')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-semibold text-slate-700 mb-1">Cover Image (optional)</label>
                <input type="file" name="image" id="image" accept="image/*"
                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Recommended size: 1200x630 or 16:9 banner. Max 5MB.</p>
                @error('image')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Body Textarea -->
            <div>
                <label for="body" class="block text-sm font-semibold text-slate-700 mb-1">Article Body Content *</label>
                <textarea name="body" id="body" rows="10" required
                          class="w-full text-sm border-slate-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('body') border-rose-500 @enderror"
                          placeholder="Write the full news announcement, quotes, regulations, or tournament press release here...">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.news.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-medium text-sm rounded-lg shadow transition">
                    Publish News Article
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

