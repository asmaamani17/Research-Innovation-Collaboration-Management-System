@extends('layouts.admin')

@section('title', 'IP Reports & Analytics')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                IP Reports & Analytics
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Generate comprehensive reports and analyze intellectual property statistics.
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.ip.export') }}"
                class="flex min-w-[140px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-purple-600 text-white text-sm font-bold shadow-lg hover:bg-purple-700 transition-all">
                <span class="material-symbols-outlined text-sm">download</span>
                <span>Export IP Data</span>
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
                    <label class="block text-sm font-medium text-text-main mb-2">IP Type</label>
                    <select id="filterType"
                        class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                        <option value="">All Types</option>
                        <option value="PATENT">Patent</option>
                        <option value="UTILITY_INNOVATION">Utility Innovation</option>
                        <option value="COPYRIGHT">Copyright</option>
                        <option value="TRADEMARK">Trademark</option>
                        <option value="INDUSTRIAL_DESIGN">Industrial Design</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-main mb-2">IP Status</label>
                    <select id="filterStatus"
                        class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                        <option value="">All Statuses</option>
                        <option value="GRANTED">Granted</option>
                        <option value="FILED">Filed</option>
                        <option value="REGISTERED">Registered</option>
                        <option value="PENDING">Pending</option>
                        <option value="REJECTED">Rejected</option>
                        <option value="EXPIRED">Expired</option>
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
                <a href="{{ route('admin.ip.dashboard') }}"
                    class="group bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-3xl text-purple-600">dashboard</span>
                        <span class="text-xs font-medium text-purple-600 bg-purple-200 px-2 py-1 rounded-full">Analytics</span>
                    </div>
                    <h4 class="font-bold text-text-main mb-2 group-hover:text-purple-700 transition-colors">IP Dashboard</h4>
                    <p class="text-sm text-divider-subtle">Overview of IP statistics and trends</p>
                </a>
                <a href="{{ route('admin.ip.export') }}"
                    class="group bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-3xl text-green-600">download</span>
                        <span class="text-xs font-medium text-green-600 bg-green-200 px-2 py-1 rounded-full">Export</span>
                    </div>
                    <h4 class="font-bold text-text-main mb-2 group-hover:text-green-700 transition-colors">Export IP Data</h4>
                    <p class="text-sm text-divider-subtle">Download IP data as CSV with filters</p>
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
                    <span class="text-sm font-medium text-divider-subtle">Total IPs</span>
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['total_ips'] ?? 0 }}</p>
                <p class="text-xs text-divider-subtle mt-2">All intellectual properties</p>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-divider-subtle">Granted IPs</span>
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['granted_ips'] ?? 0 }}</p>
                <p class="text-xs text-divider-subtle mt-2">Successfully granted</p>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-divider-subtle">Pending IPs</span>
                    <span class="material-symbols-outlined text-yellow-600">pending</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['pending_ips'] ?? 0 }}</p>
                <p class="text-xs text-divider-subtle mt-2">Awaiting approval</p>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-divider-subtle">This Year</span>
                    <span class="material-symbols-outlined text-purple-600">calendar_today</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['this_year_ips'] ?? 0 }}</p>
                <p class="text-xs text-divider-subtle mt-2">IPs filed this year</p>
            </div>
        </div>
    </div>

@endsection
