@extends('layouts.admin')

@section('title', 'Edit Project')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Edit Project
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Update project details
        </p>
    </div>
    <a href="{{ route('admin.projects.index') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Back to Projects</span>
    </a>
</header>

<div class="px-8 pb-8">
    <!-- Main Form Container -->
    <div class="max-w-5xl mx-auto bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-8 space-y-10">
                <!-- Section 1: Basic Information -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Basic Information</h2>
                        <p class="text-sm text-divider-subtle">Enter the project identification and title details.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Project ID <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="number" name="project_id" value="{{ $project->project_id }}" required
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                                placeholder="Enter project ID">
                            @error('project_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Project Title <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" name="project_title" value="{{ $project->project_title }}" required
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                                placeholder="Enter project title">
                            @error('project_title')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Grant No
                            </label>
                            <input type="text" name="grant_no" value="{{ $project->grant_no }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                                placeholder="Enter grant number (optional)">
                            @error('grant_no')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-between pt-6 border-t border-divider-subtle/30">
                    <div class="flex items-center gap-2 text-divider-subtle text-sm">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        <span>Last updated {{ $project->updated_at ? $project->updated_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.projects.index') }}" class="px-6 py-2.5 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-2.5 rounded-lg bg-primary text-white font-medium shadow-md hover:bg-primary/90 transition-all">
                            Update Project
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
