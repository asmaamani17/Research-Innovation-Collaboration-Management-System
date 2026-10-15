@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
@include('admin.users.partials.form-styles')

<div class="user-form">
<header class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h1 class="page-title">Edit User: {{ $user->name }}</h1>
        <p class="page-description">
            Update account information and management module access.
        </p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="back-link">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Back to Users</span>
    </a>
</header>

<div class="form-card">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="error-list">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-grid">
                <!-- Full Name -->
                <div class="form-field">
                    <label for="name" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">Full Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="user-control"
                        placeholder="e.g. Dr. Ahmad Ali">
                </div>

                <!-- IC Number -->
                <div class="form-field">
                    <label for="ic" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">IC Number *</label>
                    <input type="text" name="ic" id="ic" value="{{ old('ic', $user->ic) }}" required
                        class="user-control"
                        placeholder="e.g. 850101045231">
                </div>

                <!-- Email -->
                <div class="form-field">
                    <label for="email" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">Email Address *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="user-control"
                        placeholder="e.g. ahmad@utem.edu.my">
                </div>

                <!-- Password -->
                <div class="form-field">
                    <label for="password" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">New Password (Leave blank to keep existing)</label>
                    <input type="password" name="password" id="password" minlength="12"
                        class="user-control"
                        placeholder="At least 12 characters; leave blank to keep current password">
                </div>

                <!-- System Role -->
                <div class="form-field">
                    <label for="role_id" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">System Role *</label>
                    <select name="role_id" id="role_id" required
                        class="user-control">
                        <option value="">Select System Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->role_name)) }} - {{ $role->description }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Account Status -->
                <div class="form-field">
                    <label for="status" class="block text-xs font-semibold text-text-main uppercase tracking-wider mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="user-control">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Management Module Assignment (Multi-Select) -->
            <div class="module-access">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="block text-sm font-bold text-text-main">Management Module Access</label>
                        <p class="text-xs text-divider-subtle">Check all management sections this user/admin is authorized to manage.</p>
                    </div>
                    <button type="button" id="selectAllModules" class="text-xs font-semibold text-primary hover:underline">Select All</button>
                </div>

                <div class="module-grid">
                    @foreach($modules as $module)
                        <label class="module-option">
                            <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                                class="module-checkbox"
                                {{ in_array($module->id, old('modules', $userModuleIds)) ? 'checked' : '' }}>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-text-main">{{ $module->module_name }}</span>
                                <span class="text-xs text-divider-subtle">Management Access</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="button secondary">Cancel</a>
                <button type="submit" class="button">
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
