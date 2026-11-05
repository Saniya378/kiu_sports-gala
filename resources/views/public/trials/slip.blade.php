@extends('layouts.public')

@section('title', 'Trial Registration Slip - ' . $trial->student_name . ' | KIU Sports Gala')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Action Bar (Print & Home) -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-blue-700">
            &larr; Return to Gala Home
        </a>

        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-amber-300 font-bold text-xs shadow-sm transition-colors">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Print Trial Slip</span>
        </button>
    </div>

    <!-- Official Printable Registration Slip -->
    <div class="bg-white border-2 border-slate-300 rounded-3xl p-6 sm:p-10 shadow-lg relative overflow-hidden print:border-none print:shadow-none print:p-0">
        <!-- University Header Banner -->
        <div class="text-center pb-6 border-b-2 border-slate-200">
            <div class="w-12 h-12 mx-auto rounded-xl bg-blue-950 text-amber-400 flex items-center justify-center font-black text-xl mb-2">
                KIU
            </div>
            <h2 class="font-sports font-black text-xl text-slate-900 uppercase tracking-wide">
                Karakoram International University
            </h2>
            <h3 class="text-xs font-bold uppercase tracking-widest text-blue-800 mt-0.5">
                Directorate of Sports &bull; Annual Sports Gala
            </h3>
            <span class="inline-block mt-3 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider border border-emerald-300">
                Official Department Trial Slip &bull; Confirmed
            </span>
        </div>

        <!-- Slip Metadata -->
        <div class="py-6 border-b border-slate-100 grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block font-medium">Slip ID:</span>
                <span class="font-mono font-black text-base text-slate-900">#TR-{{ str_pad($trial->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Registered Date:</span>
                <span class="font-bold text-slate-800">{{ $trial->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Tournament Edition:</span>
                <span class="font-bold text-amber-600">{{ $trial->edition->title ?? 'KIU Gala' }} ({{ $trial->edition->year ?? '' }})</span>
            </div>
        </div>

        <!-- Applicant Details -->
        <div class="py-6 space-y-3.5 text-xs">
            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                <span class="font-bold text-slate-500">Student Athlete Name:</span>
                <span class="font-black text-slate-900 text-sm uppercase">{{ $trial->student_name }}</span>
            </div>

            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                <span class="font-bold text-slate-500">University Registration / Roll #:</span>
                <span class="font-mono font-black text-slate-900">{{ $trial->reg_no }}</span>
            </div>

            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                <span class="font-bold text-slate-500">Academic Department:</span>
                <span class="font-bold text-blue-900">{{ $trial->department->name ?? 'Department' }}</span>
            </div>

            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                <span class="font-bold text-slate-500">Sport Discipline:</span>
                <span class="font-bold text-amber-600 uppercase">{{ $trial->sport->name ?? 'Sport' }} ({{ ucfirst($trial->sport->category ?? '') }})</span>
            </div>

            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                <span class="font-bold text-slate-500">Preferred Position / Role:</span>
                <span class="font-medium text-slate-800">{{ $trial->playing_position ?: 'General / Flexible' }}</span>
            </div>

            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                <span class="font-bold text-slate-500">Experience Level:</span>
                <span class="font-bold uppercase text-slate-700">{{ ucfirst($trial->experience_level) }}</span>
            </div>

            <div class="flex items-center justify-between py-1">
                <span class="font-bold text-slate-500">WhatsApp / Contact:</span>
                <span class="font-mono font-bold text-slate-800">{{ $trial->contact }}</span>
            </div>
        </div>

        <!-- Instructions & Signatures -->
        <div class="pt-6 border-t-2 border-slate-200 text-[11px] text-slate-500 space-y-4">
            <p class="leading-relaxed">
                <strong>Instructions for Trials:</strong> Please bring your original student ID card and proper athletic attire to the trials ground. Your Department Sports Coordinator will notify you with the trial time and venue schedule.
            </p>

            <div class="pt-10 flex items-center justify-between text-slate-400">
                <div class="text-center">
                    <div class="border-t border-slate-300 w-36 pt-1 text-[10px] font-bold text-slate-600">Student Signature</div>
                </div>
                <div class="text-center">
                    <div class="border-t border-slate-300 w-44 pt-1 text-[10px] font-bold text-slate-600">Department Coordinator / Coach</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

