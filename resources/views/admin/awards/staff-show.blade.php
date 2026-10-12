@extends('layouts.admin')

@section('title', 'Staff Details')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                Staff Details
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                View staff member information and award history.
            </p>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-white border border-divider-subtle/40 text-text-main text-sm font-bold shadow-sm hover:bg-background-light transition-all">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Back to Staff</span>
        </a>
    </header>

    <div class="px-8 pb-8 space-y-6">
        <!-- Staff Info Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-start gap-6">
                <div class="size-20 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-2xl">
                    {{ strtoupper(substr($staff->staff_name, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-text-main">{{ $staff->staff_name }}</h3>
                    <div class="flex items-center gap-4 mt-2 text-sm text-divider-subtle">
                        <span class="font-medium text-primary">{{ $staff->staff_id ?? 'N/A' }}</span>
                        <span>•</span>
                        <span>{{ $staff->faculty->faculty_name ?? 'No Faculty' }}</span>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button 
                        type="button"
                        onclick="editStaff(this)"
                        data-id="{{ $staff->id }}"
                        data-staff-id="{{ $staff->staff_id }}"
                        data-staff-name="{{ e($staff->staff_name) }}"
                        data-faculty-id="{{ $staff->faculty_id }}"
                        class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
                    >
                        <span class="material-symbols-outlined text-sm">edit</span>
                        <span>Edit</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Total Awards</div>
                <div class="text-3xl font-bold text-primary">{{ $award_stats['total'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Gold</div>
                <div class="text-3xl font-bold text-yellow-600">{{ $award_stats['gold'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Silver</div>
                <div class="text-3xl font-bold text-gray-600">{{ $award_stats['silver'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Bronze</div>
                <div class="text-3xl font-bold text-orange-700">{{ $award_stats['bronze'] }}</div>
            </div>
        </div>

        <!-- Awards Section -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                <h3 class="text-lg font-bold text-text-main">Award History</h3>
            </div>
            @if($staff->awards->count() > 0)
                <div class="divide-y divide-divider-subtle/20">
                    @foreach($staff->awards as $award)
                        <div class="px-6 py-4 hover:bg-background-light/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase" style="background-color: {{ $award->level_color }}20; color: {{ $award->level_color }}">
                                            {{ $award->award_level }}
                                        </span>
                                        <h4 class="text-sm font-semibold text-text-main">{{ $award->award_name }}</h4>
                                    </div>
                                    <div class="flex items-center gap-4 mt-2 text-sm text-divider-subtle">
                                        @if($award->project)
                                            <span>{{ $award->project->project_title }}</span>
                                            <span>•</span>
                                        @endif
                                        @if($award->competition)
                                            <span>{{ $award->competition->event_name }}</span>
                                            <span>•</span>
                                        @endif
                                        @if($award->exhibitionResult)
                                            <span>{{ $award->exhibitionResult->Description }}</span>
                                            <span>•</span>
                                        @endif
                                        <span>{{ $award->award_date ? \Carbon\Carbon::parse($award->award_date)->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($award->evidence_document)
                                        <a href="{{ asset('storage/' . $award->evidence_document) }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-primary hover:underline text-sm">
                                            <span class="material-symbols-outlined text-lg">link</span>
                                            Evidence
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-8 text-center text-divider-subtle">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-4xl">emoji_events</span>
                        <span>No awards recorded</span>
                    </div>
                </div>
            @endif
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
                            @foreach($faculties ?? [] as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->faculty_name }}</option>
                            @endforeach
                        </select>
                        @error('faculty_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-divider-subtle/30 flex justify-end gap-3">
                    <button type="button" onclick="closeEditStaffModal()" class="px-4 py-2 rounded-lg border border-divider-subtle/40 text-sm font-semibold text-text-main hover:bg-background-light transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const staffUpdateUrlTemplate = @json(route('admin.staff.update', ['id' => '__ID__']));

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
        @if($errors->any())
            openEditStaffModal();
        @endif
    });
</script>
@endpush
