@extends('layouts.admin')

@section('title', 'Edit KPI')

@section('content')
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <div class="text-divider-subtle text-sm uppercase tracking-[0.25em] font-semibold">ST2 Dashboard</div>
            <h2 class="text-text-main text-3xl font-black leading-tight">UTeM ST2 Pelan Strategik 2026-2030</h2>
            <p class="text-divider-subtle text-sm mt-2">Memperkasa RDICE, Melestari Universiti</p>
        </div>
        <div class="flex flex-col items-end gap-3">
            <div
                class="inline-flex items-center gap-2 rounded-full border border-divider-subtle/50 bg-white px-4 py-2 shadow-sm">
                <span class="material-symbols-outlined text-base text-primary">calendar_month</span>
                <span class="text-sm font-semibold">Kemaskini: {{ now()->format('j/n/Y, g:i:s A') }}</span>
            </div>
        </div>
    </header>

    <div class="px-8 pb-8">
        <!-- Session Messages -->
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white border border-divider-subtle/30 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-divider-subtle/30 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <select id="yearSelector"
                        class="px-4 py-2 rounded-lg border border-divider-subtle/70 bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50">
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-divider-subtle">Klik pada sel untuk edit. Tekan Enter untuk simpan.</p>
                </div>
                <a href="#" id="exportBtn"
                    class="px-4 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">download</span>
                    <span class="font-medium text-sm">Export</span>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-divider-subtle text-sm" id="kpiTable">
                    <thead class="bg-background-light">
                        <tr>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main sticky left-0 bg-background-light z-10">Strategi</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main sticky left-20 bg-background-light z-10 min-w-[200px]">Inisiatif</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main min-w-[300px]">Petunjuk Prestasi</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main min-w-[120px]">Sasaran</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main min-w-[100px]">Fasa 1</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main min-w-[100px]">Fasa 2</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main min-w-[100px]">Fasa 3</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main min-w-[100px]">Fasa 4</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main min-w-[100px]">Pencapaian</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main min-w-[100px]">Peratus</th>
                            <th class="px-4 py-3 text-left uppercase font-semibold text-text-main min-w-[250px]">Maklumat Pencapaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle" id="kpiTableBody">
                        @foreach($strategies as $strategy)
                            <tr class="strategy-row bg-blue-50/30">
                                <td class="px-4 py-3 font-bold text-primary sticky left-0 bg-blue-50/30 z-10">{{ $strategy->strategy_code }}</td>
                                <td class="px-4 py-3 font-bold text-primary sticky left-20 bg-blue-50/30 z-10" colspan="10">{{ $strategy->strategy_name }}</td>
                            </tr>
                            @foreach($strategy->kpiRecords as $kpi)
                                @php
                                    $kpiYear = $kpi->kpiYears->where('target_year', $currentYear)->first();
                                    $phases = $kpiYear ? $kpiYear->phases->keyBy('phase') : collect();
                                    
                                    $parseValue = function($value) {
                                        if ($value === null || $value === '') return 0;
                                        $cleaned = str_replace(['RM', ',', ' '], '', (string) $value);
                                        return is_numeric($cleaned) ? floatval($cleaned) : 0;
                                    };
                                    
                                    $phase1 = $parseValue($phases->get('Phase 1')->achievement ?? 0);
                                    $phase2 = $parseValue($phases->get('Phase 2')->achievement ?? 0);
                                    $phase3 = $parseValue($phases->get('Phase 3')->achievement ?? 0);
                                    $phase4 = $parseValue($phases->get('Phase 4')->achievement ?? 0);
                                    $totalAchievement = $phase1 + $phase2 + $phase3 + $phase4;
                                    $targetValue = $kpiYear ? $kpiYear->target_value : 0;
                                    $percentage = $kpiYear ? $kpiYear->achievement_percentage : 0;
                                    $achievementInfo = $kpiYear ? $kpiYear->achievement_information : '';
                                @endphp
                                <tr class="kpi-row">
                                    <td class="px-4 py-3 sticky left-0 bg-white z-10"></td>
                                    <td class="px-4 py-3 sticky left-20 bg-white z-10">{{ $kpi->initiative }}</td>
                                    <td class="px-4 py-3 sticky left-20 bg-white z-10">{{ $kpi->initiative }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="performance_indicator">{{ $kpi->kpi_code }} {{ $kpi->performance_indicator }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="target_value">{{ $targetValue }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="phase1">{{ $phase1 }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="phase2">{{ $phase2 }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="phase3">{{ $phase3 }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="phase4">{{ $phase4 }}</td>
                                    <td class="px-4 py-3 achievement-cell" data-kpi="{{ $kpi->kpi_code }}">{{ $totalAchievement }}</td>
                                    <td class="px-4 py-3 percentage-cell" data-kpi="{{ $kpi->kpi_code }}">{{ number_format($percentage, 1) }}%</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="achievement_information" data-full-text="{{ $achievementInfo }}">
                                        <div class="max-h-12 overflow-y-auto text-sm" title="{{ $achievementInfo }}">
                                            {{ \Illuminate\Support\Str::limit($achievementInfo, 100) }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-divider-subtle/30 flex justify-end">
                <button id="saveAllBtn"
                    class="px-6 py-2.5 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">save</span>
                    <span class="font-medium text-sm">Simpan Semua</span>
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editableCells = document.querySelectorAll('.editable-cell');
            const saveAllBtn = document.getElementById('saveAllBtn');
            const yearSelector = document.getElementById('yearSelector');
            const exportBtn = document.getElementById('exportBtn');
            let currentEditingCell = null;

            // Export button - use selected year
            exportBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const selectedYear = yearSelector.value;
                window.location.href = `{{ route('admin.kpi.export') }}?year=${selectedYear}`;
            });

            // Make cells editable on click
            editableCells.forEach(cell => {
                cell.addEventListener('click', function(e) {
                    // Don't trigger if clicking on an existing input
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                        e.stopPropagation();
                        return;
                    }
                    
                    if (currentEditingCell && currentEditingCell !== this) {
                        currentEditingCell.blur();
                    }
                    
                    // Check if already editing
                    if (this.querySelector('input') || this.querySelector('textarea')) {
                        return;
                    }
                    
                    const currentValue = this.innerHTML.trim();
                    const textValue = this.textContent.trim();
                    const isMultiline = this.dataset.field === 'achievement_information';
                    
                    if (isMultiline) {
                        const fullText = this.dataset.fullText || textValue;
                        const textarea = document.createElement('textarea');
                        textarea.value = fullText;
                        textarea.className = 'w-full px-2 py-1 border border-primary rounded focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none';
                        textarea.rows = 3;
                        
                        this.innerHTML = '';
                        this.appendChild(textarea);
                        textarea.focus();
                        textarea.select();
                        currentEditingCell = this;

                        const saveCell = () => {
                            const newValue = textarea.value.trim();
                            const displayValue = newValue ? (newValue.length > 100 ? newValue.substring(0, 100) + '...' : newValue) : '';
                            this.innerHTML = '<div class="max-h-12 overflow-y-auto text-sm" title="' + newValue.replace(/"/g, '&quot;') + '">' + displayValue + '</div>';
                            this.dataset.fullText = newValue;
                            this.classList.add('bg-yellow-50');
                            setTimeout(() => this.classList.remove('bg-yellow-50'), 1000);
                            updateCalculations(this.dataset.kpi);
                            currentEditingCell = null;
                        };

                        textarea.addEventListener('blur', saveCell);
                        textarea.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter' && !e.shiftKey) {
                                e.preventDefault();
                                textarea.blur();
                            } else if (e.key === 'Escape') {
                                this.innerHTML = currentValue;
                                currentEditingCell = null;
                            }
                        });
                        
                        // Prevent textarea clicks from bubbling to cell
                        textarea.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    } else {
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.value = textValue;
                        input.className = 'w-full px-2 py-1 border border-primary rounded focus:outline-none focus:ring-2 focus:ring-primary/50';
                        
                        this.innerHTML = '';
                        this.appendChild(input);
                        input.focus();
                        input.select();
                        currentEditingCell = this;

                        const saveCell = () => {
                            const newValue = input.value.trim();
                            this.innerHTML = newValue || currentValue;
                            this.classList.add('bg-yellow-50');
                            setTimeout(() => this.classList.remove('bg-yellow-50'), 1000);
                            updateCalculations(this.dataset.kpi);
                            currentEditingCell = null;
                        };

                        input.addEventListener('blur', saveCell);
                        input.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                input.blur();
                            } else if (e.key === 'Escape') {
                                this.innerHTML = currentValue;
                                currentEditingCell = null;
                            }
                        });
                        
                        // Prevent input clicks from bubbling to cell
                        input.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    }
                });
            });

            // Update calculations when values change
            function updateCalculations(kpiCode) {
                const row = document.querySelector(`[data-kpi="${kpiCode}"]`).closest('tr');
                const phase1 = parseFloat(row.querySelector('[data-field="phase1"]')?.textContent) || 0;
                const phase2 = parseFloat(row.querySelector('[data-field="phase2"]')?.textContent) || 0;
                const phase3 = parseFloat(row.querySelector('[data-field="phase3"]')?.textContent) || 0;
                const phase4 = parseFloat(row.querySelector('[data-field="phase4"]')?.textContent) || 0;
                const target = parseFloat(row.querySelector('[data-field="target_value"]')?.textContent) || 0;
                
                const totalAchievement = phase1 + phase2 + phase3 + phase4;
                const percentage = target > 0 ? Math.min(100, (totalAchievement / target) * 100) : 0;
                
                const achievementCell = row.querySelector('.achievement-cell');
                const percentageCell = row.querySelector('.percentage-cell');
                
                if (achievementCell) {
                    achievementCell.textContent = totalAchievement.toLocaleString('ms-MY', { maximumFractionDigits: 2 });
                }
                if (percentageCell) {
                    percentageCell.textContent = percentage.toFixed(1) + '%';
                }
            }

            // Save all changes
            saveAllBtn.addEventListener('click', function() {
                const changes = [];
                
                editableCells.forEach(cell => {
                    const kpiCode = cell.dataset.kpi;
                    const field = cell.dataset.field;
                    const value = cell.textContent.trim();
                    
                    changes.push({
                        kpi_code: kpiCode,
                        field: field,
                        value: value,
                        year: yearSelector.value
                    });
                });

                // Disable button and show loading
                saveAllBtn.disabled = true;
                saveAllBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">refresh</span><span class="font-medium text-sm">Menyimpan...</span>';

                // Send changes to backend
                fetch('{{ route('admin.kpi.updateKpi') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ changes: changes })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const successDiv = document.createElement('div');
                        successDiv.className = 'rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 mb-4';
                        successDiv.textContent = 'Data berjaya disimpan!';
                        const container = document.querySelector('.px-8.pb-8');
                        container.insertBefore(successDiv, container.firstChild);
                        
                        // Remove after 3 seconds
                        setTimeout(() => successDiv.remove(), 3000);
                    } else {
                        // Show error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 mb-4';
                        errorDiv.textContent = data.error || 'Ralat berlaku semasa menyimpan data.';
                        const container = document.querySelector('.px-8.pb-8');
                        container.insertBefore(errorDiv, container.firstChild);
                        
                        // Remove after 5 seconds
                        setTimeout(() => errorDiv.remove(), 5000);
                    }
                })
                .catch(error => {
                    // Show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 mb-4';
                    errorDiv.textContent = 'Ralat sambungan. Sila cuba lagi.';
                    const container = document.querySelector('.px-8.pb-8');
                    container.insertBefore(errorDiv, container.firstChild);
                    
                    // Remove after 5 seconds
                    setTimeout(() => errorDiv.remove(), 5000);
                })
                .finally(() => {
                    // Re-enable button
                    saveAllBtn.disabled = false;
                    saveAllBtn.innerHTML = '<span class="material-symbols-outlined text-lg">save</span><span class="font-medium text-sm">Simpan Semua</span>';
                });
            });

            // Year selector change
            yearSelector.addEventListener('change', function() {
                window.location.href = `{{ route('admin.kpi.edit') }}?year=${this.value}`;
            });
        });
    </script>
@endpush