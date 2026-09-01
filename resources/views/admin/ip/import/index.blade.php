@extends('layouts.admin')

@section('title', 'Import IP Data')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                Import IP Data
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Import intellectual property data from CSV files into the system.
            </p>
        </div>
    </header>

    @if(session('success'))
        <div class="mx-8 mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-800 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mx-8 mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="px-8 pb-8 flex-1">
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <!-- Template Download Section -->
            <div class="px-8 py-6 border-b border-divider-subtle/30">
                <h3 class="text-lg font-bold text-text-main mb-4">Download Templates</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- <a href="{{ route('admin.import.template', ['type' => 'Template_RD8_New']) }}"
                        class="flex items-center gap-3 p-4 rounded-lg bg-green-50 hover:bg-green-100 transition-colors border border-green-200">
                        <span class="material-symbols-outlined text-green-600 text-2xl">download</span>
                        <div>
                            <p class="font-semibold text-text-main text-sm">Template_RD8_New</p>
                            <p class="text-xs text-divider-subtle">Download Template_RD8_New format</p>
                        </div>
                    </a> -->
                    <a href="{{ route('admin.import.template', ['type' => 'ip']) }}"
                        class="flex items-center gap-3 p-4 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors border border-purple-200">
                        <span class="material-symbols-outlined text-purple-600 text-2xl">download</span>
                        <div>
                            <p class="font-semibold text-text-main text-sm">Intellectual Properties</p>
                            <p class="text-xs text-divider-subtle">Download IP template format</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="px-8 py-6 border-b border-divider-subtle/30">
                <h3 class="text-lg font-bold text-text-main mb-4">Instructions</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">looks_one</span>
                        <p class="text-sm text-text-main/80">Download the appropriate template for the data you want to import.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">looks_two</span>
                        <p class="text-sm text-text-main/80">Fill in the CSV file with your data following the template format exactly.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">looks_3</span>
                        <p class="text-sm text-text-main/80">Select the data type and upload your CSV file below.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">looks_4</span>
                        <p class="text-sm text-text-main/80">Wait for the import to complete. The progress bar will show the status.</p>
                    </div>
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-yellow-600 text-lg">warning</span>
                            <div>
                                <p class="text-sm font-semibold text-yellow-800">Important Notes:</p>
                                <ul class="text-xs text-yellow-700 mt-1 space-y-1 list-disc list-inside">
                                    <li>Ensure date fields are in YYYY-MM-DD format</li>
                                    <li>For Staff imports, faculty_code must match existing faculty codes</li>
                                    <li>Maximum file size: 10MB</li>
                                    <li>Only CSV files are accepted</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Section -->
            <div class="px-8 py-6">
                <h3 class="text-lg font-bold text-text-main mb-4">Upload CSV File</h3>
                <form id="importForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-text-main mb-2">Select Data Type</label>
                        <select name="type" id="dataType" required
                            class="w-full px-4 py-3 rounded-lg border border-divider-subtle/70 bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <option value="">-- Select Data Type --</option>
                            <option value="ip">Intellectual Properties</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-main mb-2">Upload CSV File</label>
                        <div class="relative">
                            <input type="file" name="file" id="csvFile" accept=".csv" required
                                class="w-full px-4 py-3 rounded-lg border border-divider-subtle/70 bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:text-sm file:font-medium hover:file:bg-primary/90">
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div id="progressContainer" class="hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-text-main">Importing...</span>
                            <span id="progressText" class="text-sm text-divider-subtle">0%</span>
                        </div>
                        <div class="w-full bg-background-light rounded-full h-3 overflow-hidden">
                            <div id="progressBar" class="bg-primary h-3 rounded-full transition-all duration-300"
                                style="width: 0%"></div>
                        </div>
                    </div>

                    <div id="resultMessage" class="hidden"></div>

                    <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-2 rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
                        <span class="material-symbols-outlined text-sm">upload_file</span>
                        <span>Import Data</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const importForm = document.getElementById('importForm');
            const csvFile = document.getElementById('csvFile');
            const dataType = document.getElementById('dataType');
            const submitBtn = document.getElementById('submitBtn');
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const resultMessage = document.getElementById('resultMessage');

            importForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                if (!csvFile.files[0]) {
                    alert('Please select a CSV file to upload.');
                    return;
                }

                if (!dataType.value) {
                    alert('Please select a data type.');
                    return;
                }

                const formData = new FormData();
                formData.append('file', csvFile.files[0]);
                formData.append('type', dataType.value);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                // Show progress bar
                progressContainer.classList.remove('hidden');
                resultMessage.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">refresh</span><span>Importing...</span>';

                // Simulate progress
                let progress = 0;
                const progressInterval = setInterval(() => {
                    if (progress < 90) {
                        progress += Math.random() * 10;
                        if (progress > 90) progress = 90;
                        progressBar.style.width = progress + '%';
                        progressText.textContent = Math.round(progress) + '%';
                    }
                }, 200);

                try {
                    const response = await fetch('{{ route('admin.import.import') }}', {
                        method: 'POST',
                        body: formData
                    });

                    clearInterval(progressInterval);
                    progressBar.style.width = '100%';
                    progressText.textContent = '100%';

                    const result = await response.json();

                    if (result.success) {
                        resultMessage.innerHTML = `
                        <div class="p-4 rounded-lg bg-green-100 text-green-800 text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>${result.message} ${result.imported} records imported.</span>
                            </div>
                        </div>
                    `;
                    } else {
                        resultMessage.innerHTML = `
                        <div class="p-4 rounded-lg bg-red-100 text-red-800 text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">error</span>
                                <span>${result.message}</span>
                            </div>
                        </div>
                    `;
                    }

                    resultMessage.classList.remove('hidden');
                } catch (error) {
                    clearInterval(progressInterval);
                    resultMessage.innerHTML = `
                    <div class="p-4 rounded-lg bg-red-100 text-red-800 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">error</span>
                            <span>An error occurred during import. Please try again.</span>
                        </div>
                    </div>
                `;
                    resultMessage.classList.remove('hidden');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="material-symbols-outlined text-sm">upload_file</span><span>Import Data</span>';

                    // Hide progress bar after delay
                    setTimeout(() => {
                        progressContainer.classList.add('hidden');
                        progressBar.style.width = '0%';
                        progressText.textContent = '0%';
                    }, 3000);
                }
            });
        });
    </script>
@endpush
