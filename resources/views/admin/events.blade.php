@extends('layouts.admin')

@section('title', 'Events')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Events
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Manage research events and exhibitions
        </p>
    </div>
    <a href="{{ route('admin.event.create') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        <span>New Events</span>
    </a>
</header>

<div class="px-8 pb-8">
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
            <div class="min-w-[120px]">
                <input type="number" id="year" placeholder="Year" 
                    class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                    value="{{ request('year') }}">
            </div>
            <button onclick="applyFilters()" class="px-6 py-3 bg-primary text-white rounded-lg font-bold hover:bg-primary/90 transition-all">
                Filter
            </button>
            <button onclick="clearFilters()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition-all">
                Clear
            </button>
        </div>
    </div>

    <!-- EventsTable -->
    <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-divider-subtle/30">
                <tr>
                    <th onclick="sortByColumn('event_name')" class="text-left px-6 py-4 text-sm font-bold font-black text-text-main uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                        Event Name <span id="sort-event_name" class="material-symbols-outlined text-sm align-middle opacity-0">sort</span>
                    </th>
                    <th onclick="sortByColumn('organizer')" class="text-left px-6 py-4 text-sm font-bold font-black text-text-main uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                        Organizer <span id="sort-organizer" class="material-symbols-outlined text-sm align-middle opacity-0">sort</span>
                    </th>
                    <th onclick="filterByColumn('level')" class="text-left px-6 py-4 text-sm font-bold font-black text-text-main uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                        Level <span class="material-symbols-outlined text-sm align-middle">filter_list</span>
                    </th>
                    <th onclick="sortByColumn('start_date')" class="text-left px-6 py-4 text-sm font-bold font-black text-text-main uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                        <span class="inline-flex items-center gap-1">Date <span id="sort-start_date" class="material-symbols-outlined text-sm opacity-0">sort</span></span>
                    </th>
                    <th onclick="sortByColumn('awards_count')" class="text-center px-6 py-4 text-sm font-bold font-black text-text-main uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                        <span class="inline-flex items-center gap-1">Awards <span id="sort-awards_count" class="material-symbols-outlined text-sm opacity-0">sort</span></span>
                    </th>
                    <th onclick="sortByColumn('unique_participants')" class="text-center px-6 py-4 text-sm font-bold font-black text-text-main uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                        <span class="inline-flex items-center gap-1">Participants <span id="sort-unique_participants" class="material-symbols-outlined text-sm opacity-0">sort</span></span>
                    </th>
                    <th class="text-center px-6 py-4 text-sm font-bold font-black text-text-main uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr class="border-b border-divider-subtle/30 hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.event.show', $event->id) }}" class="font-bold text-primary hover:underline">
                            {{ $event->event_name }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-text-main">{{ $event->organizer }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $event->exhibition_level === 'International' ? 'bg-purple-100 text-purple-700' : ($event->exhibition_level === 'National' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ $event->exhibition_level }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-text-main">
                        {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-primary">{{ $event->awards_count ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-blue-600">{{ $event->unique_participants ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.event.show', $event->id) }}" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-xl">visibility</span>
                            </a>
                            <a href="{{ route('admin.event.edit', $event->id) }}" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-xl">edit</span>
                            </a>
                            @if($event->awards_count == 0)
                            <button onclick="deleteEvent({{ $event->id }})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-xl">delete</span>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-divider-subtle">
                        <p class="text-lg">No events found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($events->hasPages())
        <div class="px-6 py-4 border-t border-divider-subtle/30 flex items-center justify-between">
            <p class="text-sm text-divider-subtle">
                Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} results
            </p>
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function deleteEvent(id) {
    if (confirm('Are you sure you want to delete this event?')) {
        fetch(`/admin/events/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting event');
            }
        });
    }
}

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

let currentSort = { column: null, direction: 'asc' };

function sortByColumn(column) {
    // Toggle direction if clicking same column
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }

    // Update sort icons
    document.querySelectorAll('[id^="sort-"]').forEach(el => {
        el.textContent = 'sort';
        el.classList.add('opacity-0');
    });
    const sortIcon = document.getElementById(`sort-${column}`);
    if (sortIcon) {
        sortIcon.textContent = currentSort.direction === 'asc' ? 'arrow_upward' : 'arrow_downward';
        sortIcon.classList.remove('opacity-0');
    }

    // Apply sort
    const tbody = document.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        let aVal, bVal;
        
        switch(column) {
            case 'event_name':
                aVal = a.querySelector('td:nth-child(1)').textContent.trim();
                bVal = b.querySelector('td:nth-child(1)').textContent.trim();
                break;
            case 'organizer':
                aVal = a.querySelector('td:nth-child(2)').textContent.trim();
                bVal = b.querySelector('td:nth-child(2)').textContent.trim();
                break;
            case 'start_date':
                aVal = a.querySelector('td:nth-child(4)').textContent.trim();
                bVal = b.querySelector('td:nth-child(4)').textContent.trim();
                break;
            case 'awards_count':
                aVal = parseInt(a.querySelector('td:nth-child(5)').textContent.trim()) || 0;
                bVal = parseInt(b.querySelector('td:nth-child(5)').textContent.trim()) || 0;
                break;
            case 'unique_participants':
                aVal = parseInt(a.querySelector('td:nth-child(6)').textContent.trim()) || 0;
                bVal = parseInt(b.querySelector('td:nth-child(6)').textContent.trim()) || 0;
                break;
            default:
                return 0;
        }

        if (currentSort.direction === 'asc') {
            return aVal > bVal ? 1 : -1;
        } else {
            return aVal < bVal ? 1 : -1;
        }
    });

    rows.forEach(row => tbody.appendChild(row));
}

function filterByColumn(column) {
    if (column === 'level') {
        const levelSelect = document.getElementById('level');
        const currentLevel = levelSelect.value;
        
        // Cycle through levels: All -> International -> National -> University -> Regional -> All
        const levels = ['', 'International', 'National', 'University', 'Regional'];
        const currentIndex = levels.indexOf(currentLevel);
        const nextIndex = (currentIndex + 1) % levels.length;
        
        levelSelect.value = levels[nextIndex];
        applyFilters();
    }
}
</script>
@endsection
