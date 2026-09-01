@extends('layouts.admin')

@section('title', 'Intellectual Property Details')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Intellectual Property Details
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            View detailed information about this intellectual property.
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.ip.edit', $ip->id) }}"
            class="flex min-w-[120px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-sm">edit</span>
            <span>Edit</span>
        </a>
        <a href="{{ route('admin.ip.index') }}"
            class="flex min-w-[120px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Back</span>
        </a>
    </div>
</header>

<div class="px-8 pb-8">
    <!-- Main Content -->
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Basic Information Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light">
                <h3 class="text-lg font-bold text-text-main">Basic Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">IP Number</label>
                    <p class="text-text-main font-medium">{{ $ip->ip_number ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">Title</label>
                    <p class="text-text-main font-medium">{{ $ip->title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">Type</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                        {{ $ip->type }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($ip->status === 'GRANTED') bg-green-100 text-green-700
                        @elseif($ip->status === 'FILED') bg-yellow-100 text-yellow-700
                        @elseif($ip->status === 'REGISTERED') bg-blue-100 text-blue-700
                        @elseif($ip->status === 'PENDING') bg-orange-100 text-orange-700
                        @elseif($ip->status === 'REJECTED') bg-red-100 text-red-700
                        @elseif($ip->status === 'EXPIRED') bg-gray-100 text-gray-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ $ip->status ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Important Dates Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light">
                <h3 class="text-lg font-bold text-text-main">Important Dates</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">Filing Date</label>
                    <p class="text-text-main font-medium">{{ $ip->filing_date ? $ip->filing_date->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">Grant Date</label>
                    <p class="text-text-main font-medium">{{ $ip->grant_date ? $ip->grant_date->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">Expiry Date</label>
                    <p class="text-text-main font-medium">{{ $ip->expiry_date ? $ip->expiry_date->format('d M Y') : '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Associated Information Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light">
                <h3 class="text-lg font-bold text-text-main">Associated Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">Staff Members</label>
                    <div class="space-y-2">
                        @foreach($ip->staff as $staff)
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-primary">person</span>
                                <span class="text-text-main">{{ $staff->staff_id }} - {{ $staff->staff_name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">Project</label>
                    @if($ip->project)
                        <p class="text-text-main font-medium">{{ $ip->project->project_id }} - {{ Str::limit($ip->project->project_title, 50) }}</p>
                    @else
                        <p class="text-text-main">-</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-divider-subtle mb-1">Country</label>
                    <p class="text-text-main font-medium">{{ $ip->country ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Additional Information Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light">
                <h3 class="text-lg font-bold text-text-main">Additional Information</h3>
            </div>
            <div class="p-6 space-y-6">
                @if($ip->link_to_evidence)
                    <div>
                        <label class="block text-sm font-medium text-divider-subtle mb-1">Link to Evidence</label>
                        <a href="{{ $ip->link_to_evidence }}" target="_blank" class="text-primary hover:underline">
                            {{ $ip->link_to_evidence }}
                        </a>
                    </div>
                @endif
                @if($ip->remarks)
                    <div>
                        <label class="block text-sm font-medium text-divider-subtle mb-1">Remarks</label>
                        <p class="text-text-main">{{ $ip->remarks }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <form action="{{ route('admin.ip.destroy', $ip->id) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this IP?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="h-11 px-6 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700 transition-all">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
