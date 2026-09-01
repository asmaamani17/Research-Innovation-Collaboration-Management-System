@extends('layouts.admin')

@section('title', 'Edit Award')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Edit Award
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Update award details
        </p>
    </div>
    <a href="{{ route('admin.awards') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Back to Awards</span>
    </a>
</header>

<div class="px-8 pb-8">
    <!-- Main Form Container -->
    <div class="max-w-5xl mx-auto bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.awards.update', $award->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-8 space-y-10">
                <!-- Section 1: Recipient Information -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Recipient Information</h2>
                        <p class="text-sm text-divider-subtle">Select the staff member and project receiving the award.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Staff Member <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input list="staff_list" name="staff_id" required placeholder="Search staff member..." value="{{ $award->staff_id }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            <datalist id="staff_list">
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}" {{ $award->staff_id == $s->id ? 'selected' : '' }}>{{ $s->staff_id }} - {{ $s->staff_name }}</option>
                                @endforeach
                            </datalist>
                            @error('staff_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Project <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input list="project_list" name="project_id" required placeholder="Search project..." value="{{ $award->project_id }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            <datalist id="project_list">
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ $award->project_id == $p->id ? 'selected' : '' }}>{{ $p->project_id }} - {{ $p->project_title }}</option>
                                @endforeach
                            </datalist>
                            @error('project_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-divider-subtle/30"></div>

                <!-- Section 2: Award Details -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Award Details</h2>
                        <p class="text-sm text-divider-subtle">Specify the award name, level, and type.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Award Name <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input list="award_name_list" name="award_name" required placeholder="Search award name..." value="{{ $award->award_name }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            <datalist id="award_name_list">
                                <option value="PLATINUM" {{ $award->award_name == 'PLATINUM' ? 'selected' : '' }}>Platinum</option>
                                <option value="SPECIAL" {{ $award->award_name == 'SPECIAL' ? 'selected' : '' }}>Special</option>
                                <option value="GOLD" {{ $award->award_name == 'GOLD' ? 'selected' : '' }}>Gold</option>
                                <option value="SILVER" {{ $award->award_name == 'SILVER' ? 'selected' : '' }}>Silver</option>
                                <option value="BRONZE" {{ $award->award_name == 'BRONZE' ? 'selected' : '' }}>Bronze</option>
                                <option value="OTHERS" {{ $award->award_name == 'OTHERS' ? 'selected' : '' }}>Others</option>
                            </datalist>
                            @error('award_name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Award Level <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input list="award_level_list" name="award_level" required placeholder="Search award level..." value="{{ $award->award_level }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            <datalist id="award_level_list">
                                <option value="INDIVIDUAL" {{ $award->award_level == 'INDIVIDUAL' ? 'selected' : '' }}>Individual</option>
                                <option value="INSTITUTIONAL" {{ $award->award_level == 'INSTITUTIONAL' ? 'selected' : '' }}>Institutional</option>
                            </datalist>
                            @error('award_level')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Award Type <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input list="award_type_list" name="award_type" required placeholder="Search award type..." value="{{ $award->award_type }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            <datalist id="award_type_list">
                                <option value="AWARD" {{ $award->award_type == 'AWARD' ? 'selected' : '' }}>Award</option>
                                <option value="RECOGNITION" {{ $award->award_type == 'RECOGNITION' ? 'selected' : '' }}>Recognition</option>
                                <option value="STEWARDSHIP" {{ $award->award_type == 'STEWARDSHIP' ? 'selected' : '' }}>Stewardship</option>
                                <option value="EXHIBITION" {{ $award->award_type == 'EXHIBITION' ? 'selected' : '' }}>Exhibition</option>
                                <option value="OTHER RESEARCH AWARDS" {{ $award->award_type == 'OTHER RESEARCH AWARDS' ? 'selected' : '' }}>Other Research Awards</option>
                                <option value="CLARIVATE HIGHLY AWARD" {{ $award->award_type == 'CLARIVATE HIGHLY AWARD' ? 'selected' : '' }}>Clarivate Highly Award</option>
                            </datalist>
                            @error('award_type')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-divider-subtle/30"></div>

                <!-- Section 3: Event Information -->
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-4">
                        <h2 class="text-xl font-bold text-text-main mb-2">Event Information</h2>
                        <p class="text-sm text-divider-subtle">Select the event and award date.</p>
                    </div>
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Event <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input list="event_list" name="event_id" required placeholder="Search event..." value="{{ $award->competition_id }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            <datalist id="event_list">
                                @foreach($events as $e)
                                    <option value="{{ $e->id }}" {{ $award->competition_id == $e->id ? 'selected' : '' }}>{{ $e->event_name }}</option>
                                @endforeach
                            </datalist>
                            @error('event_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Exhibition Level <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input list="exhibition_level_list" name="event_exhibition_level" required placeholder="Search exhibition level..." value="{{ $award->event->exhibition_level }}"
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            <datalist id="exhibition_level_list">
                                <option value="International" {{ $award->event->exhibition_level == 'International' ? 'selected' : '' }}>International</option>
                                <option value="National" {{ $award->event->exhibition_level == 'National' ? 'selected' : '' }}>National</option>
                                <option value="Regional" {{ $award->event->exhibition_level == 'Regional' ? 'selected' : '' }}>Regional</option>
                                <option value="University" {{ $award->event->exhibition_level == 'University' ? 'selected' : '' }}>University</option>
                            </datalist>
                            @error('event_exhibition_level')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Award Date <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="date" name="award_date" value="{{ $award->award_date ? $award->award_date->format('Y-m-d') : '' }}" required
                                class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                            @error('award_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-main mb-2 flex items-center">
                                Evidence Document
                            </label>
                            <div id="drop-zone" class="border-2 border-dashed border-divider-subtle/30 rounded-lg p-6 text-center hover:border-primary/50 transition-colors cursor-pointer">
                                <input type="file" name="evidence_document" id="file-input" class="hidden"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                                <div id="drop-content">
                                    <span class="material-symbols-outlined text-4xl text-divider-subtle mb-2">cloud_upload</span>
                                    <p class="text-sm text-text-main">Drag & drop file here or <span class="text-primary font-medium">browse</span></p>
                                    <p class="text-xs text-divider-subtle mt-1">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP (Max 10MB)</p>
                                    @if($award->evidence_document)
                                        <p class="text-xs text-primary mt-2">Current: <a href="{{ asset('storage/' . $award->evidence_document) }}" target="_blank" class="hover:underline">View existing file</a></p>
                                    @endif
                                </div>
                                <div id="file-preview" class="hidden">
                                    <span class="material-symbols-outlined text-4xl text-primary mb-2">description</span>
                                    <p id="file-name" class="text-sm text-text-main font-medium"></p>
                                    <button type="button" onclick="clearFile()" class="text-xs text-red-500 hover:text-red-700 mt-2">Remove file</button>
                                </div>
                            </div>
                            @error('evidence_document')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-between pt-6 border-t border-divider-subtle/30">
                    <div class="flex items-center gap-2 text-divider-subtle text-sm">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        <span>Last updated {{ $award->updated_at ? $award->updated_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.awards') }}" class="px-6 py-2.5 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-2.5 rounded-lg bg-primary text-white font-medium shadow-md hover:bg-primary/90 transition-all">
                            Update Award
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const dropContent = document.getElementById('drop-content');
const filePreview = document.getElementById('file-preview');
const fileName = document.getElementById('file-name');

// Click to browse
dropZone.addEventListener('click', () => fileInput.click());

// Handle file selection
fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        showFilePreview(e.target.files[0]);
    }
});

// Drag and drop events
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-primary', 'bg-primary/5');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-primary', 'bg-primary/5');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary', 'bg-primary/5');
    
    if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        showFilePreview(e.dataTransfer.files[0]);
    }
});

function showFilePreview(file) {
    dropContent.classList.add('hidden');
    filePreview.classList.remove('hidden');
    fileName.textContent = file.name;
}

function clearFile() {
    fileInput.value = '';
    dropContent.classList.remove('hidden');
    filePreview.classList.add('hidden');
}
</script>
@endpush
