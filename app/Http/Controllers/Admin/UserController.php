<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of system users (Admins and Scorers).
     */
    public function index(Request $request): View
    {
        $query = User::with('department');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $adminCount = User::where('role', 'admin')->count();
        $scorerCount = User::where('role', 'scorer')->count();
        $coordinatorCount = User::where('role', 'coordinator')->count();

        return view('admin.users.index', compact('users', 'adminCount', 'scorerCount', 'coordinatorCount'));
    }

    /**
     * Show the form for creating a new official or scorer.
     */
    public function create(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.users.create', compact('departments'));
    }

    /**
     * Store a newly created official / scorer / coordinator.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,scorer,coordinator'],
            'department_id' => ['nullable', 'required_if:role,coordinator', 'exists:departments,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department_id' => $validated['role'] === 'coordinator' ? $validated['department_id'] : null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Official '{$user->name}' ({$user->role}) created successfully.");
    }

    /**
     * Show the form for editing an official.
     */
    public function edit(User $user): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'departments'));
    }

    /**
     * Update an official's name, email, or role.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => ['required', 'in:admin,scorer,coordinator'],
            'department_id' => ['nullable', 'required_if:role,coordinator', 'exists:departments,id'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Prevent self-demotion if current user is this admin and they try to make themselves a scorer
        if (auth()->id() === $user->id && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove admin privileges from your own logged-in account.']);
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department_id' => $validated['role'] === 'coordinator' ? $validated['department_id'] : null,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Remove an official from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent an admin from deleting their own account
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own active administrator account.');
        }

        // Prevent deleting the last remaining admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the only remaining administrator in the system.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Official '{$userName}' deleted successfully.");
    }
}

