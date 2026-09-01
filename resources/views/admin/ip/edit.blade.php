@extends('layouts.admin')

@section('title', 'Edit Intellectual Property')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Edit Intellectual Property
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Update intellectual property record
        </p>
    </div>
    <a href="{{ route('admin.ip.index') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Back to IPs</span>
    </a>
</header>

<div class="px-8 pb-8">
    <!-- Main Form Container -->
    <div class="max-w-5xl mx-auto bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.ip.update', $ip->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-8 space-y-10">
                <!-- Section 1: Basic Information -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Basic Information</h2>
                        <p class="text-sm text-divider-subtle">Enter the IP number and title.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2">
                                IP Number
                            </label>
                            <input type="text" name="ip_number" value="{{ old('ip_number', $ip->ip_number) }}" placeholder="e.g., MY-2023-001"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            @error('ip_number')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Title <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title', $ip->title) }}" required placeholder="Enter IP title..."
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            @error('title')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-divider-subtle/30"></div>

                <!-- Section 2: IP Type & Status -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Type & Status</h2>
                        <p class="text-sm text-divider-subtle">Specify the IP type and current status.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Type <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select name="type" required
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                                <option value="">Select Type</option>
                                <option value="PATENT" @selected(old('type', $ip->type) === 'PATENT')">Patent</option>
                                <option value="UTILITY_INNOVATION" @selected(old('type', $ip->type) === 'UTILITY_INNOVATION')">Utility Innovation</option>
                                <option value="COPYRIGHT" @selected(old('type', $ip->type) === 'COPYRIGHT')">Copyright</option>
                                <option value="TRADEMARK" @selected(old('type', $ip->type) === 'TRADEMARK')">Trademark</option>
                                <option value="INDUSTRIAL_DESIGN" @selected(old('type', $ip->type) === 'INDUSTRIAL_DESIGN')">Industrial Design</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2">
                                Status
                            </label>
                            <select name="status"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                                <option value="">Select Status</option>
                                <option value="FILED" @selected(old('status', $ip->status) === 'FILED')">Filed</option>
                                <option value="GRANTED" @selected(old('status', $ip->status) === 'GRANTED')">Granted</option>
                                <option value="REGISTERED" @selected(old('status', $ip->status) === 'REGISTERED')">Registered</option>
                                <option value="PENDING" @selected(old('status', $ip->status) === 'PENDING')">Pending</option>
                                <option value="REJECTED" @selected(old('status', $ip->status) === 'REJECTED')">Rejected</option>
                                <option value="EXPIRED" @selected(old('status', $ip->status) === 'EXPIRED')">Expired</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-divider-subtle/30"></div>

                <!-- Section 3: Dates -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Important Dates</h2>
                        <p class="text-sm text-divider-subtle">Enter filing, grant, and expiry dates.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2">
                                Filing Date
                            </label>
                            <input type="date" name="filing_date" value="{{ old('filing_date', $ip->filing_date ? $ip->filing_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            @error('filing_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2">
                                Grant Date
                            </label>
                            <input type="date" name="grant_date" value="{{ old('grant_date', $ip->grant_date ? $ip->grant_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            @error('grant_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2">
                                Expiry Date
                            </label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date', $ip->expiry_date ? $ip->expiry_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            @error('expiry_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-divider-subtle/30"></div>

                <!-- Section 4: Associated Information -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Associated Information</h2>
                        <p class="text-sm text-divider-subtle">Link to staff, project, and country.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Staff Members <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select name="staff_ids[]" required multiple
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all h-32">
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}" @selected(in_array($s->id, old('staff_ids', $ip->staff->pluck('id')->toArray())))>
                                        {{ $s->staff_id }} - {{ $s->staff_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-divider-subtle">Hold Ctrl/Cmd to select multiple staff</p>
                            @error('staff_ids')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2">
                                Project
                            </label>
                            <select name="project_id"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                                <option value="">Select Project (Optional)</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" @selected(old('project_id', $ip->project_id) === $p->id)>
                                        {{ $p->project_id }} - {{ Str::limit($p->project_title, 50) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2">
                                Country
                            </label>
                            <input type="text" name="country" value="{{ old('country', $ip->country) }}" placeholder="e.g., Malaysia, United States"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            @error('country')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-divider-subtle/30"></div>

                <!-- Section 5: Additional Information -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Additional Information</h2>
                        <p class="text-sm text-divider-subtle">Add evidence link and remarks.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2">
                                Link to Evidence
                            </label>
                            <input type="url" name="link_to_evidence" value="{{ old('link_to_evidence', $ip->link_to_evidence) }}" placeholder="https://..."
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            @error('link_to_evidence')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2">
                                Remarks
                            </label>
                            <textarea name="remarks" rows="4" placeholder="Enter any additional remarks..."
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all resize-none">{{ old('remarks', $ip->remarks) }}</textarea>
                            @error('remarks')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-8 py-6 bg-background-light border-t border-divider-subtle/30 flex items-center justify-end gap-4">
                <a href="{{ route('admin.ip.index') }}"
                    class="h-11 px-6 bg-white border border-divider-subtle/30 text-text-main text-sm font-bold rounded-lg hover:bg-background-light transition-all">
                    Cancel
                </a>
                <button type="submit"
                    class="h-11 px-8 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-all">
                    Update Intellectual Property
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
