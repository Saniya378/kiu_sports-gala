<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $editions = Edition::query()
            ->orderByDesc('year')
            ->paginate(10);

        return view('admin.editions.index', compact('editions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.editions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'digits:4', 'unique:editions,year'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:upcoming,running,completed'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('editions', 'public');
        }

        Edition::create($validated);

        return redirect()
            ->route('admin.editions.index')
            ->with('success', 'Edition created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Edition $edition): View
    {
        return view('admin.editions.show', compact('edition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Edition $edition): View
    {
        $edition->load(['photos.sport', 'winners.sport', 'winners.winnerDepartment']);
        $sports = \App\Models\Sport::orderBy('name')->get();

        return view('admin.editions.edit', compact('edition', 'sports'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Edition $edition): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'digits:4', 'unique:editions,year,' . $edition->id],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:upcoming,running,completed'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('banner')) {
            if ($edition->banner && Storage::disk('public')->exists($edition->banner)) {
                Storage::disk('public')->delete($edition->banner);
            }
            $validated['banner'] = $request->file('banner')->store('editions', 'public');
        }

        $edition->update($validated);

        return redirect()
            ->route('admin.editions.index')
            ->with('success', 'Edition updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Edition $edition): RedirectResponse
    {
        if ($edition->banner && Storage::disk('public')->exists($edition->banner)) {
            Storage::disk('public')->delete($edition->banner);
        }

        $edition->delete();

        return redirect()
            ->route('admin.editions.index')
            ->with('success', 'Edition deleted successfully.');
    }
}
