@extends('layouts.admin')

@section('header_title', 'Departments')
@section('header_subtitle', 'Manage participating academic and administrative departments.')

@section('header_actions')
    <a href="{{ route('admin.departments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Department
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    @if ($departments->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-2xl">
                🏛️
            </div>
            <h3 class="text-lg font-bold text-slate-800">No Departments Found</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                Add university departments that will be competing in the KIU Sports Gala.
            </p>
            <a href="{{ route('admin.departments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Department
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">#</th>
                        <th class="py-3.5 px-6">Department Name</th>
                        <th class="py-3.5 px-6">Created</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach ($departments as $department)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 text-xs text-slate-400 font-mono">
                                #{{ $department->id }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-900 text-base">
                                    {{ $department->name }}
                                </div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $department->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('admin.departments.edit', $department) }}"
                                       class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg transition-colors">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Are you sure you want to delete this department?');">
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

        @if ($departments->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $departments->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
