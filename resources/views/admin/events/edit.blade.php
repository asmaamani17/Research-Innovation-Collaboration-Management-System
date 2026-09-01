@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Edit Event
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Update event details and information
        </p>
    </div>
    <a href="{{ route('admin.events') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Back to Events</span>
    </a>
</header>

<div class="px-8 pb-8">

    <!-- Main Form Container -->
    <div class="max-w-5xl mx-auto bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.event.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-8 space-y-10">
                <!-- Section 1: Basic Information -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Basic Information</h2>
                        <p class="text-sm text-divider-subtle">Enter the event name and organizing institution.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Event Name <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" name="event_name" value="{{ $event->event_name }}" required
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                                placeholder="Enter event name">
                            @error('event_name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Organizer <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" name="organizer" value="{{ $event->organizer }}" required
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                                placeholder="Organization name">
                            @error('organizer')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-divider-subtle/30"></div>

                <!-- Section 2: Venue & Classification -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Venue & Classification</h2>
                        <p class="text-sm text-divider-subtle">Set the exhibition location and competition level for awards.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Exhibition Place <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-divider-subtle">location_on</span>
                                <input type="text" name="exhibition_place" value="{{ $event->exhibition_place }}" required
                                    class="w-full pl-10 pr-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                                    placeholder="Search for a location or URL">
                            </div>
                            @error('exhibition_place')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="max-w-md">
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Exhibition Level <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <select name="exhibition_level" required
                                    class="w-full appearance-none px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                                    <option value="">Select Level</option>
                                    <option value="International" {{ $event->exhibition_level === 'International' ? 'selected' : '' }}>International</option>
                                    <option value="Regional" {{ $event->exhibition_level === 'Regional' ? 'selected' : '' }}>Regional</option>
                                    <option value="National" {{ $event->exhibition_level === 'National' ? 'selected' : '' }}>National</option>
                                    <option value="University" {{ $event->exhibition_level === 'University' ? 'selected' : '' }}>University</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-divider-subtle">expand_more</span>
                            </div>
                            @error('exhibition_level')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-divider-subtle/30"></div>

                <!-- Section 3: Event Schedule -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Event Schedule</h2>
                        <p class="text-sm text-divider-subtle">Set the start and end dates for the competition period.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Start Date <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-divider-subtle">calendar_today</span>
                                <input type="date" name="start_date" value="{{ $event->start_date ? $event->start_date->format('Y-m-d') : '' }}" required
                                    class="w-full pr-10 px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            </div>
                            @error('start_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                End Date <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-divider-subtle">calendar_today</span>
                                <input type="date" name="end_date" value="{{ $event->end_date ? $event->end_date->format('Y-m-d') : '' }}" required
                                    class="w-full pr-10 px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            </div>
                            @error('end_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-between pt-6 border-t border-divider-subtle/30">
                    <div class="flex items-center gap-2 text-divider-subtle text-sm">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        <span>Last updated {{ $event->updated_at ? $event->updated_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.events') }}" class="px-6 py-2.5 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-2.5 rounded-lg bg-primary text-white font-medium shadow-md hover:bg-primary/90 transition-all">
                            Update Event
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
