@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <div class="flex items-center gap-2 text-sm text-divider-subtle mb-1">
            <a href="{{ route('admin.users.index') }}" class="hover:text-primary transition-colors">Users</a>
            <span>/</span>
            <span class="text-text-main font-medium">Edit User</span>
        </div>
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Edit User: {{ $user->name }}
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Update account information and management module access.
        </p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="flex cursor-pointer items-center justify-center gap-2 rounded-lg h-10 px-4 bg-background-light border border-divider-subtle/30 text-text-main text-sm font-semibold hover:bg-gray-100 transition-all">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Back to Users</span>
    </a>
</header>

<div class="px-8 pb-12">
    <div class="max-w-4xl bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-divider-subtle/30">
            <h3 class="text-lg font-bold text-text-main">User Account & Management Modules</h3>
            <p class="text-xs text-divider-subtle mt-0.5">Modify user details and select multiple management modules for this admin.</p>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">Full Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 bg-background-light border border-divider-subtle/30 rounded-lg text-sm focus:outline-none focus:border-primary text-text-main"
                        placeholder="e.g. Dr. Ahmad Ali">
                </div>

                <!-- IC Number -->
                <div>
                    <label for="ic" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">IC Number *</label>
                    <input type="text" name="ic" id="ic" value="{{ old('ic', $user->ic) }}" required
                        class="w-full px-4 py-2.5 bg-background-light border border-divider-subtle/30 rounded-lg text-sm focus:outline-none focus:border-primary text-text-main"
                        placeholder="e.g. 850101045231">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">Email Address *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 bg-background-light border border-divider-subtle/30 rounded-lg text-sm focus:outline-none focus:border-primary text-text-main"
                        placeholder="e.g. ahmad@utem.edu.my">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">New Password (Leave blank to keep existing)</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-2.5 bg-background-light border border-divider-subtle/30 rounded-lg text-sm focus:outline-none focus:border-primary text-text-main"
                        placeholder="Leave blank to keep current password">
                </div>

                <!-- System Role -->
                <div>
                    <label for="role_id" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">System Role *</label>
                    <select name="role_id" id="role_id" required
                        class="w-full px-4 py-2.5 bg-background-light border border-divider-subtle/30 rounded-lg text-sm focus:outline-none focus:border-primary text-text-main">
                        <option value="">Select System Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->role_name)) }} - {{ $role->description }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Account Status -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-2.5 bg-background-light border border-divider-subtle/30 rounded-lg text-sm focus:outline-none focus:border-primary text-text-main">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Management Module Assignment (Multi-Select) -->
            <div class="pt-4 border-t border-divider-subtle/20">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="block text-sm font-bold text-text-main">Management Module Access</label>
                        <p class="text-xs text-divider-subtle">Check all management sections this user/admin is authorized to manage.</p>
                    </div>
                    <button type="button" id="selectAllModules" class="text-xs font-semibold text-primary hover:underline">Select All</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-background-light p-4 rounded-xl border border-divider-subtle/20">
                    @foreach($modules as $module)
                        <label class="flex items-center gap-3 p-3 bg-white border border-divider-subtle/30 rounded-lg hover:border-primary/50 cursor-pointer transition-all">
                            <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                                class="module-checkbox w-4 h-4 text-primary rounded border-divider-subtle focus:ring-primary"
                                {{ in_array($module->id, old('modules', $userModuleIds)) ? 'checked' : '' }}>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-text-main">{{ $module->module_name }}</span>
                                <span class="text-xs text-divider-subtle">Management Access</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-divider-subtle/20">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-lg border border-divider-subtle/30 text-sm font-semibold text-text-main hover:bg-gray-50 transition-all">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-white text-sm font-bold shadow-md hover:bg-primary/90 transition-all">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('selectAllModules')?.addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.module-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    this.textContent = allChecked ? 'Select All' : 'Deselect All';
});
</script>
@endsection
