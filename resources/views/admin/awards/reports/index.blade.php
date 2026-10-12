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
    </header>

    <!-- Report Filters -->
    <div class="px-8 mb-6">
        <form method="GET" action="{{ route('admin.reports') }}" class="bg-white border border-divider-subtle/30 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-text-main">Report Filters</h3>
                @if($selectedYear || $selectedFaculty || $selectedLevel)
                    <span class="text-xs font-semibold text-primary bg-primary/10 px-3 py-1 rounded-full flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">filter_list</span> Filters Active
                    </span>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Faculty</label>
                    <select name="faculty_id" id="filterFaculty"
                        class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                        <option value="">All Faculties</option>
                        @foreach($faculties as $fac)
                            <option value="{{ $fac->id }}" {{ (string)$selectedFaculty === (string)$fac->id ? 'selected' : '' }}>
                                {{ $fac->faculty_name }} ({{ $fac->faculty_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1.5 uppercase tracking-wider">Award Level</label>
                    <select name="level" id="filterLevel"
                        class="w-full h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                        <option value="">All Levels</option>
                        @foreach($availableLevels as $levelOpt)
                            <option value="{{ $levelOpt }}" {{ (string)$selectedLevel === (string)$levelOpt ? 'selected' : '' }}>
                                {{ $levelOpt }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-5 flex items-center gap-3">
                <button type="submit"
                    class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-sm">filter_alt</span> Apply Filters
                </button>
                <a href="{{ route('admin.reports') }}"
                    class="px-6 py-2.5 border border-divider-subtle text-text-main text-sm font-medium rounded-lg hover:bg-background-light transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">restart_alt</span> Reset Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Report Navigation & Download Options -->
    <div class="px-8 mb-6">
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6">
            <h3 class="text-lg font-bold text-text-main mb-4">Download Reports</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Excel Template RD8_1_New Button -->
                <a href="{{ route('reports.export.excel', array_merge(['type' => 'template', 'template_type' => 'Template_RD8_1_New'], request()->all())) }}"
                    class="group bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-3xl text-blue-600">description</span>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-200/80 px-2.5 py-1 rounded-full">Excel Template</span>
                    </div>
                    <h4 class="font-bold text-text-main mb-2 group-hover:text-blue-700 transition-colors">Template_RD8_1_New</h4>
                    <p class="text-sm text-divider-subtle">Download UTeM format Excel template ({{ $selectedYear ?: 'All Years' }})</p>
                </a>

                <!-- Excel Template RD8_New Button -->
                <a href="{{ route('reports.export.excel', array_merge(['type' => 'template', 'template_type' => 'Template_RD8_New'], request()->all())) }}"
                    class="group bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-3xl text-green-600">table_chart</span>
                        <span class="text-xs font-semibold text-green-600 bg-green-200/80 px-2.5 py-1 rounded-full">Excel Template</span>
                    </div>
                    <h4 class="font-bold text-text-main mb-2 group-hover:text-green-700 transition-colors">Template_RD8_New</h4>
                    <p class="text-sm text-divider-subtle">Download standard Excel template ({{ $selectedYear ?: 'All Years' }})</p>
                </a>

                  <!-- PDF Download Button -->
                <a href="{{ route('reports.export.pdf', request()->all()) }}"
                    class="group bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-3xl text-red-600">picture_as_pdf</span>
                        <span class="text-xs font-semibold text-red-600 bg-red-200/80 px-2.5 py-1 rounded-full">PDF Report</span>
                    </div>
                    <h4 class="font-bold text-text-main mb-2 group-hover:text-red-700 transition-colors">Download PDF Report</h4>
                    <p class="text-sm text-divider-subtle">Export complete filtered report summary and awards list as PDF</p>
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
                        Matching current filter
                    @endif
                </p>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-divider-subtle">Total Projects</span>
                    <span class="material-symbols-outlined text-blue-600">folder</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['total_projects'] ?? 0 }}</p>
                <p class="text-xs text-divider-subtle mt-2">Research projects involved</p>
            </div>
            <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-divider-subtle">Total Staff</span>
                    <span class="material-symbols-outlined text-green-600">groups</span>
                </div>
                <p class="text-2xl font-bold text-text-main">{{ $summary['total_staff'] ?? 0 }}</p>
                <p class="text-xs text-divider-subtle mt-2">Staff members involved</p>
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

        <!-- Filtered Award Records Table -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-background-light">
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">ID</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">AWARD LEVEL</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">STAFF</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">FACULTY</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">PROJECT</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">DATE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($awardsList as $index => $award)
                            @php
                                $lvlLower = strtolower($award->award_name ?? $award->award_level ?? '');
                                $badgeStyle = 'bg-blue-100 text-blue-700';
                                if (str_contains($lvlLower, 'gold')) $badgeStyle = 'bg-amber-100 text-amber-700';
                                elseif (str_contains($lvlLower, 'silver')) $badgeStyle = 'bg-slate-200 text-slate-700';
                                elseif (str_contains($lvlLower, 'bronze')) $badgeStyle = 'bg-orange-100 text-orange-700';
                                elseif (str_contains($lvlLower, 'special')) $badgeStyle = 'bg-purple-100 text-purple-700';
                                elseif (str_contains($lvlLower, 'platinum')) $badgeStyle = 'bg-cyan-100 text-cyan-700';
                            @endphp
                            <tr class="hover:bg-background-light/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-primary">{{ $awardsList->firstItem() + $index }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $badgeStyle }}">
                                        {{ $award->award_name ?? $award->award_level ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                            {{ strtoupper(substr($award->staff->staff_name ?? 'S', 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-medium text-text-main">{{ $award->staff->staff_name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $award->staff->faculty->faculty_code ?? ($award->staff->faculty->faculty_name ?? 'N/A') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80 max-w-xs truncate" title="{{ $award->project->project_title ?? '' }}">
                                    {{ $award->project->project_title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">
                                    {{ $award->award_date ? \Carbon\Carbon::parse($award->award_date)->format('Y-m-d') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-divider-subtle">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-4xl">emoji_events</span>
                                        <span>No award records found matching the specified filters</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-divider-subtle/30">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-divider-subtle">
                        Showing {{ $awardsList->firstItem() ?? 0 }} to {{ $awardsList->lastItem() ?? 0 }} of {{ $awardsList->total() }} results
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $awardsList->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
