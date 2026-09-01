@extends('layouts.admin')

@section('title', 'Reports & Analytics')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                Reports & Analytics
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Generate comprehensive reports and analyze award statistics.
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('reports.export.excel', ['type' => 'template', 'template_type' => 'Template_RD8_1_New', 'year' => 2025]) }}"
                class="flex min-w-[140px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-blue-600 text-white text-sm font-bold shadow-lg hover:bg-blue-700 transition-all">
                <span class="material-symbols-outlined text-sm">download</span>
                <span>Download Template_RD8_1_New</span>
            </a>
            <a href="{{ route('reports.export.excel', ['type' => 'template', 'template_type' => 'Template_RD8_New', 'year' => 2025]) }}"
                class="flex min-w-[140px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-green-600 text-white text-sm font-bold shadow-lg hover:bg-green-700 transition-all">
                <span class="material-symbols-outlined text-sm">download</span>
                <span>Download Template_RD8_New</span>
            </a>
        </div>
    </header>

    <!-- Report Filters -->
    <div class="px-8 mb-6">
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6">
            <h3 class="text-lg font-bold text-text-main mb-4">Report Filters</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-main mb-2">Year</label>
                    <select id="filterYear"
                        class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                        <option value="">All Years</option>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-main mb-2">Faculty</label>
                    <select id="filterFaculty"
                        class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                        <option value="">All Faculties</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-main mb-2">Award Level</label>
                    <select id="filterLevel"
                        class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                        <option value="">All Levels</option>
                        <option value="International">International</option>
                        <option value="National">National</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-main mb-2">Report Type</label>
                    <select id="filterType"
                        class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                        <option value="template">Template Report (UTeM Format)</option>
                        <option value="summary">Summary Report</option>
                        <option value="faculty_performance">Faculty Performance</option>
                        <option value="award_statistics">Award Statistics</option>
                        <option value="staff_performance">Staff Performance</option>
                        <option value="event_participation">Event Participation</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <button
                    class="px-6 py-2 border border-divider-subtle text-text-main text-sm font-medium rounded-lg hover:bg-background-light transition-colors">
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Report Navigation -->
    <div class="px-8 mb-6">
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6">
            <h3 class="text-lg font-bold text-text-main mb-4">Detailed Reports</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('reports.faculty-performance') }}"
                    class="group bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-3xl text-blue-600">school</span>
                        <span class="text-xs font-medium text-blue-600 bg-blue-200 px-2 py-1 rounded-full">Analytics</span>
                    </div>
                    <h4 class="font-bold text-text-main mb-2 group-hover:text-blue-700 transition-colors">Faculty
                        Performance</h4>
                    <p class="text-sm text-divider-subtle">Detailed faculty analytics with participation rates and award
                        distributions</p>
                </a>
                <a href="{{ route('reports.award-statistics') }}"
                    class="group bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-3xl text-green-600">bar_chart</span>
                        <span
                            class="text-xs font-medium text-green-600 bg-green-200 px-2 py-1 rounded-full">Statistics</span>
                    </div>
                    <h4 class="font-bold text-text-main mb-2 group-hover:text-green-700 transition-colors">Award Statistics
                    </h4>
                    <p class="text-sm text-divider-subtle">Comprehensive award distribution and trend analysis</p>
                </a>
                <a href="{{ route('reports.staff-performance') }}"
                    class="group bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-3xl text-yellow-600">person</span>
                        <span
                            class="text-xs font-medium text-yellow-600 bg-yellow-200 px-2 py-1 rounded-full">Performance</span>
                    </div>
                    <h4 class="font-bold text-text-main mb-2 group-hover:text-yellow-700 transition-colors">Staff
                        Performance</h4>
                    <p class="text-sm text-divider-subtle">Individual staff metrics, career spans, and achievements</p>
                </a>
                <a href="{{ route('reports.event-participation') }}"
                    class="group bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-3xl text-purple-600">event</span>
                        <span class="text-xs font-medium text-purple-600 bg-purple-200 px-2 py-1 rounded-full">Events</span>
                    </div>
                    <h4 class="font-bold text-text-main mb-2 group-hover:text-purple-700 transition-colors">Event
                        Participation</h4>
                    <p class="text-sm text-divider-subtle">Event efficiency analysis and participation metrics</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="px-8 pb-8 flex-1">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-divider-subtle">Total Awards</span>
                    <span class="material-symbols-outlined text-primary">military_tech</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['total_awards'] ?? 0 }}</p>
                <p class="text-xs text-green-600 mt-2">
                    @if(isset($summary['last_year_awards']) && $summary['last_year_awards'] > 0)
                        +{{ round((($summary['this_year_awards'] - $summary['last_year_awards']) / $summary['last_year_awards']) * 100, 1) }}%
                        from last year
                    @else
                        New this year
                    @endif
                </p>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-divider-subtle">Total Projects</span>
                    <span class="material-symbols-outlined text-blue-600">folder</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['total_projects'] ?? 0 }}</p>
                <p class="text-xs text-divider-subtle mt-2">Active research projects</p>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-divider-subtle">Total Staff</span>
                    <span class="material-symbols-outlined text-green-600">groups</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['total_staff'] ?? 0 }}</p>
                <p class="text-xs text-divider-subtle mt-2">Registered staff members</p>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-divider-subtle">Total Events</span>
                    <span class="material-symbols-outlined text-purple-600">event</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['total_events'] ?? 0 }}</p>
                <p class="text-xs text-divider-subtle mt-2">Competitions & exhibitions</p>
            </div>
        </div>
    </div>

@endsection