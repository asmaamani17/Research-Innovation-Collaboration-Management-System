<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function modal()
    {
        $users = User::all();
        return view('admin.users.modal', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'ic' => 'required|string|max:100|unique:users,ic',
            'password' => 'required|string|min:8',
            'role' => 'required|in:superadmin,admin',
        ]);

        $roleId = Role::where('role_name', $request->role)->value('id');

        User::create([
            'name' => $request->name,
            'ic' => $request->ic,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
            'status' => 'active',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'ic' => 'required|string|max:100|unique:users,ic,' . $user->id,
            'role' => 'required|in:superadmin,admin',
        ]);

        $roleId = Role::where('role_name', $request->role)->value('id');

        $user->update([
            'name' => $request->name,
            'ic' => $request->ic,
            'role_id' => $roleId,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
