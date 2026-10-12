<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roleRecord', 'modules']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ic', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roleRecord', function ($q) use ($request) {
                $q->where('role_name', $request->role);
            });
        }

        if ($request->filled('module_id')) {
            $query->whereHas('modules', function ($q) use ($request) {
                $q->where('modules.id', $request->module_id);
            });
        }

        $users = $query->orderBy('id', 'asc')->paginate(15)->withQueryString();
        $roles = Role::all();
        $modules = Module::all();

        return view('admin.users.index', compact('users', 'roles', 'modules'));
    }

    public function modal()
    {
        $users = User::with(['roleRecord', 'modules'])->get();
        $roles = Role::all();
        $modules = Module::all();
        return view('admin.users.modal', compact('users', 'roles', 'modules'));
    }

    public function create()
    {
        $roles = Role::all();
        $modules = Module::all();
        return view('admin.users.create', compact('roles', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'ic' => 'required|string|max:100|unique:users,ic',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'modules' => 'nullable|array',
            'modules.*' => 'exists:modules,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'ic' => $request->ic,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        if ($request->filled('modules')) {
            $user->modules()->sync($request->modules);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully with assigned management modules.');
    }

    public function edit(User $user)
    {
        $user->load(['roleRecord', 'modules']);
        $roles = Role::all();
        $modules = Module::all();
        $userModuleIds = $user->modules->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'modules', 'userModuleIds'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'ic' => ['required', 'string', 'max:100', Rule::unique('users', 'ic')->ignore($user->id)],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8',
            'modules' => 'nullable|array',
            'modules.*' => 'exists:modules,id',
        ]);

        $user->update([
            'name' => $request->name,
            'ic' => $request->ic,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->modules()->sync($request->modules ?? []);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your logged-in account.');
        }

        $user->modules()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
