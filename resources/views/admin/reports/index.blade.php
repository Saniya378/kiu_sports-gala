@extends('layouts.admin')

@section('title', 'Admin Reports')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Tournament Reports & Print Center</h1>
        <p class="text-sm text-slate-500">Generate and print official KIU Sports Gala documentation, match schedules, and rosters.</p>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase">Editions</div>
            <div class="text-2xl font-extrabold text-slate-800 mt-1">{{ $editionsCount }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase">Fixtures</div>
            <div class="text-2xl font-extrabold text-slate-800 mt-1">{{ $fixturesCount }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase">Teams</div>
            <div class="text-2xl font-extrabold text-slate-800 mt-1">{{ $teamsCount }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase">Winners Recorded</div>
            <div class="text-2xl font-extrabold text-slate-800 mt-1">{{ $winnersCount }}</div>
        </div>
    </div>

    <!-- Report Selection Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Report 0: General Trophy / Medal Tally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-amber-500 transition">
            <div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl mb-4 border border-amber-200">
                    🥇
                </div>
                <h3 class="font-bold text-lg text-slate-800">General Trophy Medal Tally</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Overall university championship standings ranked by Gold, Silver, and Bronze medals and total points across departments.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.reports.medals') }}" class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-800 gap-1.5">
                    <span>Generate Medal Standings</span> &rarr;
                </a>
            </div>
        </div>

        <!-- Report 1: Winners List by Edition -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-emerald-500 transition">
            <div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl mb-4 border border-amber-200">
                    🏆
                </div>
                <h3 class="font-bold text-lg text-slate-800">Winners List by Edition</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Formal list of champions and runners-up for each sport discipline in a selected gala edition, including final scores and podium honors.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.reports.winners') }}" class="inline-flex items-center text-xs font-bold text-emerald-700 hover:text-emerald-900 gap-1.5">
                    <span>Generate Winners Report</span> &rarr;
                </a>
            </div>
        </div>

        <!-- Report 2: Fixtures List by Date -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-emerald-500 transition">
            <div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl mb-4 border border-blue-200">
                    📅
                </div>
                <h3 class="font-bold text-lg text-slate-800">Fixtures List by Date</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Daily match roster for ground coordinators and referees. Shows scheduled times, venues, participating departments, and live results.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.reports.fixtures') }}" class="inline-flex items-center text-xs font-bold text-emerald-700 hover:text-emerald-900 gap-1.5">
                    <span>Generate Fixtures Report</span> &rarr;
                </a>
            </div>
        </div>

        <!-- Report 3: Teams List by Sport -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-emerald-500 transition">
            <div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl mb-4 border border-emerald-200">
                    👥
                </div>
                <h3 class="font-bold text-lg text-slate-800">Teams List by Sport</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Comprehensive roster breakdown for a selected sport discipline with department allocations, player lists, and registration numbers.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.reports.teams') }}" class="inline-flex items-center text-xs font-bold text-emerald-700 hover:text-emerald-900 gap-1.5">
                    <span>Generate Teams Report</span> &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

