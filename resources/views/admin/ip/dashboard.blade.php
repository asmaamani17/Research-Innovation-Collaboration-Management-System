@extends('layouts.admin')

@section('title', 'IP Dashboard Overview')

@section('content')
@php
    $ipColors = ['#184290', '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'];
@endphp

<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            IP Dashboard Overview
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Live intellectual properties overview from the system database.
        </p>
    </div>
    <a href="{{ route('admin.ip.create') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        <span>New IP</span>
    </a>
</header>

<div class="px-8 pb-8">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Total IPs</p>
                <p class="text-3xl font-bold text-text-main">{{ number_format($stats['total_ips']) }}</p>
            </div>
            <div class="bg-primary/10 p-3 rounded-lg text-primary">
                <span class="material-symbols-outlined text-2xl">inventory_2</span>
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
                <p class="text-sm font-medium text-divider-subtle">IPs This Year</p>
                <p class="text-3xl font-bold text-primary">{{ number_format($stats['this_year_ips']) }}</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg text-orange-600">
                <span class="material-symbols-outlined text-2xl">event</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- IP Types Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">IP Types Distribution</h3>
                <button onclick="refreshChart('types')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="typesChart"></canvas>
            </div>
        </div>

        <!-- IP Statuses Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">IP Statuses</h3>
                <button onclick="refreshChart('statuses')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="statusesChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">Monthly IP Trend (12 Months)</h3>
                <button onclick="refreshChart('monthly')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- IP by Faculty Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-text-main">IPs by Faculty</h3>
                <button onclick="refreshChart('faculties')" class="text-sm font-semibold text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </button>
            </div>
            <div class="h-64">
                <canvas id="facultiesChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartApiUrl = "{{ route('api.ip.chart-data') }}";
    let typesChart, statusesChart, monthlyChart, facultiesChart;

    // Initialize all charts
    async function initCharts() {
        await Promise.all([
            loadTypesChart(),
            loadStatusesChart(),
            loadMonthlyChart(),
            loadFacultiesChart()
        ]);
    }

    // Load Types Chart (Doughnut)
    async function loadTypesChart() {
        const response = await fetch(`${chartApiUrl}?type=types`);
        const data = await response.json();

        const ctx = document.getElementById('typesChart').getContext('2d');

        if (typesChart) typesChart.destroy();

        const colors = {
            'PATENT': '#184290',
            'UTILITY_INNOVATION': '#3B82F6',
            'COPYRIGHT': '#8B5CF6',
            'TRADEMARK': '#10B981',
            'INDUSTRIAL_DESIGN': '#F59E0B'
        };

        typesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.type),
                datasets: [{
                    data: data.map(d => d.count),
                    backgroundColor: data.map(d => colors[d.type] || '#6B7280'),
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

    // Load Statuses Chart (Pie)
    async function loadStatusesChart() {
        const response = await fetch(`${chartApiUrl}?type=statuses`);
        const data = await response.json();

        const ctx = document.getElementById('statusesChart').getContext('2d');

        if (statusesChart) statusesChart.destroy();

        const colors = {
            'GRANTED': '#10B981',
            'FILED': '#F59E0B',
            'REGISTERED': '#3B82F6',
            'PENDING': '#F97316',
            'REJECTED': '#EF4444',
            'EXPIRED': '#6B7280'
        };

        statusesChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.map(d => d.status),
                datasets: [{
                    data: data.map(d => d.count),
                    backgroundColor: data.map(d => colors[d.status] || '#6B7280'),
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
                    label: 'IPs',
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

    // Load Faculties Chart (Horizontal Bar)
    async function loadFacultiesChart() {
        const response = await fetch(`${chartApiUrl}?type=faculties`);
        const data = await response.json();

        const ctx = document.getElementById('facultiesChart').getContext('2d');

        if (facultiesChart) facultiesChart.destroy();

        facultiesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(f => f.faculty_code),
                datasets: [{
                    label: 'IPs',
                    data: data.map(f => f.count),
                    backgroundColor: ['#184290', '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'],
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }

    // Refresh specific chart
    async function refreshChart(type) {
        switch(type) {
            case 'types': await loadTypesChart(); break;
            case 'statuses': await loadStatusesChart(); break;
            case 'monthly': await loadMonthlyChart(); break;
            case 'faculties': await loadFacultiesChart(); break;
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
