@extends('layouts.admin')

@section('title', 'Award Details')

@php
    $awardStyles = [
        'GOLD' => ['text' => 'text-yellow-600', 'icon' => 'text-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-700'],
        'SILVER' => ['text' => 'text-gray-600', 'icon' => 'text-gray-400', 'badge' => 'bg-gray-100 text-gray-700'],
        'BRONZE' => ['text' => 'text-orange-700', 'icon' => 'text-orange-600', 'badge' => 'bg-orange-100 text-orange-700'],
        'SPECIAL' => ['text' => 'text-primary', 'icon' => 'text-primary', 'badge' => 'bg-primary/10 text-primary'],
        'PLATINUM' => ['text' => 'text-cyan-700', 'icon' => 'text-cyan-500', 'badge' => 'bg-cyan-100 text-cyan-700'],
    ];
    $awardKey = strtoupper($award->award_name ?? 'AWARD');
    $style = $awardStyles[$awardKey] ?? ['text' => 'text-text-main', 'icon' => 'text-primary', 'badge' => 'bg-primary/10 text-primary'];
@endphp

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Award Details
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            View award information and evidence
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.awards.edit', $award->id) }}" class="flex min-w-[120px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-sm">edit</span>
            <span>Edit Award</span>
        </a>
        <a href="{{ route('admin.awards') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Back to Awards</span>
        </a>
    </div>
</header>

<div class="px-8 pb-8">
    <!-- Main Content -->
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Award Summary Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
            <div class="p-8">
                <div class="flex items-start gap-6">
                    <!-- Award Icon -->
                    <div class="size-20 rounded-full {{ $style['badge'] }} flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined {{ $style['icon'] }} text-5xl">emoji_events</span>
                    </div>
                    
                    <!-- Award Info -->
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-text-main mb-2">{{ $award->award_name }}</h3>
                        <div class="flex flex-wrap gap-3 mb-4">
                            <span class="px-3 py-1 rounded-full {{ $style['badge'] }} text-sm font-bold">{{ $award->award_level }}</span>
                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-bold">{{ $award->award_type }}</span>
                            <span class="px-3 py-1 rounded-full {{ strtoupper($award->event->exhibition_level ?? '') === 'INTERNATIONAL' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }} text-sm font-bold">
                                {{ $award->event->exhibition_level ?? 'N/A' }}
                            </span>
                        </div>
                        <p class="text-text-main/80">
                            Awarded on {{ optional($award->award_date)->format('F d, Y') ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipient Information -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light">
                <h3 class="text-lg font-bold text-text-main">Recipient Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Staff -->
                    <div class="flex items-start gap-4">
                        <div class="size-12 rounded-full bg-gray-200 bg-cover shrink-0"
                            style="background-image: url('https://ui-avatars.com/api/?name={{ rawurlencode($award->staff->staff_name ?? 'Unknown') }}&background=184290&color=fff')">
                        </div>
                        <div>
                            <p class="text-sm text-divider-subtle mb-1">Staff Member</p>
                            <p class="text-text-main font-medium">{{ $award->staff->staff_name ?? 'Unknown Staff' }}</p>
                            <p class="text-sm text-divider-subtle">{{ $award->staff->staff_id ?? '' }}</p>
                        </div>
                    </div>
                    
                    <!-- Faculty -->
                    <div>
                        <p class="text-sm text-divider-subtle mb-1">Faculty</p>
                        <p class="text-text-main font-medium">{{ $award->staff->faculty->faculty_name ?? 'N/A' }}</p>
                        <span class="px-2 py-1 rounded bg-primary/10 text-primary text-[10px] font-bold">
                            {{ $award->staff->faculty->faculty_code ?? 'N/A' }}
                        </span>
                    </div>
                    
                    <!-- Project -->
                    <div class="md:col-span-2">
                        <p class="text-sm text-divider-subtle mb-1">Project</p>
                        <p class="text-text-main font-medium">{{ $award->project->project_title ?? 'Unknown Project' }}</p>
                        <p class="text-sm text-divider-subtle">{{ $award->project->project_id ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Information -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light">
                <h3 class="text-lg font-bold text-text-main">Event Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-divider-subtle mb-1">Event Name</p>
                        <p class="text-text-main font-medium">{{ $award->event->event_name ?? 'Unknown Event' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-divider-subtle mb-1">Organizer</p>
                        <p class="text-text-main font-medium">{{ $award->event->organizer ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-divider-subtle mb-1">Exhibition Level</p>
                        <p class="text-text-main font-medium">{{ $award->event->exhibition_level ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-divider-subtle mb-1">Exhibition Place</p>
                        <p class="text-text-main font-medium">{{ $award->event->exhibition_place ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-divider-subtle mb-1">Event Date</p>
                        <p class="text-text-main font-medium">
                            {{ optional($award->event->start_date)->format('M d, Y') ?? 'N/A' }}
                            @if($award->event->end_date && $award->event->end_date != $award->event->start_date)
                                - {{ optional($award->event->end_date)->format('M d, Y') ?? 'N/A' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evidence Document -->
        @if($award->evidence_document)
        <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light">
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

        <!-- Metadata -->
        <div class="bg-background-light/50 border border-divider-subtle/20 rounded-lg p-4">
            <div class="flex flex-wrap gap-6 text-sm text-divider-subtle">
                <div>
                    <span class="font-medium">Created:</span> {{ optional($award->created_at)->format('M d, Y H:i') ?? 'N/A' }}
                </div>
                <div>
                    <span class="font-medium">Last Updated:</span> {{ optional($award->updated_at)->format('M d, Y H:i') ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
