@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
@php
    $facultyColors = ['#184290', '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'];
    $awardColors = [
        'GOLD' => ['text' => 'text-yellow-600', 'icon' => 'text-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-700'],
        'SILVER' => ['text' => 'text-gray-600', 'icon' => 'text-gray-400', 'badge' => 'bg-gray-100 text-gray-700'],
        'BRONZE' => ['text' => 'text-orange-700', 'icon' => 'text-orange-600', 'badge' => 'bg-orange-100 text-orange-700'],
        'SPECIAL' => ['text' => 'text-primary', 'icon' => 'text-primary', 'badge' => 'bg-primary/10 text-primary'],
        'PLATINUM' => ['text' => 'text-cyan-700', 'icon' => 'text-cyan-500', 'badge' => 'bg-cyan-100 text-cyan-700'],
    ];
    $routeIndex = 'admin.awards.dashboard';
@endphp

<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Dashboard Overview
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Live research awards overview from the system database.
        </p>
    </div>
</header>

<div class="px-8 pb-8">
    <!-- Filter Bar -->
    <div class="rounded-xl border border-divider-subtle/30 bg-white p-4 shadow-sm sm:p-6 mb-6">
        <form method="GET" action="{{ route($routeIndex) }}">
            <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-[1fr_1fr_auto]">
                <div>
                    <label class="mb-2 block text-sm font-medium text-text-main">Year</label>
                    <select name="year" onchange="this.form.submit()" class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                        <option value="">All Years</option>
                        @foreach($senaraiTahunFilter ?? [] as $year)
                            <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-text-main">Faculty</label>
                    <select name="faculty" onchange="this.form.submit()" class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                        <option value="">All Faculties</option>
                        @foreach($senaraiFacultyFilter ?? [] as $faculty)
                            <option value="{{ $faculty->id }}" @selected(request('faculty') == $faculty->id)>{{ $faculty->faculty_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <a href="{{ route($routeIndex) }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-divider-subtle/50 bg-white px-5 py-3 text-sm font-bold text-text-main transition hover:bg-background-light">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total Awards</p>
                <p class="text-3xl font-bold text-text-main" id="total-awards">{{ number_format($stats['total_awards']) }}</p>
                <div class="text-xs text-divider-subtle">records {{ request('year') ? 'in ' . request('year') : 'all years' }}</div>
            </div>
            <div class="bg-primary/10 p-3 rounded-lg text-primary">
                <span class="material-symbols-outlined text-2xl">emoji_events</span>
            </div>
        </div>
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total Staff</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($stats['total_staff']) }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                <span class="material-symbols-outlined text-2xl">people</span>
            </div>
        </div>
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total Projects</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($stats['total_projects']) }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg text-purple-600">
                <span class="material-symbols-outlined text-2xl">biotech</span>
            </div>
        </div>
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle" id="this-year-label">Awards {{ request('year') ? 'in ' . request('year') : 'This Year' }}</p>
                <p class="text-3xl font-bold text-primary" id="this-year-awards">{{ number_format($stats['this_year_awards']) }}</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg text-orange-600">
                <span class="material-symbols-outlined text-2xl">event</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Awards by Faculty & Exhibition Result Chart (Main Chart) -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Awards by Faculty & Exhibition Result</h3>
                <button onclick="refreshChart('faculty')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="facultyChart"></canvas>
            </div>
        </div>

        <!-- Award Levels Distribution Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Award Levels Distribution</h3>
                <button onclick="refreshChart('levels')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="levelsChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main" id="monthly-chart-title">Monthly Awards Trend ({{ request('year') ?: '12 Months' }})</h3>
                <button onclick="refreshChart('monthly')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Exhibition Levels Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Awards by Exhibition Level</h3>
                <button onclick="refreshChart('exhibition')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="exhibitionChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartApiUrl = "{{ route('api.awards.chart-data') }}";
    let facultyChart, levelsChart, monthlyChart, exhibitionChart;

    function countAxis() {
        return {
            beginAtZero: true,
            ticks: {
                precision: 0
            }
        };
    }

    function categoryAxis() {
        return {};
    }

    // Get filter parameters from URL
    function getFilterParams() {
        const params = new URLSearchParams(window.location.search);
        let query = '';
        if (params.get('year')) query += `&year=${params.get('year')}`;
        if (params.get('faculty')) query += `&faculty=${params.get('faculty')}`;
        return query ? query : '';
    }

    // Initialize all charts
    async function initCharts() {
        await Promise.all([
            loadStats(),
            loadFacultyChart(),
            loadLevelsChart(),
            loadMonthlyChart(),
            loadExhibitionChart()
        ]);
    }

    // Load Stats
    async function loadStats() {
        const response = await fetch(`${chartApiUrl}?type=stats${getFilterParams()}`);
        const data = await response.json();

        document.getElementById('total-awards').textContent = new Intl.NumberFormat().format(data.total_awards);
        document.getElementById('this-year-awards').textContent = new Intl.NumberFormat().format(data.this_year_awards);

        // Update the label for the "Awards This Year" card
        const params = new URLSearchParams(window.location.search);
        const selectedYear = params.get('year');
        const labelElement = document.getElementById('this-year-label');
        if (labelElement) {
            labelElement.textContent = `Awards ${selectedYear ? 'in ' + selectedYear : 'This Year'}`;
        }
    }

    // Load Faculty Chart (Stacked Bar - Awards by Exhibition Result)
    async function loadFacultyChart() {
        const response = await fetch(`${chartApiUrl}?type=faculties${getFilterParams()}`);
        const data = await response.json();

        const ctx = document.getElementById('facultyChart').getContext('2d');

        if (facultyChart) facultyChart.destroy();

        // Check if data is empty
        if (!data || data.length === 0) {
            facultyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['No Data'],
                    datasets: [{
                        label: 'No awards found',
                        data: [0],
                        backgroundColor: '#E5E7EB'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: countAxis(),
                        y: categoryAxis()
                    }
                }
            });
            return;
        }

        // Get unique exhibition results
        const results = [...new Set(data.map(d => d.result_description))].sort();
        const faculties = [...new Set(data.map(d => d.faculty_code))].sort();

        // Create datasets for each exhibition result
        const colors = ['#184290', '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#EC4899', '#6366F1'];
        const datasets = results.map((result, index) => {
            return {
                label: result,
                data: faculties.map(faculty => {
                    const item = data.find(d => d.faculty_code === faculty && d.result_description === result);
                    return item ? item.count : 0;
                }),
                backgroundColor: colors[index % colors.length],
                borderRadius: 4
            };
        });

        facultyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: faculties,
                datasets: datasets
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                },
                scales: {
                    x: {
                        ...countAxis(),
                        stacked: true
                    },
                    y: {
                        ...categoryAxis(),
                        stacked: true
                    }
                }
            }
        });
    }

    // Load Exhibition Results Chart (Vertical Bar)
    async function loadResultsChart() {
        const response = await fetch(`${chartApiUrl}?type=results${getFilterParams()}`);
        const data = await response.json();

        const ctx = document.getElementById('resultsChart').getContext('2d');

        if (resultsChart) resultsChart.destroy();

        resultsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(r => r.description || 'Unknown'),
                datasets: [{
                    label: 'Awards',
                    data: data.map(r => r.count),
                    backgroundColor: ['#184290', '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: categoryAxis(),
                    y: countAxis()
                }
            }
        });
    }

    // Load Levels Chart (Doughnut)
    async function loadLevelsChart() {
        const response = await fetch(`${chartApiUrl}?type=levels${getFilterParams()}`);
        const data = await response.json();

        const ctx = document.getElementById('levelsChart').getContext('2d');

        if (levelsChart) levelsChart.destroy();

        // Check if data is empty
        if (!data || data.length === 0) {
            levelsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['No Data'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['#E5E7EB'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
            return;
        }

        const colors = {
            'Gold': '#FFD700',
            'GOLD': '#FFD700',
            'Silver': '#C0C0C0',
            'SILVER': '#C0C0C0',
            'Bronze': '#CD7F32',
            'BRONZE': '#CD7F32',
            'Platinum': '#E5E4E2',
            'PLATINUM': '#E5E4E2',
            'Special': '#9333EA',
            'SPECIAL': '#9333EA',
            'OTHERS': '#6B7280',
            'N/A': '#6B7280'
        };

        levelsChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.award_level),
                datasets: [{
                    data: data.map(d => d.count),
                    backgroundColor: data.map(d => colors[d.award_level] || '#6B7280'),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12 }
                    }
                }
            }
        });
    }

    // Load Monthly Trend Chart (Line)
    async function loadMonthlyChart() {
        const response = await fetch(`${chartApiUrl}?type=monthly${getFilterParams()}`);
        const data = await response.json();

        const ctx = document.getElementById('monthlyChart').getContext('2d');

        if (monthlyChart) monthlyChart.destroy();

        // Check if data is empty
        if (!data || data.length === 0) {
            monthlyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['No Data'],
                    datasets: [{
                        label: 'No awards found',
                        data: [0],
                        borderColor: '#E5E7EB',
                        backgroundColor: 'rgba(229, 231, 235, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#E5E7EB',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: categoryAxis(),
                        y: countAxis()
                    }
                }
            });
            return;
        }

        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const params = new URLSearchParams(window.location.search);
        const selectedYear = params.get('year');

        let labels, chartData;

        if (selectedYear) {
            // Show all months for the selected year
            const monthMap = {};
            data.forEach(d => {
                monthMap[d.month] = d.count;
            });

            labels = monthNames;
            chartData = Array.from({length: 12}, (_, i) => monthMap[i + 1] || 0);
        } else {
            // Show last 12 months with year in label
            labels = data.map(d => `${monthNames[d.month - 1]} ${d.year}`);
            chartData = data.map(d => d.count);
        }

        monthlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Awards',
                    data: chartData,
                    borderColor: '#184290',
                    backgroundColor: 'rgba(24, 66, 144, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#184290',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: categoryAxis(),
                    y: countAxis()
                }
            }
        });
    }

    // Load Exhibition Levels Chart (Pie)
    async function loadExhibitionChart() {
        const response = await fetch(`${chartApiUrl}?type=exhibition${getFilterParams()}`);
        const data = await response.json();

        const ctx = document.getElementById('exhibitionChart').getContext('2d');

        if (exhibitionChart) exhibitionChart.destroy();

        // Check if data is empty
        if (!data || data.length === 0) {
            exhibitionChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['No Data'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['#E5E7EB'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
            return;
        }

        exhibitionChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.map(d => d.exhibition_level || 'Unknown'),
                datasets: [{
                    data: data.map(d => d.count),
                    backgroundColor: ['#184290', '#10B981', '#F59E0B', '#8B5CF6'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12 }
                    }
                }
            }
        });
    }

    // Refresh specific chart
    async function refreshChart(type) {
        switch(type) {
            case 'faculty': await loadFacultyChart(); break;
            case 'levels': await loadLevelsChart(); break;
            case 'monthly': await loadMonthlyChart(); break;
            case 'exhibition': await loadExhibitionChart(); break;
        }
    }

    // Auto-refresh charts every 30 seconds
    setInterval(async () => {
        await initCharts();
    }, 30000);

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initCharts);
</script>
@endpush

@endsection
