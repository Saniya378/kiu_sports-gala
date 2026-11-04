@extends('layouts.admin')

@section('header_title', 'Add New Official / Scorer')
@section('header_subtitle', 'Register ground referees, scorers, or sports administrators.')

@section('header_actions')
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors duration-150">
        &larr; Back to Officials
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Full Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                       placeholder="e.g., Ali Raza or Coach Karim"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Email Address (Login Username) <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       placeholder="scorer1@kiu.edu.pk"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('email') border-rose-500 @enderror">
                @error('email')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Selector -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Role & Permissions <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="cursor-pointer border-2 rounded-2xl p-4 transition-all hover:border-emerald-300 {{ old('role', 'scorer') === 'scorer' ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-start space-x-3">
                            <input type="radio" name="role" value="scorer" class="mt-1 text-emerald-600 focus:ring-emerald-500" {{ old('role', 'scorer') === 'scorer' ? 'checked' : '' }}
                                   onclick="document.getElementById('dept-select-wrap').classList.add('hidden')">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">📱 Ground Scorer</span>
                                <span class="text-xs text-slate-500 leading-relaxed block mt-0.5">
                                    Enters scores, live pad, match photos.
                                </span>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-4 transition-all hover:border-amber-300 {{ old('role') === 'coordinator' ? 'border-amber-500 bg-amber-50/40' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-start space-x-3">
                            <input type="radio" name="role" value="coordinator" class="mt-1 text-amber-600 focus:ring-amber-500" {{ old('role') === 'coordinator' ? 'checked' : '' }}
                                   onclick="document.getElementById('dept-select-wrap').classList.remove('hidden')">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">🎓 Dept Coordinator</span>
                                <span class="text-xs text-slate-500 leading-relaxed block mt-0.5">
                                    Submits department teams & athlete squads.
                                </span>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-4 transition-all hover:border-indigo-300 {{ old('role') === 'admin' ? 'border-indigo-500 bg-indigo-50/40' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-start space-x-3">
                            <input type="radio" name="role" value="admin" class="mt-1 text-indigo-600 focus:ring-indigo-500" {{ old('role') === 'admin' ? 'checked' : '' }}
                                   onclick="document.getElementById('dept-select-wrap').classList.add('hidden')">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">🛡️ Administrator</span>
                                <span class="text-xs text-slate-500 leading-relaxed block mt-0.5">
                                    Full gala management and approvals.
                                </span>
                            </div>
                        </div>
                    </label>
                </div>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department Assignment (for Coordinators) -->
            <div id="dept-select-wrap" class="{{ old('role') === 'coordinator' ? '' : 'hidden' }}">
                <label for="department_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Assigned Academic Department <span class="text-rose-500">*</span>
                </label>
                <select name="department_id" id="department_id"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-amber-400 @error('department_id') border-rose-500 @enderror">
                    <option value="">-- Select Department --</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                           placeholder="Minimum 8 characters"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('password') border-rose-500 @enderror">
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Confirm Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           placeholder="Repeat password"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-colors">
                    Create Official Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

