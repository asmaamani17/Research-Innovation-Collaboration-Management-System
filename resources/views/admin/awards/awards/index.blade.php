@extends('layouts.admin')

@section('title', 'Awards Management')

@php
    $awardStyles = [
        'GOLD' => ['text' => 'text-yellow-600', 'icon' => 'text-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-700'],
        'SILVER' => ['text' => 'text-gray-600', 'icon' => 'text-gray-400', 'badge' => 'bg-gray-100 text-gray-700'],
        'BRONZE' => ['text' => 'text-orange-700', 'icon' => 'text-orange-600', 'badge' => 'bg-orange-100 text-orange-700'],
        'SPECIAL' => ['text' => 'text-primary', 'icon' => 'text-primary', 'badge' => 'bg-primary/10 text-primary'],
        'PLATINUM' => ['text' => 'text-cyan-700', 'icon' => 'text-cyan-500', 'badge' => 'bg-cyan-100 text-cyan-700'],
    ];
@endphp

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Awards Management
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Manage real award records from staff, projects, and events.
        </p>
    </div>
    <a href="{{ route('admin.awards.create') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        <span>Add Award</span>
    </a>
</header>

<div class="px-8 pb-8">
    <!-- Session Messages -->
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 mb-4">
            <p class="font-bold mb-1">Please fix these fields:</p>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" id="search" placeholder="Search awards..." 
                    class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                    value="{{ request('search') }}">
            </div>
            <div class="min-w-[150px]">
                <select id="year" class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                    <option value="">All Years</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <select id="level" class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                    <option value="">All Levels</option>
                    @foreach($awardLevels as $level)
                        <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                    @endforeach
                </select>
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
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle">Staff Name</th>
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle">Faculty</th>
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle">Project Title</th>
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle text-center">Award</th>
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle">Level</th>
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle">Type</th>
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle">Event Name</th>
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle">Exhibition Level</th>
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle">Award Date</th>
                            <th class="px-6 py-4 text-sm uppercase font-bold text-text-main border-b border-divider-subtle text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($awards as $award)
                            @php
                                $awardKey = strtoupper($award->award_name ?? 'AWARD');
                                $style = $awardStyles[$awardKey] ?? ['text' => 'text-text-main', 'icon' => 'text-primary', 'badge' => 'bg-primary/10 text-primary'];
                                $staffName = $award->staff->staff_name ?? 'Unknown Staff';
                            @endphp
                            <tr class="hover:bg-background-light/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 min-w-[220px]">
                                        <div class="size-8 rounded-full bg-gray-200 bg-cover shrink-0"
                                            style="background-image: url('https://ui-avatars.com/api/?name={{ rawurlencode($staffName) }}&background=184290&color=fff')">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-text-main">{{ $staffName }}</p>
                                            <p class="text-xs text-divider-subtle">{{ $award->staff->staff_id ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded bg-primary/10 text-primary text-[10px] font-bold">
                                        {{ $award->staff->faculty->faculty_code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80 min-w-[240px]">
                                    {{ $award->project->project_title ?? 'Unknown Project' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined {{ $style['icon'] }} text-lg">emoji_events</span>
                                        <span class="text-sm font-bold {{ $style['text'] }}">{{ $award->award_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-[10px] font-bold">{{ $award->award_level }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">{{ $award->award_type }}</td>
                                <td class="px-6 py-4 text-sm text-text-main/80 min-w-[260px]">
                                    {{ $award->event->event_name ?? 'Unknown Event' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded {{ strtoupper($award->event->exhibition_level ?? '') === 'INTERNATIONAL' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }} text-[10px] font-bold">
                                        {{ $award->event->exhibition_level ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80 whitespace-nowrap">
                                    {{ optional($award->award_date)->format('M d, Y') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($award->evidence_document)
                                            <a href="{{ asset('storage/' . $award->evidence_document) }}" target="_blank"
                                                class="size-8 rounded flex items-center justify-center text-blue-500 hover:bg-blue-50 transition-colors"
                                                title="View Evidence">
                                                <span class="material-symbols-outlined text-xl">link</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.awards.show', $award->id) }}" class="size-8 rounded flex items-center justify-center text-blue-500 hover:bg-blue-50 transition-colors" title="View">
                                            <span class="material-symbols-outlined text-xl">visibility</span>
                                        </a>
                                        <a href="{{ route('admin.awards.edit', $award->id) }}" class="size-8 rounded flex items-center justify-center text-primary hover:bg-primary/10 transition-colors" title="Edit">
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.awards.destroy', $award->id) }}"
                                            onsubmit="return confirm('Delete this award record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="size-8 rounded flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center text-sm text-divider-subtle">
                                    No award records found.
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
                        Showing {{ $awards->firstItem() ?? 0 }} to {{ $awards->lastItem() ?? 0 }} of {{ $awards->total() }} results
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $awards->links() }}
                    </div>
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
    const year = document.getElementById('year').value;
    const level = document.getElementById('level').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (year) params.append('year', year);
    if (level) params.append('level', level);
    
    window.location.href = `{{ route('admin.awards') }}?${params.toString()}`;
}

function clearFilters() {
    window.location.href = '{{ route('admin.awards') }}';
}
</script>
@endpush
