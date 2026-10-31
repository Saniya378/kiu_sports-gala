@extends('layouts.admin')

@section('header_title', 'Sports')
@section('header_subtitle', 'Manage sports disciplines, categories (Boys / Girls), and official rules.')

@section('header_actions')
    <a href="{{ route('admin.sports.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Sport
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    @if ($sports->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-2xl">
                ⚽
            </div>
            <h3 class="text-lg font-bold text-slate-800">No Sports Registered</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                Register sports like Football, Cricket, Badminton, Volleyball for Boys and Girls categories.
            </p>
            <a href="{{ route('admin.sports.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Sport
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">#</th>
                        <th class="py-3.5 px-6">Sport Name</th>
                        <th class="py-3.5 px-6">Category</th>
                        <th class="py-3.5 px-6">Rules & Regulations</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach ($sports as $sport)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 text-xs text-slate-400 font-mono">
                                #{{ $sport->id }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="font-bold text-slate-900 text-base">
                                    {{ $sport->name }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6">
                                @if ($sport->category === 'boys')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                        👦 Boys
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-pink-50 text-pink-700 border border-pink-200">
                                        👧 Girls
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600 max-w-xs">
                                @if ($sport->rules)
                                    <div class="line-clamp-2 text-slate-700" title="{{ $sport->rules }}">
                                        {{ $sport->rules }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">No specific rules recorded</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('admin.sports.edit', $sport) }}"
                                       class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg transition-colors">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.sports.destroy', $sport) }}" onsubmit="return confirm('Are you sure you want to delete this sport?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-medium rounded-lg transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($sports->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $sports->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
