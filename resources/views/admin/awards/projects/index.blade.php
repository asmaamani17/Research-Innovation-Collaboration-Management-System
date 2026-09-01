@extends('layouts.admin')

@php
use App\Models\Project;
@endphp

@section('title', 'Projects Management')

@section('content')
        <!-- Page Header -->
        <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col">
                <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                    Projects Management
                </h2>
                <p class="text-divider-subtle font-medium text-sm mt-1">
                    Manage research projects and innovations submitted to competitions.
                </p>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>Add Project</span>
            </a>
        </header>

        <div class="px-8 pb-8">
            <!-- Filters -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" id="search" placeholder="Search projects..." 
                            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                            value="{{ request('search') }}">
                    </div>
                    <button onclick="applyFilters()" class="px-6 py-3 bg-primary text-white rounded-lg font-bold hover:bg-primary/90 transition-all">
                        Filter
                    </button>
                    <button onclick="clearFilters()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition-all">
                        Clear
                    </button>
                </div>
            </div>

            <!-- Data Table Section -->
            <div class="flex-1">
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm flex flex-col">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-background-light">
                                <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">PROJECT ID</th>
                                <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">GRANT NO</th>
                                <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">PROJECT TITLE</th>
                                <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">STAFF MEMBERS</th>
                                <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">AWARDS</th>
                                <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">STATUS</th>
                                <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-right">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-divider-subtle/20">
                            @forelse($projects as $project)
                                <tr class="hover:bg-background-light/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-primary">{{ $project->project_id ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-text-main/80">{{ $project->grant_no ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-text-main/80">{{ $project->project_title }}</td>
                                    <td class="px-6 py-4">
                                        @if($project->staff_members && $project->staff_members->count() > 0)
                                            <div class="flex items-center gap-3">
                                                <div>
                                                    <span class="text-sm text-text-main">{{ $project->staff_members->first()->staff_name }}</span>
                                                    @if($project->staff_members->count() > 1)
                                                        <div class="text-xs text-divider-subtle">+{{ $project->staff_members->count() - 1 }} more</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-divider-subtle">No staff assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($project->awards_count > 0)
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">{{ $project->awards_count }} awards</span>
                                        @else
                                            <span class="text-xs text-divider-subtle">No awards</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase bg-green-100 text-green-700">Active</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.projects.show', $project->id) }}" class="size-8 rounded flex items-center justify-center text-blue-500 hover:bg-blue-50/10 transition-colors" title="View Details">
                                                <span class="material-symbols-outlined text-xl">visibility</span>
                                            </a>
                                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="size-8 rounded flex items-center justify-center text-primary hover:bg-primary/10 transition-colors" title="Edit">
                                                <span class="material-symbols-outlined text-xl">edit</span>
                                            </a>
                                            <form 
                                                action="{{ route('admin.projects.destroy', $project->id) }}" 
                                                method="POST" 
                                                class="inline swal-delete-form"
                                                data-swal-title="Delete project?"
                                                data-swal-text="Are you sure you want to delete {{ $project->project_title }}? Projects with award records cannot be deleted."
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
                                    <td colspan="7" class="px-6 py-8 text-center text-divider-subtle">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="material-symbols-outlined text-4xl">science_off</span>
                                            <span>No projects found</span>
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
                            Showing {{ $projects->firstItem() ?? 0 }} to {{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }} results
                        </div>
                        <div class="flex items-center gap-2">
                            {{ $projects->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
<script>
function applyFilters() {
    const search = document.getElementById('search').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    
    window.location.href = `{{ route('admin.projects.index') }}?${params.toString()}`;
}

function clearFilters() {
    window.location.href = '{{ route('admin.projects.index') }}';
}
</script>
@endpush
