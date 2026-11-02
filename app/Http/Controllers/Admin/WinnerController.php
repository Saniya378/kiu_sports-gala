<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Edition;
use App\Models\Sport;
use App\Models\Winner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WinnerController extends Controller
{
    /**
     * Display a listing of winners.
     */
    public function index(Request $request): View
    {
        $query = Winner::with(['edition', 'sport', 'winnerDepartment', 'runnerUpDepartment', 'secondRunnerUpDepartment']);

        if ($request->filled('edition_id')) {
            $query->where('edition_id', $request->edition_id);
        }

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        $winners = $query->join('editions', 'winners.edition_id', '=', 'editions.id')
            ->orderBy('editions.year', 'desc')
            ->select('winners.*')
            ->paginate(15)
            ->withQueryString();

        $editions = Edition::orderBy('year', 'desc')->get();
        $sports = Sport::orderBy('name')->get();

        return view('admin.winners.index', compact('winners', 'editions', 'sports'));
    }

    /**
     * Show the form for creating a new winner record.
     */
    public function create(): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $sports = Sport::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.winners.create', compact('editions', 'sports', 'departments'));
    }

    /**
     * Store a newly created winner record in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'edition_id' => ['required', 'exists:editions,id'],
            'sport_id' => [
                'required',
                'exists:sports,id',
                Rule::unique('winners')->where(fn ($query) =>
                    $query->where('edition_id', $request->edition_id)
                ),
            ],
            'winner_department_id' => ['nullable', 'exists:departments,id'],
            'runner_up_department_id' => ['nullable', 'exists:departments,id', 'different:winner_department_id'],
            'second_runner_up_department_id' => ['nullable', 'exists:departments,id', 'different:winner_department_id', 'different:runner_up_department_id'],
            'final_score' => ['nullable', 'string', 'max:255'],
        ], [
            'sport_id.unique' => 'A winner record already exists for this edition and sport.',
            'runner_up_department_id.different' => 'The runner-up department cannot be the same as the winner department.',
            'second_runner_up_department_id.different' => 'The bronze medal department must be distinct from winner and runner-up departments.',
        ]);

        Winner::create($validated);

        return redirect()->route('admin.winners.index')
            ->with('success', 'Winner record added successfully.');
    }

    /**
     * Show the form for editing the specified winner record.
     */
    public function edit(Winner $winner): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();
        $sports = Sport::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.winners.edit', compact('winner', 'editions', 'sports', 'departments'));
    }

    /**
     * Update the specified winner record in storage.
     */
    public function update(Request $request, Winner $winner): RedirectResponse
    {
        $validated = $request->validate([
            'edition_id' => ['required', 'exists:editions,id'],
            'sport_id' => [
                'required',
                'exists:sports,id',
                Rule::unique('winners')->where(fn ($query) =>
                    $query->where('edition_id', $request->edition_id)
                )->ignore($winner->id),
            ],
            'winner_department_id' => ['nullable', 'exists:departments,id'],
            'runner_up_department_id' => ['nullable', 'exists:departments,id', 'different:winner_department_id'],
            'second_runner_up_department_id' => ['nullable', 'exists:departments,id', 'different:winner_department_id', 'different:runner_up_department_id'],
            'final_score' => ['nullable', 'string', 'max:255'],
        ], [
            'sport_id.unique' => 'A winner record already exists for this edition and sport.',
            'runner_up_department_id.different' => 'The runner-up department cannot be the same as the winner department.',
            'second_runner_up_department_id.different' => 'The bronze medal department must be distinct from winner and runner-up departments.',
        ]);

        $winner->update($validated);

        return redirect()->route('admin.winners.index')
            ->with('success', 'Winner record updated successfully.');
    }

    /**
     * Remove the specified winner record from storage.
     */
    public function destroy(Winner $winner): RedirectResponse
    {
        $winner->delete();

        return redirect()->route('admin.winners.index')
            ->with('success', 'Winner record deleted successfully.');
    }
}

