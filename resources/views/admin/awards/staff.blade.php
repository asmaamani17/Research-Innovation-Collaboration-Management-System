@extends('layouts.admin')

@section('title', 'Staff Management')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                Staff Management
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Manage staff members and their award records.
            </p>
        </div>
        <button type="button" onclick="openAddStaffModal()"
            class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>Add Staff</span>
        </button>
    </header>

    <div class="px-8 space-y-4">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Table Controls -->
        <form method="GET" action="{{ route('admin.staff.index') }}" class="mb-4">
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-2 flex items-center gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-divider-subtle">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input 
                        type="text"
                        name="search" 
                        value="{{ request('search') }}"
                        class="w-full h-11 pl-12 pr-4 bg-background-light border-none rounded-lg focus:ring-2 focus:ring-primary text-sm text-text-main placeholder:text-divider-subtle" 
                        placeholder="Search staff by name or ID..." 
                    />
                </div>
                <div class="w-48">
                    <select name="faculty_id" class="w-full h-11 px-4 bg-background-light border-none rounded-lg focus:ring-2 focus:ring-primary text-sm text-text-main">
                        <option value="">All Faculties</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->faculty_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="h-11 px-4 flex items-center gap-2 border border-divider-subtle/40 rounded-lg text-sm font-medium text-text-main hover:bg-background-light transition-colors">
                    <span class="material-symbols-outlined text-lg">filter_list</span>
                    Filter
                </button>
                @if(request('search') || request('faculty_id'))
                    <a href="{{ route('admin.staff.index') }}" class="h-11 px-4 flex items-center gap-2 border border-divider-subtle/40 rounded-lg text-sm font-medium text-text-main hover:bg-background-light transition-colors">
                        <span class="material-symbols-outlined text-lg">clear</span>
                        Clear
                    </a>
                @endif
            </div>
        </form>

        <!-- Data Table Section -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-background-light">
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">STAFF ID</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">STAFF NAME</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">FACULTY</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">AWARDS</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($staff as $staffMember)
                            <tr class="hover:bg-background-light/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-primary">{{ $staffMember->staff_id ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                            {{ strtoupper(substr($staffMember->staff_name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-medium text-text-main">{{ $staffMember->staff_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $staffMember->faculty->faculty_name ?? 'No Faculty' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($staffMember->awards_count > 0)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                            {{ $staffMember->awards_count }} awards
                                        </span>
                                    @else
                                        <span class="text-xs text-divider-subtle">No awards</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.staff.show', $staffMember->id) }}" class="size-8 rounded flex items-center justify-center text-blue-500 hover:bg-blue-50/10 transition-colors" title="View Details">
                                            <span class="material-symbols-outlined text-xl">visibility</span>
                                        </a>
                                        <button 
                                            type="button"
                                            onclick="editStaff(this)"
                                            data-id="{{ $staffMember->id }}"
                                            data-staff-id="{{ $staffMember->staff_id }}"
                                            data-staff-name="{{ e($staffMember->staff_name) }}"
                                            data-faculty-id="{{ $staffMember->faculty_id }}"
                                            class="size-8 rounded flex items-center justify-center text-primary hover:bg-primary/10 transition-colors" 
                                            title="Edit"
                                        >
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </button>
                                        <form 
                                            action="{{ route('admin.staff.destroy', $staffMember->id) }}" 
                                            method="POST" 
                                            class="inline swal-delete-form"
                                            data-swal-title="Delete staff member?"
                                            data-swal-text="Are you sure you want to delete {{ $staffMember->staff_name }}? Staff with award records cannot be deleted."
                                            data-swal-confirm="Yes, delete"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="size-8 rounded flex items-center justify-center text-red-500 hover:bg-red-50/10 transition-colors" title="Delete">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-divider-subtle">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-4xl">person_off</span>
                                        <span>No staff members found</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-divider-subtle/30">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-divider-subtle">
                        Showing {{ $staff->firstItem() ?? 0 }} to {{ $staff->lastItem() ?? 0 }} of {{ $staff->total() }} results
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $staff->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div id="addStaffModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-divider-subtle/30">
                <h3 class="text-lg font-bold text-text-main">Add New Staff</h3>
                <button type="button" onclick="closeAddStaffModal()" class="text-divider-subtle hover:text-text-main">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('admin.staff.store') }}" method="POST" id="addStaffForm">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-text-main mb-2">Staff ID</label>
                        <input type="text" name="staff_id" required
                            class="w-full h-11 px-4 bg-background-light border border-divider-subtle/40 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                            placeholder="Enter staff ID">
                        @error('staff_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-main mb-2">Staff Name</label>
                        <input type="text" name="staff_name" required
                            class="w-full h-11 px-4 bg-background-light border border-divider-subtle/40 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                            placeholder="Enter staff name">
                        @error('staff_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-main mb-2">Faculty</label>
                        <select name="faculty_id" required
                            class="w-full h-11 px-4 bg-background-light border border-divider-subtle/40 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                            <option value="">Select Faculty</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->faculty_name }}</option>
                            @endforeach
                        </select>
                        @error('faculty_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-divider-subtle/30">
                    <button type="button" onclick="closeAddStaffModal()"
                        class="h-11 px-6 rounded-lg border border-divider-subtle/40 text-sm font-medium text-text-main hover:bg-background-light transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="h-11 px-6 rounded-lg bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-colors">
                        Add Staff
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div id="editStaffModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-divider-subtle/30">
                <h3 class="text-lg font-bold text-text-main">Edit Staff</h3>
                <button type="button" onclick="closeEditStaffModal()" class="text-divider-subtle hover:text-text-main">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="" method="POST" id="editStaffForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_staff_id">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-text-main mb-2">Staff ID</label>
                        <input type="text" name="staff_id" id="edit_staff_code" readonly
                            class="w-full h-11 px-4 bg-background-light border border-divider-subtle/40 rounded-lg text-sm text-divider-subtle cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-main mb-2">Staff Name</label>
                        <input type="text" name="staff_name" id="edit_staff_name" required
                            class="w-full h-11 px-4 bg-background-light border border-divider-subtle/40 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        @error('staff_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-main mb-2">Faculty</label>
                        <select name="faculty_id" id="edit_faculty_id" required
                            class="w-full h-11 px-4 bg-background-light border border-divider-subtle/40 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                            <option value="">Select Faculty</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->faculty_name }}</option>
                            @endforeach
                        </select>
                        @error('faculty_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-divider-subtle/30">
                    <button type="button" onclick="closeEditStaffModal()"
                        class="h-11 px-6 rounded-lg border border-divider-subtle/40 text-sm font-medium text-text-main hover:bg-background-light transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="h-11 px-6 rounded-lg bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-colors">
                        Update Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const staffUpdateUrlTemplate = @json(route('admin.staff.update', ['id' => '__ID__']));
    const staffStoreUrl = @json(route('admin.staff.store'));

    function openAddStaffModal() {
        document.getElementById('addStaffModal').classList.remove('hidden');
        document.getElementById('addStaffForm').reset();
        document.body.style.overflow = 'hidden';
    }

    function closeAddStaffModal() {
        document.getElementById('addStaffModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openEditStaffModal() {
        document.getElementById('editStaffModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditStaffModal() {
        document.getElementById('editStaffModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function editStaff(button) {
        const id = button.dataset.id;
        const form = document.getElementById('editStaffForm');
        form.action = staffUpdateUrlTemplate.replace('__ID__', id);
        document.getElementById('edit_staff_id').value = id;
        document.getElementById('edit_staff_code').value = button.dataset.staffId || '';
        document.getElementById('edit_staff_name').value = button.dataset.staffName || '';
        document.getElementById('edit_faculty_id').value = button.dataset.facultyId || '';
        openEditStaffModal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->has('staff_id') || $errors->has('staff_name') || $errors->has('faculty_id'))
            @if(old('_method') === 'PUT')
                openEditStaffModal();
            @else
                openAddStaffModal();
            @endif
        @endif
    });
</script>
@endpush
