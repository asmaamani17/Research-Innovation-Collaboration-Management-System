@extends('layouts.admin')

@section('title', 'Faculty Awards')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                {{ $faculty->faculty_name }} - Awards
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                View all awards for this faculty.
            </p>
        </div>
        <a href="{{ route('admin.faculty.show', $faculty->id) }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-white border border-divider-subtle/40 text-text-main text-sm font-bold shadow-sm hover:bg-background-light transition-all">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Back to Faculty</span>
        </a>
    </header>

    <div class="px-8 pb-8">
        <!-- Data Table Section -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-background-light">
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">AWARD NAME</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">LEVEL</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">STAFF</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">PROJECT</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">DATE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($awards as $award)
                            <tr class="hover:bg-background-light/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-text-main">{{ $award->award_name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $award->level_color }}">
                                        {{ $award->award_level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $award->staff->staff_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $award->project->project_title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $award->award_date ? \Carbon\Carbon::parse($award->award_date)->format('M d, Y') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-divider-subtle">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-4xl">emoji_events</span>
                                        <span>No awards found</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($awards->hasPages())
                <div class="px-6 py-4 border-t border-divider-subtle/30">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-divider-subtle">
                            Showing {{ $awards->firstItem() ?? 0 }} to {{ $awards->lastItem() ?? 0 }} of {{ $awards->total() }} results
                        </div>
                        <div class="flex items-center gap-2">
                            {{ $awards->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
