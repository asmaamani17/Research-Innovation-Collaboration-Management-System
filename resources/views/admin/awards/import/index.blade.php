@extends('layouts.admin')

@section('title', 'Import Data')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                Import Data
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Import data from CSV files into the system.
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
                    <!-- Excel Template RD8_1_New Button -->
                    <a href="{{ route('admin.import.template', ['type' => 'Template_RD8_1_New']) }}"
                        class="group bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <span class="material-symbols-outlined text-3xl text-blue-600">description</span>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-200/80 px-2.5 py-1 rounded-full">Excel Template</span>
                        </div>
                        <h4 class="font-bold text-text-main mb-2 group-hover:text-blue-700 transition-colors">Template_RD8_1_New</h4>
                        <p class="text-sm text-divider-subtle">Download UTeM format Excel template</p>
                    </a>

                    <!-- Excel Template RD8_New Button -->
                    <a href="{{ route('admin.import.template', ['type' => 'Template_RD8_New']) }}"
                        class="group bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <span class="material-symbols-outlined text-3xl text-green-600">table_chart</span>
                            <span class="text-xs font-semibold text-green-600 bg-green-200/80 px-2.5 py-1 rounded-full">Excel Template</span>
                        </div>
                        <h4 class="font-bold text-text-main mb-2 group-hover:text-green-700 transition-colors">Template_RD8_New</h4>
                        <p class="text-sm text-divider-subtle">Download standard Excel template</p>
                    </a>
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="px-8 py-6 border-b border-divider-subtle/30">
                <h3 class="text-lg font-bold text-text-main mb-4">Instructions</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">looks_one</span>
                        <p class="text-sm text-text-main/80">Download the appropriate template for the data you want to
                            import.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">looks_two</span>
                        <p class="text-sm text-text-main/80">Fill in the CSV file with your data following the template
                            format exactly.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">looks_3</span>
                        <p class="text-sm text-text-main/80">Select the data type and upload your CSV file below.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">looks_4</span>
                        <p class="text-sm text-text-main/80">Wait for the import to complete. The progress bar will show the
                            status.</p>
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
                        <label class="block text-sm font-medium text-text-main mb-3">Select Data Format</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="templateSelectionGrid">
                            <!-- Card 1: Template_RD8_1_New -->
                            <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 border-primary bg-primary/5 shadow-sm" id="card_Template_RD8_1_New">
                                <input type="radio" name="type" value="Template_RD8_1_New" checked class="sr-only" onchange="selectImportTemplate('Template_RD8_1_New')">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">description</span>
                                        <span class="font-bold text-text-main text-sm">Template_RD8_1_New</span>
                                    </div>
                                    <span class="material-symbols-outlined text-primary text-xl check-icon">check_circle</span>
                                </div>
                                <p class="text-xs text-divider-subtle">Person Recognition & Staff mapping format</p>
                            </label>

                            <!-- Card 2: Template_RD8_New -->
                            <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 border-divider-subtle/30 bg-white hover:border-primary/50" id="card_Template_RD8_New">
                                <input type="radio" name="type" value="Template_RD8_New" class="sr-only" onchange="selectImportTemplate('Template_RD8_New')">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-green-600">table_chart</span>
                                        <span class="font-bold text-text-main text-sm">Template_RD8_New</span>
                                    </div>
                                    <span class="material-symbols-outlined text-divider-subtle text-xl check-icon hidden">check_circle</span>
                                </div>
                                <p class="text-xs text-divider-subtle">Project Awards & Exhibition details format</p>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-main mb-2">Upload CSV File</label>
                        <div class="relative">
                            <input type="file" name="file" id="csvFile" accept=".csv,.txt" required
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
        function selectImportTemplate(type) {
            document.querySelectorAll('#templateSelectionGrid label').forEach(card => {
                card.classList.remove('border-primary', 'bg-primary/5', 'shadow-sm');
                card.classList.add('border-divider-subtle/30', 'bg-white');
                const icon = card.querySelector('.check-icon');
                if (icon) icon.classList.add('hidden');
            });

            const activeCard = document.getElementById('card_' + type);
            if (activeCard) {
                activeCard.classList.remove('border-divider-subtle/30', 'bg-white');
                activeCard.classList.add('border-primary', 'bg-primary/5', 'shadow-sm');
                const icon = activeCard.querySelector('.check-icon');
                if (icon) icon.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const importForm = document.getElementById('importForm');
            const csvFile = document.getElementById('csvFile');
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

                const selectedType = document.querySelector('input[name="type"]:checked')?.value || 'Template_RD8_1_New';

                const formData = new FormData();
                formData.append('file', csvFile.files[0]);
                formData.append('type', selectedType);
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