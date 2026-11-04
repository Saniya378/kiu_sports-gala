@extends('layouts.admin')

@section('header_title', 'Edit Official: ' . $user->name)
@section('header_subtitle', 'Update official details, role, or change password.')

@section('header_actions')
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors duration-150">
        &larr; Back to Officials
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Full Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
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
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="cursor-pointer border-2 rounded-2xl p-4 transition-all hover:border-emerald-300 {{ old('role', $user->role) === 'scorer' ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-start space-x-3">
                            <input type="radio" name="role" value="scorer" class="mt-1 text-emerald-600 focus:ring-emerald-500" {{ old('role', $user->role) === 'scorer' ? 'checked' : '' }}>
                            <div>
                                <span class="block text-sm font-bold text-slate-900">📱 Ground Scorer</span>
                                <span class="text-xs text-slate-500 leading-relaxed block mt-0.5">
                                    Can enter scores, live pad updates, upload match photos, and log timeline events.
                                </span>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-4 transition-all hover:border-indigo-300 {{ old('role', $user->role) === 'admin' ? 'border-indigo-500 bg-indigo-50/40' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-start space-x-3">
                            <input type="radio" name="role" value="admin" class="mt-1 text-indigo-600 focus:ring-indigo-500" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }}>
                            <div>
                                <span class="block text-sm font-bold text-slate-900">🛡️ Full Administrator</span>
                                <span class="text-xs text-slate-500 leading-relaxed block mt-0.5">
                                    Full access: Fixtures, teams, sports, reports, officials, and gala settings.
                                </span>
                            </div>
                        </div>
                    </label>
                </div>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Optional Password Reset -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700">Change Password</span>
                    <span class="text-[11px] text-slate-400">(Leave blank to keep existing password)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-600 mb-1">
                            New Password
                        </label>
                        <input type="password" name="password" id="password"
                               placeholder="Min 8 chars"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('password') border-rose-500 @enderror">
                        @error('password')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1">
                            Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               placeholder="Repeat new password"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <div>
                    @if(auth()->id() === $user->id)
                        <span class="text-xs text-amber-600 font-medium">⚠️ This is your currently logged-in account.</span>
                    @endif
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

