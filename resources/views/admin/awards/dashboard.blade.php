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
    <a href="{{ route('admin.awards') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        <span>New Award</span>
    </a>
</header>

<div class="px-8 pb-8">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total Awards</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($stats['total_awards']) }}</p>
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
                <p class="text-sm font-medium text-divider-subtle">Awards This Year</p>
                <p class="text-3xl font-bold text-primary">{{ number_format($stats['this_year_awards']) }}</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg text-orange-600">
                <span class="material-symbols-outlined text-2xl">event</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Awards by Faculty & Exhibition Result Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
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

        <!-- Award Types Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Award Types</h3>
                <button onclick="refreshChart('types')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="typesChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Monthly Awards Trend (12 Months)</h3>
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
    let facultyChart, levelsChart, typesChart, monthlyChart, exhibitionChart;

    // Initialize all charts
    async function initCharts() {
        await Promise.all([
            loadFacultyChart(),
            loadLevelsChart(),
            loadTypesChart(),
            loadMonthlyChart(),
            loadExhibitionChart()
        ]);
    }

    // Load Faculty Chart (Stacked Bar - Awards by Exhibition Result)
    async function loadFacultyChart() {
        const response = await fetch(`${chartApiUrl}?type=faculties`);
        const data = await response.json();

        const ctx = document.getElementById('facultyChart').getContext('2d');

        if (facultyChart) facultyChart.destroy();

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
                        beginAtZero: true,
                        stacked: true
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });
    }

    // Load Exhibition Results Chart (Vertical Bar)
    async function loadResultsChart() {
        const response = await fetch(`${chartApiUrl}?type=results`);
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
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Load Levels Chart (Doughnut)
    async function loadLevelsChart() {
        const response = await fetch(`${chartApiUrl}?type=levels`);
        const data = await response.json();

        const ctx = document.getElementById('levelsChart').getContext('2d');

        if (levelsChart) levelsChart.destroy();

        const colors = {
            'Gold': '#FFD700',
            'Silver': '#C0C0C0',
            'Bronze': '#CD7F32',
            'Platinum': '#E5E4E2',
            'Special': '#9333EA',
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

    // Load Types Chart (Vertical Bar)
    async function loadTypesChart() {
        const response = await fetch(`${chartApiUrl}?type=types`);
        const data = await response.json();
        
        const ctx = document.getElementById('typesChart').getContext('2d');
        
        if (typesChart) typesChart.destroy();
        
        typesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.award_type),
                datasets: [{
                    label: 'Count',
                    data: data.map(d => d.count),
                    backgroundColor: '#184290',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }

    // Load Monthly Trend Chart (Line)
    async function loadMonthlyChart() {
        const response = await fetch(`${chartApiUrl}?type=monthly`);
        const data = await response.json();
        
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        if (monthlyChart) monthlyChart.destroy();
        
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const labels = data.map(d => `${monthNames[d.month - 1]} ${d.year}`);
        
        monthlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Awards',
                    data: data.map(d => d.count),
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
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Load Exhibition Levels Chart (Pie)
    async function loadExhibitionChart() {
        const response = await fetch(`${chartApiUrl}?type=exhibition`);
        const data = await response.json();
        
        const ctx = document.getElementById('exhibitionChart').getContext('2d');
        
        if (exhibitionChart) exhibitionChart.destroy();
        
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
            case 'types': await loadTypesChart(); break;
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