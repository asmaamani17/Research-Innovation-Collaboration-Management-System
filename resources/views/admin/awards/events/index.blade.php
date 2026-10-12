@extends('layouts.admin')

@section('title', 'Events Management')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                Events Management
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Manage research events and exhibitions
            </p>
        </div>
        <a href="{{ route('admin.event.create') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>New Event</span>
        </a>
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

        <!-- Filters -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" id="search" placeholder="Search events..." 
                        class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                        value="{{ request('search') }}">
                </div>
                <div class="min-w-[150px]">
                    <select id="level" class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                        <option value="">All Levels</option>
                        <option value="International" {{ request('level') == 'International' ? 'selected' : '' }}>International</option>
                        <option value="Regional" {{ request('level') == 'Regional' ? 'selected' : '' }}>Regional</option>
                        <option value="National" {{ request('level') == 'National' ? 'selected' : '' }}>National</option>
                        <option value="University" {{ request('level') == 'University' ? 'selected' : '' }}>University</option>
                    </select>
                </div>
                <div class="min-w-[150px]">
                    <select id="year" class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                        <option value="">All Years</option>
                        @foreach($availableYears ?? [] as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
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
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-background-light">
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">EVENT NAME</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">ORGANIZER</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">LEVEL</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">DATE</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">AWARDS</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">PARTICIPANTS</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($events as $event)
                            <tr class="hover:bg-background-light/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                            {{ strtoupper(substr($event->event_name, 0, 2)) }}
                                        </div>
                                        <a href="{{ route('admin.event.show', $event->id) }}" class="text-sm font-semibold text-primary hover:underline">
                                            {{ $event->event_name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $event->organizer }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-[10px] font-bold {{ $event->exhibition_level === 'International' ? 'bg-purple-100 text-purple-700' : ($event->exhibition_level === 'National' ? 'bg-blue-100 text-blue-700' : ($event->exhibition_level === 'Regional' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700')) }}">
                                        {{ $event->exhibition_level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($event->awards_count > 0)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                            {{ $event->awards_count }} awards
                                        </span>
                                    @else
                                        <span class="text-xs text-divider-subtle">No awards</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($event->unique_participants > 0)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                            {{ $event->unique_participants }} participants
                                        </span>
                                    @else
                                        <span class="text-xs text-divider-subtle">No participants</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.event.show', $event->id) }}" class="size-8 rounded flex items-center justify-center text-blue-500 hover:bg-blue-50/10 transition-colors" title="View Details">
                                            <span class="material-symbols-outlined text-xl">visibility</span>
                                        </a>
                                        <a href="{{ route('admin.event.edit', $event->id) }}" class="size-8 rounded flex items-center justify-center text-primary hover:bg-primary/10 transition-colors" title="Edit">
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </a>
                                        @if($event->awards_count == 0)
                                        <form action="{{ route('admin.event.destroy', $event->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this event?')" class="size-8 rounded flex items-center justify-center text-red-500 hover:bg-red-50/10 transition-colors" title="Delete">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-divider-subtle">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-4xl">event</span>
                                        <span>No events found</span>
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
                        Showing {{ $events->firstItem() ?? 0 }} to {{ $events->lastItem() ?? 0 }} of {{ $events->total() }} results
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
function applyFilters() {
    const search = document.getElementById('search').value;
    const level = document.getElementById('level').value;
    const year = document.getElementById('year').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (level) params.append('level', level);
    if (year) params.append('year', year);
    
    window.location.href = `{{ route('admin.events') }}?${params.toString()}`;
}

function clearFilters() {
    window.location.href = '{{ route('admin.events') }}';
}
</script>
@endpush
@endsection
