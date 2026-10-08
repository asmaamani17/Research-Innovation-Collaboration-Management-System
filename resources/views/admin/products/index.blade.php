@extends('layouts.admin')

@section('title', 'TRL Dashboard')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">TRL Product Dashboard</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Overview of product readiness, development status, and faculty activity.</p>
        </div>
    </header>

    <div class="space-y-6 px-4 pb-8 sm:px-8">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 items-end gap-4 rounded-xl border border-divider-subtle/30 bg-white p-4 shadow-sm sm:grid-cols-2 xl:grid-cols-5 sm:p-5">
            <div>
                <label for="dashboard-trl-filter" class="mb-2 block text-sm font-medium text-text-main">TRL</label>
                <select id="dashboard-trl-filter" name="trl" class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All</option>
                    @foreach(range(1, 9) as $level)
                        <option value="{{ $level }}" @selected((string) request('trl') === (string) $level)>TRL {{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="dashboard-status-filter" class="mb-2 block text-sm font-medium text-text-main">Status</label>
                <select id="dashboard-status-filter" name="status" class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All</option>
                    <option value="__not_set__" @selected(request('status') === '__not_set__')>Not Set</option>
                    @foreach($allStatuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="dashboard-faculty-filter" class="mb-2 block text-sm font-medium text-text-main">Faculty</label>
                <select id="dashboard-faculty-filter" name="faculty" class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All</option>
                    <option value="__not_set__" @selected(request('faculty') === '__not_set__')>Not Set</option>
                    @foreach($allFaculties as $faculty)
                        <option value="{{ $faculty }}" @selected(request('faculty') === $faculty)>{{ $faculty }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="dashboard-year-filter" class="mb-2 block text-sm font-medium text-text-main">Year</label>
                <select id="dashboard-year-filter" name="year" class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All</option>
                    @foreach($allYears as $year)
                        <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-11 flex-1 rounded-lg bg-primary px-4 text-sm font-bold text-white transition hover:bg-primary/90">Apply</button>
                <a href="{{ route('admin.products.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-divider-subtle/40 bg-white px-4 text-sm font-bold text-text-main transition hover:bg-background-light">Reset</a>
            </div>
        </form>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="TRL product summary">
            @foreach([
                ['label' => 'Total Products', 'value' => $totalProducts, 'icon' => 'inventory_2', 'tone' => 'bg-primary/10 text-primary', 'note' => 'Products matching the selected filters'],
                ['label' => 'In-Development', 'value' => $inDevelopment, 'icon' => 'engineering', 'tone' => 'bg-blue-100 text-blue-600', 'note' => 'Products being developed'],
                ['label' => 'Stagnant', 'value' => $stagnant, 'icon' => 'hourglass_disabled', 'tone' => 'bg-amber-100 text-amber-700', 'note' => 'Products requiring attention'],
                ['label' => 'Commercial', 'value' => $commercial, 'icon' => 'storefront', 'tone' => 'bg-green-100 text-green-600', 'note' => 'Commercialized products'],
            ] as $metric)
                <article class="flex items-center justify-between gap-4 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm transition-colors hover:border-primary sm:p-6">
                    <div>
                        <p class="text-sm font-medium text-divider-subtle">{{ $metric['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold text-text-main">{{ number_format($metric['value']) }}</p>
                        <p class="mt-1 text-xs text-divider-subtle">{{ $metric['note'] }}</p>
                    </div>
                    <div class="shrink-0 rounded-lg p-3 {{ $metric['tone'] }}">
                        <span class="material-symbols-outlined text-2xl" aria-hidden="true">{{ $metric['icon'] }}</span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid grid-cols-1 gap-4 xl:grid-cols-2" aria-label="TRL product charts">
            <article class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6 xl:col-span-2">
                <h2 class="mb-1 text-lg font-bold text-text-main">Products by TRL</h2>
                <p class="mb-4 text-sm text-divider-subtle">Products at each technology readiness level.</p>
                <div class="h-80"><canvas id="productTrlChart" aria-label="Products by technology readiness level"></canvas></div>
            </article>
            <article class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="mb-1 text-lg font-bold text-text-main">Product Status</h2>
                <p class="mb-4 text-sm text-divider-subtle">Current development status distribution.</p>
                <div class="h-64"><canvas id="productStatusChart" aria-label="Products by status"></canvas></div>
            </article>
            <article class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="mb-1 text-lg font-bold text-text-main">Top 10 Faculties</h2>
                <p class="mb-4 text-sm text-divider-subtle">Faculties with the most products.</p>
                <div class="h-64"><canvas id="productFacultyChart" aria-label="Top faculties by product count"></canvas></div>
            </article>
        </section>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            const trlProductsChartColors = ['#184290', '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#06B6D4', '#F97316', '#84CC16', '#A855F7'];
            const chartOptions = { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } };
            const trlData = @json($trlChart);
            const statusData = @json($statusChart);
            const facultyData = @json($facultyChart);

            new Chart(document.getElementById('productTrlChart'), {
                type: 'bar',
                data: {
                    labels: trlData.map(item => `TRL ${item.label}`),
                    datasets: [{ label: 'Products', data: trlData.map(item => item.total), backgroundColor: '#184290', borderRadius: 6 }]
                },
                options: { ...chartOptions, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });

            new Chart(document.getElementById('productStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: statusData.map(item => item.label),
                    datasets: [{ data: statusData.map(item => item.total), backgroundColor: trlProductsChartColors, borderWidth: 2, borderColor: '#fff' }]
                },
                options: chartOptions
            });

            new Chart(document.getElementById('productFacultyChart'), {
                type: 'bar',
                data: {
                    labels: facultyData.map(item => item.label),
                    datasets: [{ label: 'Products', data: facultyData.map(item => item.total), backgroundColor: '#10B981', borderRadius: 6 }]
                },
                options: { ...chartOptions, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
            });
        </script>
    @endpush
@endsection
