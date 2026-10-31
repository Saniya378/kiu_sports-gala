<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $sports = Sport::query()
            ->orderBy('name')
            ->orderBy('category')
            ->paginate(15);

        return view('admin.sports.index', compact('sports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.sports.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sports')->where(fn ($query) => $query->where('category', $request->input('category'))),
            ],
            'category' => ['required', 'in:boys,girls'],
            'rules' => ['nullable', 'string'],
        ], [
            'name.unique' => 'A sport with this name already exists in the selected category.',
        ]);

        Sport::create($validated);

        return redirect()
            ->route('admin.sports.index')
            ->with('success', 'Sport created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sport $sport): RedirectResponse
    {
        return redirect()->route('admin.sports.edit', $sport);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sport $sport): View
    {
        return view('admin.sports.edit', compact('sport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sport $sport): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sports')
                    ->where(fn ($query) => $query->where('category', $request->input('category')))
                    ->ignore($sport->id),
            ],
            'category' => ['required', 'in:boys,girls'],
            'rules' => ['nullable', 'string'],
        ], [
            'name.unique' => 'A sport with this name already exists in the selected category.',
        ]);

        $sport->update($validated);

        return redirect()
            ->route('admin.sports.index')
            ->with('success', 'Sport updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sport $sport): RedirectResponse
    {
        $sport->delete();

        return redirect()
            ->route('admin.sports.index')
            ->with('success', 'Sport deleted successfully.');
    }
}
