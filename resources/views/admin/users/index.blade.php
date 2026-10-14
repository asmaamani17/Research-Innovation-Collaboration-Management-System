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
            Manage system users, roles, and assign single or multiple management modules.
        </p>
    </div>
    <a href="{{ route('admin.users.create') }}"
        class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        <span>New User</span>
    </a>
</header>

<div class="px-8 space-y-4 pb-12">
    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-green-600">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-red-600">error</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    <!-- Table Controls / Filter Bar (Just like Staff Table) -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-2 flex flex-wrap items-center gap-4">
            <!-- Search Input -->
            <div class="flex-1 min-w-[240px] relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-divider-subtle">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <input 
                    type="text"
                    name="search" 
                    value="{{ request('search') }}"
                    class="w-full h-11 pl-12 pr-4 bg-background-light border-none rounded-lg focus:ring-2 focus:ring-primary text-sm text-text-main placeholder:text-divider-subtle" 
                    placeholder="Search user by name, IC or email..." 
                />
            </div>

            <!-- Role Filter -->
            <div class="w-48">
                <select name="role" class="w-full h-11 px-4 bg-background-light border-none rounded-lg focus:ring-2 focus:ring-primary text-sm text-text-main">
                    <option value="">All System Roles</option>
                    @foreach($roles as $roleOption)
                        <option value="{{ $roleOption->role_name }}" {{ request('role') === $roleOption->role_name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $roleOption->role_name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Module Filter -->
            <div class="w-52">
                <select name="module_id" class="w-full h-11 px-4 bg-background-light border-none rounded-lg focus:ring-2 focus:ring-primary text-sm text-text-main">
                    <option value="">All Management Modules</option>
                    @foreach($modules as $moduleOption)
                        <option value="{{ $moduleOption->id }}" {{ request('module_id') == $moduleOption->id ? 'selected' : '' }}>
                            {{ $moduleOption->module_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Filter Button -->
            <button type="submit" class="h-11 px-4 flex items-center gap-2 border border-divider-subtle/40 rounded-lg text-sm font-medium text-text-main hover:bg-background-light transition-colors">
                <span class="material-symbols-outlined text-lg">filter_list</span>
                Filter
            </button>

            @if(request('search') || request('role') || request('module_id'))
                <a href="{{ route('admin.users.index') }}" class="h-11 px-4 flex items-center gap-2 border border-divider-subtle/40 rounded-lg text-sm font-medium text-text-main hover:bg-background-light transition-colors">
                    <span class="material-symbols-outlined text-lg">clear</span>
                    Clear
                </a>
            @endif
        </div>
    </form>

    <!-- Data Table Section (Identical styling to Staff Table) -->
    <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-background-light">
                        <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">USER ID</th>
                        <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">USER NAME</th>
                        <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">EMAIL</th>
                        <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">ROLE</th>
                        <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">MANAGEMENT ACCESS</th>
                        <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">STATUS</th>
                        <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-divider-subtle/20">
                    @forelse($users as $user)
                        <tr class="hover:bg-background-light/50 transition-colors group">
                            <!-- User ID -->
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-primary">#{{ $user->id }}</span>
                            </td>

                            <!-- User Name & Avatar -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-text-main block">{{ $user->name }}</span>
                                        @if($user->ic)
                                            <span class="text-xs text-divider-subtle">IC: {{ $user->ic }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4 text-sm text-text-main/80">
                                {{ $user->email }}
                            </td>

                            <!-- Role -->
                            <td class="px-6 py-4">
                                @php
                                    $roleName = is_string($user->role) ? $user->role : ($user->role ? $user->role->role_name : 'Unknown');
                                    $roleBadge = match(strtolower($roleName)) {
                                        'superadmin' => 'bg-purple-100 text-purple-700',
                                        'admin' => 'bg-primary/10 text-primary',
                                        'staff' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $roleBadge }}">
                                    {{ ucfirst(str_replace('_', ' ', $roleName)) }}
                                </span>
                            </td>

                            <!-- Management Access (Modules) -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @if(strtolower($roleName) === 'superadmin')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-700 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">verified</span>
                                            All Management Access
                                        </span>
                                    @elseif($user->modules && $user->modules->count() > 0)
                                        @foreach($user->modules as $mod)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                                {{ $mod->module_name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-xs text-divider-subtle italic">No modules assigned</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($user->status ?? 'active') }}
                                </span>
                            </td>

                            <!-- Actions (Identical buttons to Staff Table) -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                        class="size-8 rounded flex items-center justify-center text-primary hover:bg-primary/10 transition-colors" 
                                        title="Edit User">
                                        <span class="material-symbols-outlined text-xl">edit</span>
                                    </a>

                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="size-8 rounded flex items-center justify-center text-red-500 hover:bg-red-50/10 transition-colors" 
                                                title="Delete User" 
                                                onclick="return confirm('Are you sure you want to delete {{ addslashes($user->name) }}?')">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-divider-subtle">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-4xl">person_off</span>
                                    <span>No users found</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Section (Matching Staff Table) -->
        <div class="px-6 py-4 border-t border-divider-subtle/30">
            <div class="flex items-center justify-between">
                <div class="text-sm text-divider-subtle">
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                </div>
                <div class="flex items-center gap-2">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
