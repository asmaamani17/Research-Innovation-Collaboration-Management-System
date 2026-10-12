@extends('layouts.admin')

@section('title', 'IP Dashboard')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Intellectual Property Dashboard</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Filing trends, portfolio status, and research activity.</p>
        </div>
        <div class="flex flex-wrap gap-2">
        </div>
    </header>

    <div class="space-y-6 px-4 pb-8 sm:px-8">
        <!-- Filter Bar -->
        <div class="rounded-xl border border-divider-subtle/30 bg-white p-4 shadow-sm sm:p-6 mb-6">
            <form method="GET" action="{{ route('admin.ip.dashboard') }}">
                <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-[1fr_1fr_1fr_1fr_auto]">
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Category</label>
                        <select name="category" onchange="this.form.submit()" class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ str_replace('_', ' ', $category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Year</label>
                        <select name="year" onchange="this.form.submit()" class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="">All Years</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Faculty</label>
                        <select name="faculty" onchange="this.form.submit()" class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="">All Faculties</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty['value'] }}" @selected(request('faculty') === $faculty['value'])>{{ $faculty['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">IP Type</label>
                        <select name="type" onchange="this.form.submit()" class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="">All IP Types</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_', ' ', $type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Status</label>
                        <select name="status" onchange="this.form.submit()" class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- <div>
                        <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Search</label>
                        <input name="search" value="{{ request('search') }}" placeholder="Product, researcher, filing no."
                            class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main placeholder:text-divider-subtle outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div> -->
                    <div>
                        <a href="{{ route('admin.ip.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-divider-subtle/50 bg-white px-5 py-3 text-sm font-bold text-text-main transition hover:bg-background-light">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5" aria-label="IP summary">
            @foreach([
                ['label' => 'Total IP Records', 'value' => $stats['total_ips'], 'icon' => 'inventory_2', 'tone' => 'bg-primary/10 text-primary'],
                ['label' => 'RICM Staff', 'value' => $stats['total_staff'], 'icon' => 'groups', 'tone' => 'bg-blue-100 text-blue-700'],
                ['label' => 'RICM Projects', 'value' => $stats['total_projects'], 'icon' => 'biotech', 'tone' => 'bg-purple-100 text-purple-700'],
                ['label' => 'Filed This Year', 'value' => $stats['this_year_ips'], 'icon' => 'event', 'tone' => 'bg-orange-100 text-orange-700'],
                ['label' => 'Total Filing Cost', 'value' => 'RM '.number_format($stats['total_cost'], 2), 'icon' => 'payments', 'tone' => 'bg-green-100 text-green-700'],
            ] as $metric)
                <article class="flex min-w-0 items-center justify-between gap-3 rounded-xl border border-divider-subtle/30 bg-white p-4 shadow-sm transition-colors hover:border-primary sm:p-5">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-divider-subtle">{{ $metric['label'] }}</p>
                        <p class="mt-2 truncate text-2xl font-bold text-text-main">{{ is_numeric($metric['value']) ? number_format($metric['value']) : $metric['value'] }}</p>
                    </div>
                    <div class="shrink-0 rounded-lg p-3 {{ $metric['tone'] }}">
                        <span class="material-symbols-outlined text-2xl" aria-hidden="true">{{ $metric['icon'] }}</span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid grid-cols-1 gap-4 xl:grid-cols-2" aria-label="IP analytics">
            <article class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6 xl:col-span-2">
                <h2 class="mb-1 text-lg font-bold text-text-main">Filing Trend by Year</h2>
                <p class="mb-4 text-sm text-divider-subtle">Annual filings across the portfolio. The year filter does not hide other years in this trend.</p>
                <div class="h-72"><canvas id="ipYearChart" aria-label="IP filings by year"></canvas></div>
            </article>
            <article class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="mb-1 text-lg font-bold text-text-main">Distribution by Category</h2>
                <p class="mb-4 text-sm text-divider-subtle">Records by IP portfolio category.</p>
                <div class="h-72"><canvas id="ipCategoryChart" aria-label="IP records by category"></canvas></div>
            </article>
            <article class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="mb-1 text-lg font-bold text-text-main">Portfolio Status</h2>
                <p class="mb-4 text-sm text-divider-subtle">Current filing and registration status.</p>
                <div class="h-72"><canvas id="ipStatusChart" aria-label="IP records by status"></canvas></div>
            </article>
            <article class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6 xl:col-span-2">
                <h2 class="mb-1 text-lg font-bold text-text-main">Records by Faculty</h2>
                <p class="mb-4 text-sm text-divider-subtle">Top faculties represented in the selected portfolio.</p>
                <div class="h-72"><canvas id="ipFacultyChart" aria-label="IP records by faculty"></canvas></div>
            </article>
        </section>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            const ipChartColors = ['#184290', '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#06B6D4', '#F97316', '#84CC16'];
            const ipChartOptions = { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } };
            const ipYears = @json($yearChart);
            const ipCategories = @json($categoryChart);
            const ipStatuses = @json($statusChart);
            const ipFaculties = @json($facultyChart);

            new Chart(document.getElementById('ipYearChart'), {
                type: 'line',
                data: {
                    labels: ipYears.map(item => item.label),
                    datasets: [{
                        label: 'IP Filings',
                        data: ipYears.map(item => item.total),
                        borderColor: '#184290',
                        backgroundColor: 'rgba(24, 66, 144, 0.12)',
                        pointBackgroundColor: '#184290',
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: { ...ipChartOptions, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });

            new Chart(document.getElementById('ipCategoryChart'), {
                type: 'doughnut',
                data: {
                    labels: ipCategories.map(item => String(item.label).replaceAll('_', ' ')),
                    datasets: [{ data: ipCategories.map(item => item.total), backgroundColor: ipChartColors, borderColor: '#fff', borderWidth: 2 }]
                },
                options: ipChartOptions
            });

            new Chart(document.getElementById('ipStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: ipStatuses.map(item => item.label),
                    datasets: [{ data: ipStatuses.map(item => item.total), backgroundColor: ipChartColors, borderColor: '#fff', borderWidth: 2 }]
                },
                options: ipChartOptions
            });

            new Chart(document.getElementById('ipFacultyChart'), {
                type: 'bar',
                data: {
                    labels: ipFaculties.map(item => item.faculty_code),
                    datasets: [{ 
                        label: 'IP Records', 
                        data: ipFaculties.map(item => item.count), 
                        backgroundColor: ipFaculties.map((_, index) => ipChartColors[index % ipChartColors.length]),
                        borderRadius: 6 
                    }]
                },
                options: { ...ipChartOptions, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
            });
        </script>
    @endpush
@endsection
