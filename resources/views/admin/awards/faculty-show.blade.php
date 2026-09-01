@extends('layouts.admin')

@section('title', 'Faculty Details')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                Faculty Details
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                View faculty information, staff members, and award history.
            </p>
        </div>
        <a href="{{ route('admin.faculty.index') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-white border border-divider-subtle/40 text-text-main text-sm font-bold shadow-sm hover:bg-background-light transition-all">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Back to Faculties</span>
        </a>
    </header>

    <div class="px-8 pb-8 space-y-6">
        <!-- Faculty Info Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-start gap-6">
                <div class="size-20 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-2xl">
                    {{ strtoupper(substr($faculty->faculty_name, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-text-main">{{ $faculty->faculty_name }}</h3>
                    <div class="flex items-center gap-4 mt-2 text-sm text-divider-subtle">
                        <span class="font-medium text-primary">{{ $faculty->faculty_code ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button 
                        type="button"
                        onclick="editFaculty(this)"
                        data-id="{{ $faculty->id }}"
                        data-faculty-code="{{ $faculty->faculty_code }}"
                        data-faculty-name="{{ e($faculty->faculty_name) }}"
                        class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
                    >
                        <span class="material-symbols-outlined text-sm">edit</span>
                        <span>Edit</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Total Staff</div>
                <div class="text-3xl font-bold text-primary">{{ $stats['total_staff'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Total Awards</div>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['total_awards'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Gold</div>
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['gold_awards'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Silver</div>
                <div class="text-3xl font-bold text-gray-600">{{ $stats['silver_awards'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Bronze</div>
                <div class="text-3xl font-bold text-orange-700">{{ $stats['bronze_awards'] }}</div>
            </div>
        </div>

        <!-- Top Staff Section -->
        @if($top_staff->count() > 0)
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Top Performing Staff</h3>
                </div>
                <div class="divide-y divide-divider-subtle/20">
                    @foreach($top_staff as $staffMember)
                        <div class="px-6 py-4 hover:bg-background-light/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                        {{ strtoupper(substr($staffMember->staff_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.staff.show', $staffMember->id) }}" class="text-sm font-semibold text-text-main hover:text-primary transition-colors">
                                            {{ $staffMember->staff_name }}
                                        </a>
                                        <div class="text-xs text-divider-subtle">{{ $staffMember->staff_id ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                        {{ $staffMember->awards_count }} awards
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Staff Members Section -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-main">Staff Members ({{ $faculty->staff->count() }})</h3>
                <a href="{{ route('admin.faculty.staff', $faculty->id) }}" class="text-sm text-primary hover:underline">View All</a>
            </div>
            @if($faculty->staff->count() > 0)
                <div class="divide-y divide-divider-subtle/20">
                    @foreach($faculty->staff->take(5) as $staffMember)
                        <div class="px-6 py-4 hover:bg-background-light/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                        {{ strtoupper(substr($staffMember->staff_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.staff.show', $staffMember->id) }}" class="text-sm font-semibold text-text-main hover:text-primary transition-colors">
                                            {{ $staffMember->staff_name }}
                                        </a>
                                        <div class="text-xs text-divider-subtle">{{ $staffMember->staff_id ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($staffMember->awards_count > 0)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                            {{ $staffMember->awards_count }} awards
                                        </span>
                                    @else
                                        <span class="text-xs text-divider-subtle">No awards</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-8 text-center text-divider-subtle">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-4xl">person_off</span>
                        <span>No staff members</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent Awards Section -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-main">Recent Awards ({{ $faculty->awards->count() }})</h3>
                <a href="{{ route('admin.faculty.awards', $faculty->id) }}" class="text-sm text-primary hover:underline">View All</a>
            </div>
            @if($faculty->awards->count() > 0)
                <div class="divide-y divide-divider-subtle/20">
                    @foreach($faculty->awards->take(5) as $award)
                        <div class="px-6 py-4 hover:bg-background-light/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $award->level_color }}">
                                            {{ $award->award_level }}
                                        </span>
                                        <h4 class="text-sm font-semibold text-text-main">{{ $award->award_name }}</h4>
                                    </div>
                                    <div class="flex items-center gap-4 mt-2 text-sm text-divider-subtle">
                                        <span>{{ $award->staff->staff_name }}</span>
                                        <span>•</span>
                                        @if($award->project)
                                            <span>{{ $award->project->project_title }}</span>
                                            <span>•</span>
                                        @endif
                                        <span>{{ $award->award_date ? \Carbon\Carbon::parse($award->award_date)->format('M d, Y') : 'N/A' }}</span>
                                    </div>
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
@endsection

@push('scripts')
<script>
    const facultyUpdateUrlTemplate = @json(route('admin.faculty.update', ['id' => '__ID__']));

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
        @if($errors->any())
            openEditFacultyModal();
        @endif
    });
</script>
@endpush
