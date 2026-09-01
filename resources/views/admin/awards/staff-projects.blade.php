@extends('layouts.admin')

@section('title', 'Staff Projects')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                {{ $staff->staff_name }} - Projects
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                All projects associated with this staff member.
            </p>
        </div>
        <a href="{{ route('admin.staff.show', $staff->id) }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-white border border-divider-subtle/40 text-text-main text-sm font-bold shadow-sm hover:bg-background-light transition-all">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Back to Staff</span>
        </a>
    </header>

    <div class="px-8 pb-8">
        <!-- Data Table Section -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-background-light">
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">PROJECT ID</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">GRANT NO</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">PROJECT TITLE</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">AWARDS</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($projects as $project)
                            <tr class="hover:bg-background-light/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-primary">{{ $project->project_id ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $project->grant_no ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $project->project_title }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($project->awards_count > 0)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                            {{ $project->awards_count }} awards
                                        </span>
                                    @else
                                        <span class="text-xs text-divider-subtle">No awards</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.projects.show', $project->id) }}" class="size-8 rounded flex items-center justify-center text-blue-500 hover:bg-blue-50/10 transition-colors" title="View Details">
                                            <span class="material-symbols-outlined text-xl">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-divider-subtle">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-4xl">science</span>
                                        <span>No projects found</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
