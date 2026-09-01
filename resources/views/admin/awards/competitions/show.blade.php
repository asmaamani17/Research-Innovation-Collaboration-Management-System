@extends('layouts.admin')

@section('title', $event->event_name)

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.events') }}" class="text-primary hover:text-primary/80 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                    {{ $event->event_name }}
                </h2>
            </div>
            <p class="text-divider-subtle font-medium text-sm">
                @if($event->exhibition_level === 'International')
                    <span
                        class="px-2 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">International</span>
                @elseif($event->exhibition_level === 'National')
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">National</span>
                @else
                    <span
                        class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">{{ $event->exhibition_level }}</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.event.edit', $event->id) }}"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium shadow-lg">
                <span class="material-symbols-outlined">edit</span>
                <span>Edit</span>
            </a>
        </div>
    </header>

    <!-- Statistics Cards -->
    <div class="px-8 mb-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Total Awards</div>
                <div class="text-3xl font-bold text-primary">{{ $stats['total_awards'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Participants</div>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['unique_participants'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Gold</div>
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['gold_awards'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Silver</div>
                <div class="text-3xl font-bold text-gray-400">{{ $stats['silver_awards'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Bronze</div>
                <div class="text-3xl font-bold text-orange-600">{{ $stats['bronze_awards'] }}</div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="px-8 pb-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Awards Section -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Awards Awarded</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-background-light">
                                <th class="px-6 py-3 text-sm font-bold text-text-main border-b border-divider-subtle">Award
                                    Name</th>
                                <th class="px-6 py-3 text-sm font-bold text-text-main border-b border-divider-subtle">Staff
                                    Member</th>
                                <th class="px-6 py-3 text-sm font-bold text-text-main border-b border-divider-subtle">Level
                                </th>
                                <th class="px-6 py-3 text-sm font-bold text-text-main border-b border-divider-subtle">Date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-divider-subtle/20">
                            @forelse($event->awards as $award)
                                <tr class="hover:bg-background-light/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-text-main">{{ $award->award_name }}</td>
                                    <td class="px-6 py-4 text-sm text-text-main/80">
                                        <a href="{{ route('admin.staff.show', $award->staff_id) }}"
                                            class="text-primary hover:underline">
                                            {{ $award->staff->staff_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $levelColors = [
                                                'Gold' => 'bg-yellow-100 text-yellow-700',
                                                'Silver' => 'bg-gray-100 text-gray-700',
                                                'Bronze' => 'bg-orange-100 text-orange-700'
                                            ];
                                            $color = $levelColors[$award->award_level] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-bold {{ $color }}">
                                            {{ $award->award_level }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-text-main/80">
                                        {{ $award->award_date ? $award->award_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-divider-subtle">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="material-symbols-outlined text-4xl">emoji_events</span>
                                            <span>No awards awarded at this event</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Participating Staff Section -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Participating Staff</h3>
                </div>
                <div class="divide-y divide-divider-subtle/20">
                    @forelse($event->staff as $staffMember)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                        {{ strtoupper(substr($staffMember->staff_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.staff.show', $staffMember->id) }}"
                                            class="text-sm font-semibold text-text-main hover:text-primary transition-colors">
                                            {{ $staffMember->staff_name }}
                                        </a>
                                        <div class="text-xs text-divider-subtle">
                                            {{ $staffMember->staff_id }} •
                                            {{ $staffMember->faculty->faculty_name ?? 'No Faculty' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-divider-subtle">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl">person_off</span>
                                <span>No staff participated</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Event Info Card -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-text-main mb-4">Event Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Event Name</div>
                        <div class="text-text-main font-semibold">{{ $event->event_name }}</div>
                    </div>
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Conferring Body</div>
                        <div class="text-text-main font-semibold">{{ $event->conferring_body }}</div>
                    </div>
                    <div class="pt-3 border-t border-divider-subtle/30">
                        <div class="text-divider-subtle font-medium mb-1">Location</div>
                        <div class="text-text-main font-semibold">{{ $event->exhibition_place }}</div>
                    </div>
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Level</div>
                        <div class="text-text-main font-semibold">{{ $event->exhibition_level }}</div>
                    </div>
                </div>
            </div>

            <!-- Event Dates Card -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-text-main mb-4">Event Dates</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                        <span class="material-symbols-outlined text-lg text-blue-600">event</span>
                        <div>
                            <div class="text-divider-subtle font-medium mb-1">Start Date</div>
                            <div class="text-text-main font-semibold">{{ $event->start_date?->format('M d, Y') ?? 'TBD' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@extends('layouts.admin')

@section('title', $event->event_name)

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.events') }}" class="text-primary hover:text-primary/80 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                    {{ $event->event_name }}
                </h2>
            </div>
            <p class="text-divider-subtle font-medium text-sm">
                @if($event->exhibition_level === 'International')
                    <span
                        class="px-2 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">International</span>
                @elseif($event->exhibition_level === 'National')
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">National</span>
                @else
                    <span
                        class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">{{ $event->exhibition_level }}</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.events') }}"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium shadow-lg">
                <span class="material-symbols-outlined">edit</span>
                <span>Edit</span>
            </a>
        </div>
    </header>

    <!-- Statistics Cards -->
    <div class="px-8 mb-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Total Awards</div>
                <div class="text-3xl font-bold text-primary">{{ $stats['total_awards'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Participants</div>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['unique_participants'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Gold</div>
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['gold_awards'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Silver</div>
                <div class="text-3xl font-bold text-gray-400">{{ $stats['silver_awards'] }}</div>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-4">
                <div class="text-divider-subtle text-xs font-medium uppercase tracking-wider mb-2">Bronze</div>
                <div class="text-3xl font-bold text-orange-600">{{ $stats['bronze_awards'] }}</div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="px-8 pb-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Awards Section -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Awards Awarded</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-background-light">
                                <th class="px-6 py-3 text-sm font-bold text-text-main border-b border-divider-subtle">Award
                                    Name</th>
                                <th class="px-6 py-3 text-sm font-bold text-text-main border-b border-divider-subtle">Staff
                                    Member</th>
                                <th class="px-6 py-3 text-sm font-bold text-text-main border-b border-divider-subtle">Level
                                </th>
                                <th class="px-6 py-3 text-sm font-bold text-text-main border-b border-divider-subtle">Date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-divider-subtle/20">
                            @forelse($event->awards as $award)
                                <tr class="hover:bg-background-light/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-text-main">{{ $award->award_name }}</td>
                                    <td class="px-6 py-4 text-sm text-text-main/80">
                                        <a href="{{ route('admin.staff.show', $award->staff_id) }}"
                                            class="text-primary hover:underline">
                                            {{ $award->staff->staff_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $levelColors = [
                                                'Gold' => 'bg-yellow-100 text-yellow-700',
                                                'Silver' => 'bg-gray-100 text-gray-700',
                                                'Bronze' => 'bg-orange-100 text-orange-700'
                                            ];
                                            $color = $levelColors[$award->award_level] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-bold {{ $color }}">
                                            {{ $award->award_level }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-text-main/80">
                                        {{ $award->award_date ? $award->award_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-divider-subtle">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="material-symbols-outlined text-4xl">emoji_events</span>
                                            <span>No awards awarded at this event</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Participating Staff Section -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Participating Staff</h3>
                </div>
                <div class="divide-y divide-divider-subtle/20">
                    @forelse($event->staff as $staffMember)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                        {{ strtoupper(substr($staffMember->staff_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.staff.show', $staffMember->id) }}"
                                            class="text-sm font-semibold text-text-main hover:text-primary transition-colors">
                                            {{ $staffMember->staff_name }}
                                        </a>
                                        <div class="text-xs text-divider-subtle">
                                            {{ $staffMember->staff_id }} •
                                            {{ $staffMember->faculty->faculty_name ?? 'No Faculty' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-divider-subtle">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl">person_off</span>
                                <span>No staff participated</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Event Info Card -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-text-main mb-4">Event Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Event Name</div>
                        <div class="text-text-main font-semibold">{{ $event->event_name }}</div>
                    </div>
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Conferring Body</div>
                        <div class="text-text-main font-semibold">{{ $event->conferring_body }}</div>
                    </div>
                    <div class="pt-3 border-t border-divider-subtle/30">
                        <div class="text-divider-subtle font-medium mb-1">Location</div>
                        <div class="text-text-main font-semibold">{{ $event->exhibition_place }}</div>
                    </div>
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Level</div>
                        <div class="text-text-main font-semibold">{{ $event->exhibition_level }}</div>
                    </div>
                </div>
            </div>

            <!-- Event Dates Card -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-text-main mb-4">Event Dates</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                        <span class="material-symbols-outlined text-lg text-blue-600">event</span>
                        <div>
                            <div class="text-divider-subtle font-medium mb-1">Start Date</div>
                            <div class="text-text-main font-semibold">{{ $event->start_date->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-lg">
                        <span class="material-symbols-outlined text-lg text-orange-600">event</span>
                        <div>
                            <div class="text-divider-subtle font-medium mb-1">End Date</div>
                            <div class="text-text-main font-semibold">{{ $event->end_date->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Award Breakdown -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-text-main mb-4">Award Breakdown</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-yellow-600">emoji_events</span>
                            <span class="text-sm font-medium text-text-main">Gold Awards</span>
                        </div>
                        <span class="text-lg font-bold text-yellow-600">{{ $stats['gold_awards'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-gray-400">emoji_events</span>
                            <span class="text-sm font-medium text-text-main">Silver Awards</span>
                        </div>
                        <span class="text-lg font-bold text-gray-400">{{ $stats['silver_awards'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-orange-600">emoji_events</span>
                            <span class="text-sm font-medium text-text-main">Bronze Awards</span>
                        </div>
                        <span class="text-lg font-bold text-orange-600">{{ $stats['bronze_awards'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const eventId = @json($event->id);
    </script>
@endpush