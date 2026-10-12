@extends('layouts.admin')

@section('title', 'Research, Innovation & Collaboration Dashboard')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            Research, Innovation and Collaboration Management Dashboard
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Comprehensive analytics overview covering Awards, KPI Performance, Intellectual Property (IP), Community & STEM Industry, and TRL Products.
        </p>
    </div>
</header>

<div class="px-8 pb-8">
    <!-- Top Filter Bar -->
    <form method="GET" action="{{ route('superadmin.dashboard') }}" class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-8 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            @if($selectedYear || $selectedFaculty || $selectedModule)
                <span class="text-xs font-semibold text-primary bg-primary/10 px-3 py-1 rounded-full flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">filter_alt</span> Filters Applied
                </span>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Year Filter -->
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Year</label>
                <select name="year" id="filterYear"
                    class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                    <option value="">All Years</option>
                    @foreach($availableYears as $yearOpt)
                        <option value="{{ $yearOpt }}" {{ (string)$selectedYear === (string)$yearOpt ? 'selected' : '' }}>
                            {{ $yearOpt }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Faculty Filter -->
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Faculty</label>
                <select name="faculty" id="filterFaculty"
                    class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                    <option value="">All Faculties</option>
                    @foreach($faculties as $fac)
                        <option value="{{ $fac->id }}" {{ (string)$selectedFaculty === (string)$fac->id ? 'selected' : '' }}>
                            {{ $fac->faculty_name }} ({{ $fac->faculty_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Module Filter -->
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Module Focus</label>
                <select name="module" id="filterModule"
                    class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                    <option value="">All Modules</option>
                    <option value="awards" {{ $selectedModule === 'awards' ? 'selected' : '' }}>Awards</option>
                    <option value="kpi" {{ $selectedModule === 'kpi' ? 'selected' : '' }}>KPI Performance</option>
                    <option value="ip" {{ $selectedModule === 'ip' ? 'selected' : '' }}>Intellectual Property (IP)</option>
                    <option value="community" {{ $selectedModule === 'community' ? 'selected' : '' }}>Community, STEM & Industry</option>
                    <option value="products" {{ $selectedModule === 'products' ? 'selected' : '' }}>TRL Products</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-3">
            <button type="submit"
                class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-sm">filter_alt</span> Apply Filters
            </button>
            <a href="{{ route('superadmin.dashboard') }}"
                class="px-6 py-2.5 border border-divider-subtle text-text-main text-sm font-medium rounded-lg hover:bg-background-light transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">restart_alt</span> Reset Filters
            </a>
        </div>
    </form>

    <!-- Summary Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- 1. Awards Summary Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-divider-subtle uppercase tracking-wider">Awards</p>
                <p class="text-3xl font-extrabold text-text-main">{{ number_format($totalAwards) }}</p>
                <p class="text-xs text-primary font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">workspace_premium</span>
                    Exhibitions & Competitions
                </p>
            </div>
            <div class="bg-amber-50 p-3.5 rounded-xl text-amber-600 border border-amber-200/50">
                <span class="material-symbols-outlined text-3xl">emoji_events</span>
            </div>
        </div>

        <!-- 2. KPI Achievement Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-divider-subtle uppercase tracking-wider">Avg KPI Achievement</p>
                <p class="text-3xl font-extrabold text-text-main">{{ $avgKpiAchievement }}%</p>
                <p class="text-xs text-purple-600 font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">auto_graph</span>
                    {{ $totalKpiStrategies }} Active Strategies
                </p>
            </div>
            <div class="bg-purple-50 p-3.5 rounded-xl text-purple-600 border border-purple-200/50">
                <span class="material-symbols-outlined text-3xl">trending_up</span>
            </div>
        </div>

        <!-- 3. Intellectual Property (IP) Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-divider-subtle uppercase tracking-wider">Intellectual Property (IP)</p>
                <p class="text-3xl font-extrabold text-text-main">{{ number_format($totalIp) }}</p>
                <p class="text-xs text-blue-600 font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">verified</span>
                    Patents, Copyrights & IPR
                </p>
            </div>
            <div class="bg-blue-50 p-3.5 rounded-xl text-blue-600 border border-blue-200/50">
                <span class="material-symbols-outlined text-3xl">gavel</span>
            </div>
        </div>

        <!-- 4. Community, STEM & Industry Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-divider-subtle uppercase tracking-wider">Community & STEM Industry</p>
                <p class="text-3xl font-extrabold text-text-main">{{ number_format($totalStemPrograms) }}</p>
                <p class="text-xs text-teal-600 font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">handshake</span>
                    Community & Industry Outreach
                </p>
            </div>
            <div class="bg-teal-50 p-3.5 rounded-xl text-teal-600 border border-teal-200/50">
                <span class="material-symbols-outlined text-3xl">groups_3</span>
            </div>
        </div>

        <!-- 5. TRL Products Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-divider-subtle uppercase tracking-wider">TRL Products</p>
                <p class="text-3xl font-extrabold text-text-main">{{ number_format($totalProducts) }}</p>
                <p class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">precision_manufacturing</span>
                    Commercial & Readiness Levels
                </p>
            </div>
            <div class="bg-emerald-50 p-3.5 rounded-xl text-emerald-600 border border-emerald-200/50">
                <span class="material-symbols-outlined text-3xl">inventory_2</span>
            </div>
        </div>

        <!-- 6. Projects & Staff Card -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-divider-subtle uppercase tracking-wider">Research Projects & Staff</p>
                <p class="text-3xl font-extrabold text-text-main">{{ number_format($totalProjects) }} <span class="text-sm font-normal text-divider-subtle">Projects</span></p>
                <p class="text-xs text-indigo-600 font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">badge</span>
                    {{ number_format($totalStaff) }} Registered Researchers
                </p>
            </div>
            <div class="bg-indigo-50 p-3.5 rounded-xl text-indigo-600 border border-indigo-200/50">
                <span class="material-symbols-outlined text-3xl">biotech</span>
            </div>
        </div>
    </div>

    <!-- Charts Grid Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- 1. Awards by Level Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">military_tech</span>
                    <h3 class="text-base font-bold text-text-main">Awards Level Distribution</h3>
                </div>
                <span class="text-xs text-divider-subtle font-medium">Filtered</span>
            </div>
            <div class="h-64">
                <canvas id="awardsByLevelChart"></canvas>
            </div>
        </div>

        <!-- 2. IP Records by Type Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">copyright</span>
                    <h3 class="text-base font-bold text-text-main">Intellectual Property (IP) by Type</h3>
                </div>
                <span class="text-xs text-divider-subtle font-medium">Filtered</span>
            </div>
            <div class="h-64">
                <canvas id="ipByTypeChart"></canvas>
            </div>
        </div>

        <!-- 3. Community & STEM Industry Classifications -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-600">diversity_3</span>
                    <h3 class="text-base font-bold text-text-main">Community, STEM & Industry Outreach</h3>
                </div>
                <span class="text-xs text-divider-subtle font-medium">Filtered</span>
            </div>
            <div class="h-64">
                <canvas id="stemByClassificationChart"></canvas>
            </div>
        </div>

        <!-- 4. TRL Products Status Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">published_with_changes</span>
                    <h3 class="text-base font-bold text-text-main">TRL Products Status</h3>
                </div>
                <span class="text-xs text-divider-subtle font-medium">Filtered</span>
            </div>
            <div class="h-64">
                <canvas id="productsByStatusChart"></canvas>
            </div>
        </div>

        <!-- 5. KPI Achievement Trend Chart -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-600">monitoring</span>
                    <h3 class="text-base font-bold text-text-main">KPI Average Achievement Trend % by Target Year</h3>
                </div>
                <span class="text-xs text-divider-subtle font-medium">Annual Performance</span>
            </div>
            <div class="h-64">
                <canvas id="kpiByYearChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity Panels (4-Grid Overview) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Panel 1: Recent Awards -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">emoji_events</span> Recent Awards
                </h3>
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-primary hover:underline">View All &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($recentAwards as $award)
                <div class="p-3 bg-background-light rounded-lg border border-divider-subtle/20 flex items-center justify-between">
                    <div class="truncate max-w-xs">
                        <p class="font-bold text-text-main text-sm truncate">{{ $award->award_name }}</p>
                        <p class="text-xs text-divider-subtle">{{ $award->staff->staff_name ?? 'Researcher' }} • {{ $award->award_level }}</p>
                    </div>
                    <span class="text-xs bg-white px-2 py-1 rounded border border-divider-subtle/30 text-divider-subtle">
                        {{ $award->award_date ? $award->award_date->format('Y-m-d') : 'N/A' }}
                    </span>
                </div>
                @empty
                <p class="text-divider-subtle text-center text-xs py-4">No recent awards matching filters</p>
                @endforelse
            </div>
        </div>

        <!-- Panel 2: Recent IP Filings -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">gavel</span> Recent Intellectual Property
                </h3>
                <a href="{{ route('admin.ip.dashboard') }}" class="text-xs font-semibold text-primary hover:underline">View All &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($recentIp as $ip)
                <div class="p-3 bg-background-light rounded-lg border border-divider-subtle/20 flex items-center justify-between">
                    <div class="truncate max-w-xs">
                        <p class="font-bold text-text-main text-sm truncate">{{ $ip->nama_produk ?? $ip->title ?? 'IP Record' }}</p>
                        <p class="text-xs text-divider-subtle">{{ $ip->jenis_ipr ?? $ip->type ?? 'IPR' }} • {{ $ip->fakulti ?? 'Faculty' }}</p>
                    </div>
                    <span class="text-xs bg-white px-2 py-1 rounded border border-divider-subtle/30 text-divider-subtle">
                        {{ $ip->tarikh_difailkan ? \Carbon\Carbon::parse($ip->tarikh_difailkan)->format('Y-m-d') : ($ip->tahun ?? 'N/A') }}
                    </span>
                </div>
                @empty
                <p class="text-divider-subtle text-center text-xs py-4">No recent IP records matching filters</p>
                @endforelse
            </div>
        </div>

        <!-- Panel 3: Recent Community & STEM Programs -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-600">groups</span> Recent STEM & Community Programs
                </h3>
                <a href="{{ route('admin.stem.dashboard') }}" class="text-xs font-semibold text-primary hover:underline">View All &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($recentStemPrograms as $prog)
                <div class="p-3 bg-background-light rounded-lg border border-divider-subtle/20 flex items-center justify-between">
                    <div class="truncate max-w-xs">
                        <p class="font-bold text-text-main text-sm truncate">{{ $prog->program }}</p>
                        <p class="text-xs text-divider-subtle">{{ $prog->classification }} • {{ $prog->faculty ?? 'UTeM' }}</p>
                    </div>
                    <span class="text-xs bg-white px-2 py-1 rounded border border-divider-subtle/30 text-divider-subtle">
                        {{ $prog->start_date ? \Carbon\Carbon::parse($prog->start_date)->format('Y-m-d') : 'N/A' }}
                    </span>
                </div>
                @empty
                <p class="text-divider-subtle text-center text-xs py-4">No recent programs matching filters</p>
                @endforelse
            </div>
        </div>

        <!-- Panel 4: Recent TRL Products -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">inventory_2</span> Recent TRL Products
                </h3>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-primary hover:underline">View All &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($recentTrlProducts as $prod)
                <div class="p-3 bg-background-light rounded-lg border border-divider-subtle/20 flex items-center justify-between">
                    <div class="truncate max-w-xs">
                        <p class="font-bold text-text-main text-sm truncate">{{ $prod->product }}</p>
                        <p class="text-xs text-divider-subtle">TRL {{ $prod->trl_level }} • {{ $prod->status ?? 'Active' }}</p>
                    </div>
                    <span class="text-xs bg-white px-2 py-1 rounded border border-divider-subtle/30 text-divider-subtle">
                        {{ $prod->product_year ?? 'N/A' }}
                    </span>
                </div>
                @empty
                <p class="text-divider-subtle text-center text-xs py-4">No recent TRL products matching filters</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartApiUrl = "{{ route('superadmin.dashboard.data') }}?year={{ $selectedYear }}&faculty={{ $selectedFaculty }}";
    
    let awardsChart, ipChart, stemChart, trlChart, kpiChart;

    async function initCharts() {
        try {
            const res = await fetch(chartApiUrl);
            const data = await res.json();

            // 1. Awards Level Chart
            const ctxAwards = document.getElementById('awardsByLevelChart').getContext('2d');
            if (awardsChart) awardsChart.destroy();

            const levelColorMap = {
                'Gold': '#F59E0B',
                'Silver': '#94A3B8',
                'Bronze': '#D97706',
                'Platinum': '#06B6D4',
                'Special': '#8B5CF6',
                'Others': '#64748B',
                'Other': '#64748B'
            };
            const defaultPalette = ['#F59E0B', '#94A3B8', '#D97706', '#06B6D4', '#8B5CF6', '#64748B', '#EF4444', '#3B82F6'];

            const awardLabels = data.awardsByLevel.map(d => d.level);
            const awardCounts = data.awardsByLevel.map(d => d.count);
            const awardColors = awardLabels.map((lbl, idx) => levelColorMap[lbl] || defaultPalette[idx % defaultPalette.length]);

            awardsChart = new Chart(ctxAwards, {
                type: 'doughnut',
                data: {
                    labels: awardLabels,
                    datasets: [{
                        data: awardCounts,
                        backgroundColor: awardColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } }
                        }
                    }
                }
            });

            // 2. IP by Type Chart
            const ctxIp = document.getElementById('ipByTypeChart').getContext('2d');
            if (ipChart) ipChart.destroy();
            ipChart = new Chart(ctxIp, {
                type: 'pie',
                data: {
                    labels: data.ipByType.map(d => d.type_name),
                    datasets: [{
                        data: data.ipByType.map(d => d.count),
                        backgroundColor: ['#2563EB', '#7C3AED', '#059669', '#D97706', '#DC2626', '#475569'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 18, font: { size: 11 } }
                        }
                    }
                }
            });

            // 3. STEM Classification Chart
            const ctxStem = document.getElementById('stemByClassificationChart').getContext('2d');
            if (stemChart) stemChart.destroy();
            stemChart = new Chart(ctxStem, {
                type: 'bar',
                data: {
                    labels: data.stemByClassification.map(d => d.classification),
                    datasets: [{
                        label: 'Programs',
                        data: data.stemByClassification.map(d => d.count),
                        backgroundColor: '#0D9488',
                        borderRadius: 6
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            // 4. Products Status Chart
            const ctxTrl = document.getElementById('productsByStatusChart').getContext('2d');
            if (trlChart) trlChart.destroy();
            trlChart = new Chart(ctxTrl, {
                type: 'doughnut',
                data: {
                    labels: data.productsByStatus.map(d => d.status),
                    datasets: [{
                        data: data.productsByStatus.map(d => d.count),
                        backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#94A3B8'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } }
                        }
                    }
                }
            });

            // 5. KPI Achievement Chart
            const ctxKpi = document.getElementById('kpiByYearChart').getContext('2d');
            if (kpiChart) kpiChart.destroy();
            kpiChart = new Chart(ctxKpi, {
                type: 'line',
                data: {
                    labels: data.kpiByYear.map(d => d.year),
                    datasets: [{
                        label: 'Average Achievement %',
                        data: data.kpiByYear.map(d => d.achievement),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 100 } }
                }
            });

        } catch (e) {
            console.error('Error loading dashboard charts:', e);
        }
    }

    document.addEventListener('DOMContentLoaded', initCharts);
</script>
@endpush
