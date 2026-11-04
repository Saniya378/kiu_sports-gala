@extends('layouts.admin')

@section('header_title', 'Officials & Scorers')
@section('header_subtitle', 'Manage match officials, ground scorers, and platform administrators.')

@section('header_actions')
    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-xs transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Official / Scorer
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase text-slate-400">Total Users</span>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $users->total() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                👥
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase text-indigo-600">Administrators</span>
                <p class="text-2xl font-black text-indigo-900 mt-1">{{ $adminCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold">
                🛡️
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase text-amber-600">Coordinators</span>
                <p class="text-2xl font-black text-amber-900 mt-1">{{ $coordinatorCount ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 font-bold">
                🎓
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase text-emerald-600">Ground Scorers</span>
                <p class="text-2xl font-black text-emerald-900 mt-1">{{ $scorerCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold">
                📱
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
            <div class="flex-1 flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name or email..."
                           class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="w-full sm:w-44">
                    <select name="role" onchange="this.form.submit()"
                            class="w-full rounded-xl border border-slate-200 text-sm py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Only</option>
                        <option value="scorer" {{ request('role') === 'scorer' ? 'selected' : '' }}>Scorer Only</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition-colors">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Official Name</th>
                        <th class="px-6 py-3.5">Email Address</th>
                        <th class="px-6 py-3.5">Role / Permissions</th>
                        <th class="px-6 py-3.5">Created Date</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span>{{ $user->name }}</span>
                                        @if(auth()->id() === $user->id)
                                            <span class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        🛡️ Administrator
                                    </span>
                                @elseif ($user->role === 'coordinator')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        🎓 Coordinator
                                    </span>
                                    @if ($user->department)
                                        <span class="block text-[11px] text-slate-500 font-medium mt-0.5">
                                            {{ $user->department->name }}
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        📱 Ground Scorer
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 transition-colors">
                                        Edit / Password
                                    </a>

                                    @if(auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Are you sure you want to delete official \'{{ $user->name }}\'? They will no longer have access.');"
                                              class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-base font-semibold text-slate-700">No officials found.</p>
                                <p class="text-xs text-slate-400 mt-1">Add official ground scorers and platform administrators using the button above.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

