<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Sport;
use App\Models\StudentTrial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentTrialController extends Controller
{
    /**
     * Show the public trial application registration form.
     */
    public function create(): View
    {
        $editions = Edition::whereIn('status', ['running', 'upcoming'])->orderBy('year', 'desc')->get();
        if ($editions->isEmpty()) {
            $editions = Edition::orderBy('year', 'desc')->take(2)->get();
        }

        $departments = Department::orderBy('name')->get();
        $sports = Sport::orderBy('name')->get();

        return view('public.trials.create', compact('editions', 'departments', 'sports'));
    }

    /**
     * Store student trial registration.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'edition_id' => ['required', 'exists:editions,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'sport_id' => ['required', 'exists:sports,id'],
            'student_name' => ['required', 'string', 'max:255'],
            'reg_no' => [
                'required',
                'string',
                'max:100',
                Rule::unique('student_trials')->where(fn ($q) => $q
                    ->where('edition_id', $request->edition_id)
                    ->where('department_id', $request->department_id)
                    ->where('sport_id', $request->sport_id)
                ),
            ],
            'gender' => ['required', 'in:male,female'],
            'contact' => ['required', 'string', 'max:50'],
            'experience_level' => ['required', 'in:beginner,intermediate,advanced,varsity'],
            'playing_position' => ['nullable', 'string', 'max:255'],
            'additional_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'reg_no.unique' => 'You have already submitted a trial application for this sport in your department.',
        ]);

        $validated['status'] = 'pending';

        $trial = StudentTrial::create($validated);

        return redirect()->route('trials.slip', $trial)
            ->with('success', 'Trial registration submitted successfully! Save or print your trial confirmation slip.');
    }

    /**
     * Show official trial registration slip.
     */
    public function slip(StudentTrial $trial): View
    {
        $trial->load(['edition', 'department', 'sport']);

        return view('public.trials.slip', compact('trial'));
    }
}

