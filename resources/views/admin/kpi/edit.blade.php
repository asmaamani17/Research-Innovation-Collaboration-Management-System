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
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.kpi.index') }}"
                    class="px-4 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors border border-blue-200">
                    <span class="material-symbols-outlined text-lg">dashboard</span>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>
                <!-- <a href="{{ route('admin.import.index') }}"
                    class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined text-lg">upload_file</span>
                    <span class="font-medium text-sm">Import</span>
                </a> -->
                <a href="{{ route('admin.kpi.export', ['year' => $currentYear]) }}"
                    class="px-4 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                    <span class="material-symbols-outlined text-lg">download</span>
                    <span class="font-medium text-sm">Export</span>
                </a>
                <a href="{{ route('admin.kpi.template', ['type' => 'kpi']) }}"
                    class="px-4 py-2 rounded-lg bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors border border-slate-200">
                    <span class="material-symbols-outlined text-lg">description</span>
                    <span class="font-medium text-sm">Template</span>
                </a>
            </div>
        </div>
    </header>

    <div class="px-8 pb-8">
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
                <button id="saveAllBtn"
                    class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">save</span>
                    <span class="font-medium text-sm">Simpan Semua</span>
                </button>
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle" id="kpiTableBody">
                        @foreach($strategies as $strategy)
                            <tr class="strategy-row bg-blue-50/30">
                                <td class="px-4 py-3 font-bold text-primary sticky left-0 bg-blue-50/30 z-10">{{ $strategy->strategy_code }}</td>
                                <td class="px-4 py-3 font-bold text-primary sticky left-20 bg-blue-50/30 z-10" colspan="9">{{ $strategy->strategy_name }}</td>
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
                                @endphp
                                <tr class="kpi-row">
                                    <td class="px-4 py-3 sticky left-0 bg-white z-10"></td>
                                    <td class="px-4 py-3 sticky left-20 bg-white z-10">{{ $kpi->initiative }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="performance_indicator">{{ $kpi->kpi_code }} {{ $kpi->performance_indicator }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="target_value">{{ $targetValue }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="phase1">{{ $phase1 }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="phase2">{{ $phase2 }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="phase3">{{ $phase3 }}</td>
                                    <td class="px-4 py-3 editable-cell" data-kpi="{{ $kpi->kpi_code }}" data-field="phase4">{{ $phase4 }}</td>
                                    <td class="px-4 py-3 achievement-cell" data-kpi="{{ $kpi->kpi_code }}">{{ $totalAchievement }}</td>
                                    <td class="px-4 py-3 percentage-cell" data-kpi="{{ $kpi->kpi_code }}">{{ number_format($percentage, 1) }}%</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
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
            let currentEditingCell = null;

            // Make cells editable on click
            editableCells.forEach(cell => {
                cell.addEventListener('click', function(e) {
                    // Don't trigger if clicking on an existing input
                    if (e.target.tagName === 'INPUT') {
                        e.stopPropagation();
                        return;
                    }
                    
                    if (currentEditingCell && currentEditingCell !== this) {
                        currentEditingCell.blur();
                    }
                    
                    // Check if already editing
                    if (this.querySelector('input')) {
                        return;
                    }
                    
                    const currentValue = this.innerHTML.trim();
                    const textValue = this.textContent.trim();
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

                // Here you would send the changes to your backend
                console.log('Saving changes:', changes);
                alert('Data disimpan! (Dalam implementasi sebenar, data akan dihantar ke server)');
            });

            // Year selector change
            yearSelector.addEventListener('change', function() {
                window.location.href = `{{ route('admin.kpi.edit') }}?year=${this.value}`;
            });
        });
    </script>
@endpush