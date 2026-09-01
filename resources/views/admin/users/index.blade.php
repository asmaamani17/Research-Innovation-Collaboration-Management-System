@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            User Management
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Manage system users and their access permissions.
        </p>
    </div>
    <a href="#" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        <span>New User</span>
    </a>
</header>

<div class="px-8 pb-8">
    <!-- Users Table -->
    <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-divider-subtle/30">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-main">All Users</h3>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" placeholder="Search users..." class="pl-10 pr-4 py-2 bg-background-light border border-divider-subtle/30 rounded-lg text-sm focus:outline-none focus:border-primary w-64">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-divider-subtle text-lg">search</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-background-light">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-divider-subtle uppercase tracking-wider">User</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-divider-subtle uppercase tracking-wider">Email</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-divider-subtle uppercase tracking-wider">Role</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-divider-subtle uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-divider-subtle uppercase tracking-wider">Created</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-divider-subtle uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-divider-subtle/20">
                    @forelse($users as $user)
                    <tr class="hover:bg-background-light/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-primary/10 p-2 rounded-lg">
                                    <span class="material-symbols-outlined text-primary">person</span>
                                </div>
                                <div>
                                    <p class="font-medium text-text-main">{{ $user->name }}</p>
                                    <p class="text-sm text-divider-subtle">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-divider-subtle">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @php
                                $roleName = is_string($user->role) ? $user->role : ($user->role ? $user->role->role_name : 'Unknown');
                                $roleColor = match($roleName) {
                                    'super_admin' => 'bg-purple-100 text-purple-700',
                                    'admin' => 'bg-primary/10 text-primary',
                                    'section_staff' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColor }}">
                                {{ ucfirst(str_replace('_', ' ', $roleName)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-divider-subtle">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 hover:bg-background-light rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-divider-subtle text-lg">edit</span>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 hover:bg-red-50 rounded-lg transition-colors" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <span class="material-symbols-outlined text-red-500 text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="bg-background-light p-4 rounded-full">
                                    <span class="material-symbols-outlined text-divider-subtle text-3xl">person_off</span>
                                </div>
                                <p class="text-divider-subtle">No users found</p>
                                <a href="#" class="text-primary hover:text-primary/80 text-sm font-medium">Create your first user</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-6 border-t border-divider-subtle/30 flex items-center justify-between">
            <p class="text-sm text-divider-subtle">Showing {{ $users->count() }} user{{ $users->count() !== 1 ? 's' : '' }}</p>
        </div>
    </div>
</div>
@endsection
