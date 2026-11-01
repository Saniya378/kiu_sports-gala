<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex items-center justify-between">
                    <div>
                        {{ __("You're logged in as") }} <span class="font-semibold text-indigo-600 uppercase text-xs px-2 py-0.5 bg-indigo-50 rounded-full">{{ auth()->user()->role }}</span>!
                    </div>

                    @if(in_array(auth()->user()->role, ['admin', 'scorer']))
                        <a href="{{ route('admin.score-entry.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                            Go to Score Entry &rarr;
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
