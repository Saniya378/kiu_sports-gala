@extends('layouts.public')

@section('title', 'Online Student Trials Registration | KIU Sports Gala')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 sm:p-8 text-white shadow-xl text-center sm:text-left">
        <div class="inline-flex items-center space-x-2 mb-2 bg-amber-400/20 px-3 py-1 rounded-full border border-amber-400/40">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs uppercase tracking-widest font-sports font-black text-amber-300">
                Talent Hunt &bull; Team Tryouts
            </span>
        </div>
        <h1 class="text-2xl sm:text-4xl font-black font-sports uppercase tracking-wide text-white">
            Department Team Trials Registration
        </h1>
        <p class="text-xs sm:text-sm text-blue-100 mt-1 max-w-xl font-medium">
            Represent your department at the Karakoram International University Annual Sports Gala. Register online for department trials and get selected into the official squad roster!
        </p>
    </div>

    <!-- Registration Card Form -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-sm">
        <form method="POST" action="{{ route('trials.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Gala & Discipline Selection -->
            <div class="border-b border-slate-100 pb-5">
                <h3 class="text-sm font-black font-sports uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs">1</span>
                    <span>Tournament &amp; Department</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Edition -->
                    <div>
                        <label for="edition_id" class="block text-xs font-bold text-slate-700 mb-1">Gala Edition <span class="text-rose-500">*</span></label>
                        <select name="edition_id" id="edition_id" required
                                class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2.5">
                            @foreach ($editions as $edition)
                                <option value="{{ $edition->id }}" {{ old('edition_id') == $edition->id ? 'selected' : '' }}>
                                    {{ $edition->title }} ({{ $edition->year }})
                                </option>
                            @endforeach
                        </select>
                        @error('edition_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-xs font-bold text-slate-700 mb-1">Your Department <span class="text-rose-500">*</span></label>
                        <select name="department_id" id="department_id" required
                                class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2.5">
                            <option value="">-- Select Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code ?? 'Dept' }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Sport Discipline -->
                    <div>
                        <label for="sport_id" class="block text-xs font-bold text-slate-700 mb-1">Sport Discipline <span class="text-rose-500">*</span></label>
                        <select name="sport_id" id="sport_id" required
                                class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2.5">
                            <option value="">-- Select Sport --</option>
                            @foreach ($sports as $sport)
                                <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                                    {{ $sport->name }} ({{ ucfirst($sport->category) }})
                                </option>
                            @endforeach
                        </select>
                        @error('sport_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Personal Student Info -->
            <div class="border-b border-slate-100 pb-5">
                <h3 class="text-sm font-black font-sports uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs">2</span>
                    <span>Student Information</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Student Full Name -->
                    <div>
                        <label for="student_name" class="block text-xs font-bold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="student_name" name="student_name" required value="{{ old('student_name') }}"
                               placeholder="e.g. Ali Ahmed"
                               class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2.5">
                        @error('student_name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Registration Number -->
                    <div>
                        <label for="reg_no" class="block text-xs font-bold text-slate-700 mb-1">University Registration / Roll No <span class="text-rose-500">*</span></label>
                        <input type="text" id="reg_no" name="reg_no" required value="{{ old('reg_no') }}"
                               placeholder="e.g. KIU-2023-CS-042"
                               class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2.5 font-mono">
                        @error('reg_no') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Gender <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <label class="cursor-pointer flex items-center space-x-2 p-2.5 rounded-xl border border-slate-200 text-xs font-semibold hover:border-amber-400">
                                <input type="radio" name="gender" value="male" {{ old('gender', 'male') === 'male' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-400">
                                <span>Male (Boys)</span>
                            </label>
                            <label class="cursor-pointer flex items-center space-x-2 p-2.5 rounded-xl border border-slate-200 text-xs font-semibold hover:border-amber-400">
                                <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-400">
                                <span>Female (Girls)</span>
                            </label>
                        </div>
                        @error('gender') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- WhatsApp / Mobile Contact -->
                    <div>
                        <label for="contact" class="block text-xs font-bold text-slate-700 mb-1">WhatsApp / Mobile Number <span class="text-rose-500">*</span></label>
                        <input type="text" id="contact" name="contact" required value="{{ old('contact') }}"
                               placeholder="e.g. 0355-1234567"
                               class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2.5">
                        <span class="text-[10px] text-slate-400 mt-1 block">Your Department Coordinator will notify you via this contact.</span>
                        @error('contact') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Sports Experience & Role -->
            <div>
                <h3 class="text-sm font-black font-sports uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs">3</span>
                    <span>Athletic Experience &amp; Position</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Experience Level -->
                    <div>
                        <label for="experience_level" class="block text-xs font-bold text-slate-700 mb-1">Experience Level <span class="text-rose-500">*</span></label>
                        <select name="experience_level" id="experience_level" required
                                class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2.5">
                            <option value="intermediate" {{ old('experience_level') === 'intermediate' ? 'selected' : '' }}>Intermediate (College / Club player)</option>
                            <option value="beginner" {{ old('experience_level') === 'beginner' ? 'selected' : '' }}>Beginner (Passionate / Departmental)</option>
                            <option value="advanced" {{ old('experience_level') === 'advanced' ? 'selected' : '' }}>Advanced (District / Regional champion)</option>
                            <option value="varsity" {{ old('experience_level') === 'varsity' ? 'selected' : '' }}>Varsity (Previous KIU Gala Player)</option>
                        </select>
                        @error('experience_level') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Playing Position / Role -->
                    <div>
                        <label for="playing_position" class="block text-xs font-bold text-slate-700 mb-1">Preferred Playing Position / Role</label>
                        <input type="text" id="playing_position" name="playing_position" value="{{ old('playing_position') }}"
                               placeholder="e.g. Fast Bowler, Striker, Setter, Singles..."
                               class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 py-2.5">
                        @error('playing_position') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Notes / Past Achievements -->
                <div class="mt-4">
                    <label for="additional_notes" class="block text-xs font-bold text-slate-700 mb-1">Past Sports Achievements / Fitness Notes (Optional)</label>
                    <textarea id="additional_notes" name="additional_notes" rows="2"
                              placeholder="Mention any tournament trophies, inter-college medals, or athletic achievements..."
                              class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-400 focus:ring-amber-400 p-3">{{ old('additional_notes') }}</textarea>
                    @error('additional_notes') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full py-3.5 px-6 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-sports font-black uppercase tracking-wider text-sm shadow-md transition-all active:scale-[0.99] flex items-center justify-center space-x-2">
                    <span>Submit Trial Application</span>
                    <span>&rarr;</span>
                </button>
                <p class="text-center text-[11px] text-slate-400 mt-2">
                    Only enrolled students of Karakoram International University are eligible for gala trials.
                </p>
            </div>
        </form>
    </div>

</div>
@endsection

