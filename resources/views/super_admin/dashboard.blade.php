@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Super Admin Dashboard
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Overview of all modules and activities across the system.
        </p>
    </div>
</header>

<div class="px-8 pb-8">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total Awards</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($totalAwards) }}</p>
            </div>
            <div class="bg-primary/10 p-3 rounded-lg text-primary">
                <span class="material-symbols-outlined text-2xl">emoji_events</span>
            </div>
        </div>

        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total Products</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($totalProducts) }}</p>
            </div>
            <div class="bg-green-100 p-3 rounded-lg text-green-600">
                <span class="material-symbols-outlined text-2xl">inventory_2</span>
            </div>
        </div>

        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total Projects</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($totalProjects) }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg text-purple-600">
                <span class="material-symbols-outlined text-2xl">biotech</span>
            </div>
        </div>

        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total Staff</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($totalStaff) }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                <span class="material-symbols-outlined text-2xl">people</span>
            </div>
        </div>

        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total Competitions</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($totalCompetitions) }}</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg text-orange-600">
                <span class="material-symbols-outlined text-2xl">event</span>
            </div>
        </div>

        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">KPI Strategies</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($totalKpiStrategies) }}</p>
            </div>
            <div class="bg-indigo-100 p-3 rounded-lg text-indigo-600">
                <span class="material-symbols-outlined text-2xl">assessment</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Awards by Level Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Awards by Level</h3>
                <button onclick="refreshChart('awardsByLevel')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="awardsByLevelChart"></canvas>
            </div>
        </div>

        <!-- Awards by Year Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Awards by Year</h3>
                <button onclick="refreshChart('awardsByYear')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="awardsByYearChart"></canvas>
            </div>
        </div>

        <!-- Products by Status Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Products by Development Status</h3>
                <button onclick="refreshChart('productsByStatus')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="productsByStatusChart"></canvas>
            </div>
        </div>

        <!-- Products by Category Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Products by Category</h3>
                <button onclick="refreshChart('productsByCategory')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="productsByCategoryChart"></canvas>
            </div>
        </div>

        <!-- KPI Achievement by Year Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">KPI Achievement by Year</h3>
                <button onclick="refreshChart('kpiByYear')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="kpiByYearChart"></canvas>
            </div>
        </div>

        <!-- Staff by Faculty Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Staff by Faculty</h3>
                <button onclick="refreshChart('staffByFaculty')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="staffByFacultyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Recent Awards -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Recent Awards</h3>
                <a href="{{ route('admin.awards') }}" class="text-sm font-semibold text-primary hover:text-primary/80">
                    View All
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentAwards as $award)
                <div class="flex items-center justify-between p-3 bg-background-light rounded-lg border border-divider-subtle/20">
                    <div class="flex items-center gap-3">
                        <div class="bg-primary/10 p-2 rounded-lg">
                            <span class="material-symbols-outlined text-primary text-lg">emoji_events</span>
                        </div>
                        <div>
                            <p class="font-medium text-text-main">{{ $award->award_name }}</p>
                            <p class="text-sm text-divider-subtle">{{ $award->award_level }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-divider-subtle">{{ $award->award_date ? $award->award_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-divider-subtle text-center py-4">No recent awards</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Recent Projects</h3>
                <a href="{{ route('admin.projects.index') }}" class="text-sm font-semibold text-primary hover:text-primary/80">
                    View All
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentProjects as $project)
                <div class="flex items-center justify-between p-3 bg-background-light rounded-lg border border-divider-subtle/20">
                    <div class="flex items-center gap-3">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            <span class="material-symbols-outlined text-purple-600 text-lg">biotech</span>
                        </div>
                        <div>
                            <p class="font-medium text-text-main">{{ $project->project_title }}</p>
                            <p class="text-sm text-divider-subtle">{{ $project->grant_no }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-divider-subtle">{{ $project->created_at ? $project->created_at->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-divider-subtle text-center py-4">No recent projects</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartApiUrl = "{{ route('superadmin.dashboard.data') }}";
    let awardsByLevelChart, awardsByYearChart, productsByStatusChart, productsByCategoryChart, kpiByYearChart, staffByFacultyChart;

    // Initialize all charts
    async function initCharts() {
        await Promise.all([
            loadAwardsByLevelChart(),
            loadAwardsByYearChart(),
            loadProductsByStatusChart(),
            loadProductsByCategoryChart(),
            loadKpiByYearChart(),
            loadStaffByFacultyChart()
        ]);
    }

    // Load Awards by Level Chart (Doughnut)
    async function loadAwardsByLevelChart() {
        const response = await fetch(chartApiUrl);
        const data = await response.json();
        
        const ctx = document.getElementById('awardsByLevelChart').getContext('2d');
        
        if (awardsByLevelChart) awardsByLevelChart.destroy();
        
        awardsByLevelChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.awardsByLevel.map(d => d.level),
                datasets: [{
                    data: data.awardsByLevel.map(d => d.count),
                    backgroundColor: ['#184290', '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'],
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

    // Load Awards by Year Chart (Bar)
    async function loadAwardsByYearChart() {
        const response = await fetch(chartApiUrl);
        const data = await response.json();
        
        const ctx = document.getElementById('awardsByYearChart').getContext('2d');
        
        if (awardsByYearChart) awardsByYearChart.destroy();
        
        awardsByYearChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.awardsByYear.map(d => d.year),
                datasets: [{
                    label: 'Awards',
                    data: data.awardsByYear.map(d => d.count),
                    backgroundColor: '#184290',
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

    // Load Products by Status Chart (Pie)
    async function loadProductsByStatusChart() {
        const response = await fetch(chartApiUrl);
        const data = await response.json();
        
        const ctx = document.getElementById('productsByStatusChart').getContext('2d');
        
        if (productsByStatusChart) productsByStatusChart.destroy();
        
        productsByStatusChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.productsByStatus.map(d => d.status),
                datasets: [{
                    data: data.productsByStatus.map(d => d.count),
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
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

    // Load Products by Category Chart (Horizontal Bar)
    async function loadProductsByCategoryChart() {
        const response = await fetch(chartApiUrl);
        const data = await response.json();
        
        const ctx = document.getElementById('productsByCategoryChart').getContext('2d');
        
        if (productsByCategoryChart) productsByCategoryChart.destroy();
        
        productsByCategoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.productsByCategory.map(d => d.category),
                datasets: [{
                    label: 'Products',
                    data: data.productsByCategory.map(d => d.count),
                    backgroundColor: '#10B981',
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

    // Load KPI Achievement by Year Chart (Line)
    async function loadKpiByYearChart() {
        const response = await fetch(chartApiUrl);
        const data = await response.json();
        
        const ctx = document.getElementById('kpiByYearChart').getContext('2d');
        
        if (kpiByYearChart) kpiByYearChart.destroy();
        
        kpiByYearChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.kpiByYear.map(d => d.year),
                datasets: [{
                    label: 'Average Achievement %',
                    data: data.kpiByYear.map(d => d.achievement),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
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
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    // Load Staff by Faculty Chart (Horizontal Bar)
    async function loadStaffByFacultyChart() {
        const response = await fetch(chartApiUrl);
        const data = await response.json();
        
        const ctx = document.getElementById('staffByFacultyChart').getContext('2d');
        
        if (staffByFacultyChart) staffByFacultyChart.destroy();
        
        staffByFacultyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.staffByFaculty.map(d => d.faculty),
                datasets: [{
                    label: 'Staff',
                    data: data.staffByFaculty.map(d => d.count),
                    backgroundColor: '#F59E0B',
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

    // Refresh individual chart
    function refreshChart(chartName) {
        switch(chartName) {
            case 'awardsByLevel':
                loadAwardsByLevelChart();
                break;
            case 'awardsByYear':
                loadAwardsByYearChart();
                break;
            case 'productsByStatus':
                loadProductsByStatusChart();
                break;
            case 'productsByCategory':
                loadProductsByCategoryChart();
                break;
            case 'kpiByYear':
                loadKpiByYearChart();
                break;
            case 'staffByFaculty':
                loadStaffByFacultyChart();
                break;
        }
    }

    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', initCharts);

    // Auto-refresh every 30 seconds
    setInterval(initCharts, 30000);
</script>
@endpush
