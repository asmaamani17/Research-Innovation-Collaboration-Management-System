@extends('layouts.admin')

@section('title', 'Faculty Management')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                Faculty Management
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Manage faculties and their staff/award records.
            </p>
        </div>
        <button type="button" onclick="openAddFacultyModal()"
            class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>Add Faculty</span>
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
        <form method="GET" action="{{ route('admin.faculty.index') }}" class="mb-4">
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
                        placeholder="Search faculties by name or code..." 
                    />
                </div>
                <button type="submit" class="h-11 px-4 flex items-center gap-2 border border-divider-subtle/40 rounded-lg text-sm font-medium text-text-main hover:bg-background-light transition-colors">
                    <span class="material-symbols-outlined text-lg">filter_list</span>
                    Filter
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.faculty.index') }}" class="h-11 px-4 flex items-center gap-2 border border-divider-subtle/40 rounded-lg text-sm font-medium text-text-main hover:bg-background-light transition-colors">
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
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">FACULTY CODE</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">FACULTY NAME</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">STAFF</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">AWARDS</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($faculties as $faculty)
                            <tr class="hover:bg-background-light/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-primary">{{ $faculty->faculty_code ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                            {{ strtoupper(substr($faculty->faculty_name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-medium text-text-main">{{ $faculty->faculty_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($faculty->staff_count > 0)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                            {{ $faculty->staff_count }} staff
                                        </span>
                                    @else
                                        <span class="text-xs text-divider-subtle">No staff</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($faculty->awards_count > 0)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                            {{ $faculty->awards_count }} awards
                                        </span>
                                    @else
                                        <span class="text-xs text-divider-subtle">No awards</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.faculty.show', $faculty->id) }}" class="size-8 rounded flex items-center justify-center text-blue-500 hover:bg-blue-50/10 transition-colors" title="View Details">
                                            <span class="material-symbols-outlined text-xl">visibility</span>
                                        </a>
                                        <button 
                                            type="button"
                                            onclick="editFaculty(this)"
                                            data-id="{{ $faculty->id }}"
                                            data-faculty-code="{{ $faculty->faculty_code }}"
                                            data-faculty-name="{{ e($faculty->faculty_name) }}"
                                            class="size-8 rounded flex items-center justify-center text-primary hover:bg-primary/10 transition-colors" 
                                            title="Edit"
                                        >
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </button>
                                        <form 
                                            action="{{ route('admin.faculty.destroy', $faculty->id) }}" 
                                            method="POST" 
                                            class="inline swal-delete-form"
                                            data-swal-title="Delete faculty?"
                                            data-swal-text="Are you sure you want to delete {{ $faculty->faculty_name }}? Faculties with staff members cannot be deleted."
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
                                        <span class="material-symbols-outlined text-4xl">school</span>
                                        <span>No faculties found</span>
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
                        Showing {{ $faculties->firstItem() ?? 0 }} to {{ $faculties->lastItem() ?? 0 }} of {{ $faculties->total() }} results
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $faculties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const facultyUpdateUrlTemplate = @json(route('admin.faculty.update', ['id' => '__ID__']));
    const facultyStoreUrl = @json(route('admin.faculty.store'));

    function openAddFacultyModal() {
        document.getElementById('addFacultyModal').classList.remove('hidden');
        document.getElementById('addFacultyForm').reset();
        document.body.style.overflow = 'hidden';
    }

    function closeAddFacultyModal() {
        document.getElementById('addFacultyModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openEditFacultyModal() {
        document.getElementById('editFacultyModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditFacultyModal() {
        document.getElementById('editFacultyModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function editFaculty(button) {
        const id = button.dataset.id;
        const form = document.getElementById('editFacultyForm');
        form.action = facultyUpdateUrlTemplate.replace('__ID__', id);
        document.getElementById('edit_faculty_id').value = id;
        document.getElementById('edit_faculty_code').value = button.dataset.facultyCode || '';
        document.getElementById('edit_faculty_name').value = button.dataset.facultyName || '';
        openEditFacultyModal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->has('faculty_code') || $errors->has('faculty_name'))
            @if(old('_method') === 'PUT')
                openEditFacultyModal();
            @else
                openAddFacultyModal();
            @endif
        @endif
    });
</script>
@endpush
