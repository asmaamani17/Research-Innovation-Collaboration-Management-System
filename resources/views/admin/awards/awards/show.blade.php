@extends('layouts.admin')

@section('title', $award->award_name)

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.awards') }}" class="text-primary hover:text-primary/80 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                    {{ $award->award_name }}
                </h2>
            </div>
            <p class="text-divider-subtle font-medium text-sm">
                @php
                    $levelColors = [
                        'GOLD' => 'bg-yellow-100 text-yellow-700',
                        'Gold' => 'bg-yellow-100 text-yellow-700',
                        'SILVER' => 'bg-gray-100 text-gray-700',
                        'Silver' => 'bg-gray-100 text-gray-700',
                        'BRONZE' => 'bg-orange-100 text-orange-700',
                        'Bronze' => 'bg-orange-100 text-orange-700',
                        'PLATINUM' => 'bg-cyan-100 text-cyan-700',
                        'Platinum' => 'bg-cyan-100 text-cyan-700',
                        'SPECIAL' => 'bg-purple-100 text-purple-700',
                        'Special' => 'bg-purple-100 text-purple-700'
                    ];
                    $color = $levelColors[$award->award_name] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $color }}">
                    {{ $award->award_name }}
                </span>
                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                    {{ $award->award_type }}
                </span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.awards.edit', $award->id) }}"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium shadow-lg">
                <span class="material-symbols-outlined">edit</span>
                <span>Edit</span>
            </a>
        </div>
    </header>

    <!-- Two Column Layout -->
    <div class="px-8 pb-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Award Details Section -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Award Details</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="size-16 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-4xl text-primary">emoji_events</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-text-main">{{ $award->award_name }}</h4>
                            <p class="text-sm text-text-main/80 mt-1">
                                Award Level: <span class="font-semibold">{{ $award->award_level }}</span>
                            </p>
                            <p class="text-sm text-text-main/80 mt-1">
                                Award Date: {{ $award->award_date ? \Carbon\Carbon::parse($award->award_date)->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    @if($award->exhibitionResult)
                    <div class="pt-4 border-t border-divider-subtle/30">
                        <p class="text-sm text-divider-subtle mb-1">Exhibition Result</p>
                        <p class="text-text-main font-medium">{{ $award->exhibitionResult->Description }}</p>
                    </div>
                    @endif
                    @if($award->invention_award)
                    <div class="pt-4 border-t border-divider-subtle/30">
                        <p class="text-sm text-divider-subtle mb-1">Invention Award</p>
                        <p class="text-text-main font-medium">{{ $award->invention_award == 1 ? 'Ya (Yes)' : 'Tidak (No)' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recipient Information Section -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Recipient Information</h3>
                </div>
                <div class="divide-y divide-divider-subtle/20">
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                {{ strtoupper(substr($award->staff->staff_name ?? 'Unknown', 0, 2)) }}
                            </div>
                            <div class="flex-1">
                                <a href="{{ route('admin.staff.show', $award->staff_id) }}"
                                    class="text-sm font-semibold text-text-main hover:text-primary transition-colors">
                                    {{ $award->staff->staff_name ?? 'Unknown Staff' }}
                                </a>
                                <div class="text-xs text-divider-subtle">
                                    {{ $award->staff->staff_id ?? '' }} •
                                    {{ $award->staff->faculty->faculty_name ?? 'No Faculty' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($award->project)
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="size-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs">
                                PR
                            </div>
                            <div class="flex-1">
                                <a href="{{ route('admin.projects.show', $award->project_id) }}"
                                    class="text-sm font-semibold text-text-main hover:text-primary transition-colors">
                                    {{ $award->project->project_title }}
                                </a>
                                <div class="text-xs text-divider-subtle">
                                    {{ $award->project->project_id }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Evidence Document Section -->
            @if($award->evidence_document)
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Evidence Document</h3>
                </div>
                <div class="p-6">
                    <div class="border-2 border-dashed border-divider-subtle/30 rounded-lg p-8 text-center">
                        <span class="material-symbols-outlined text-5xl text-primary mb-3">description</span>
                        <p class="text-text-main font-medium mb-2">Evidence Document Available</p>
                        <a href="{{ asset('storage/' . $award->evidence_document) }}" target="_blank"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-all">
                            <span class="material-symbols-outlined text-xl">open_in_new</span>
                            <span>View Document</span>
                        </a>
                        <p class="text-xs text-divider-subtle mt-3">
                            Opens in new tab
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Event Info Card -->
            @if($award->competition)
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-text-main mb-4">Event Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Event Name</div>
                        <div class="text-text-main font-semibold">{{ $award->competition->event_name }}</div>
                    </div>
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Organizer</div>
                        <div class="text-text-main font-semibold">{{ $award->competition->organizer ?? 'N/A' }}</div>
                    </div>
                    <div class="pt-3 border-t border-divider-subtle/30">
                        <div class="text-divider-subtle font-medium mb-1">Location</div>
                        <div class="text-text-main font-semibold">{{ $award->competition->exhibition_place ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Level</div>
                        <div class="text-text-main font-semibold">{{ $award->competition->exhibition_level ?? 'N/A' }}</div>
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
                            <div class="text-text-main font-semibold">{{ $award->competition->start_date?->format('M d, Y') ?? 'TBD' }}
                            </div>
                        </div>
                    </div>
                    @if($award->competition->end_date && $award->competition->end_date != $award->competition->start_date)
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                        <span class="material-symbols-outlined text-lg text-green-600">event_available</span>
                        <div>
                            <div class="text-divider-subtle font-medium mb-1">End Date</div>
                            <div class="text-text-main font-semibold">{{ $award->competition->end_date?->format('M d, Y') ?? 'TBD' }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Metadata Card -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-text-main mb-4">Metadata</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Created</div>
                        <div class="text-text-main font-semibold">{{ $award->created_at?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-divider-subtle font-medium mb-1">Last Updated</div>
                        <div class="text-text-main font-semibold">{{ $award->updated_at?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
