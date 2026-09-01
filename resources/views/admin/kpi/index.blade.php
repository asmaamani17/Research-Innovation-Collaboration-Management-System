@extends('layouts.admin')

@section('title', 'KPI Dashboard')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-col">
        <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
            ST2 PELAN STRATEGIK UTEM 2026-2030
        </h2>
        <p class="text-divider-subtle font-medium text-sm mt-1">
            Memperkasa RDICE, Melestari Universiti
        </p>
    </div>
    <div class="flex items-center gap-3">
        <select id="yearSelector" class="px-4 py-2 rounded-lg border border-divider-subtle/70 bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50">
            @foreach($years as $year)
                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
        <a href="{{ route('admin.kpi.edit') }}"
            class="flex min-w-[120px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-sm">edit</span>
            <span>Edit</span>
        </a>
    </div>
</header>

<div class="px-8 pb-8">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Strategi</p>
                <p class="text-3xl font-bold text-text-main">{{ $strategies->count() }}</p>
            </div>
            <div class="bg-primary/10 p-3 rounded-lg text-primary">
                <span class="material-symbols-outlined text-2xl">military_tech</span>
            </div>
        </div>
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">Inisiatif</p>
                <p class="text-3xl font-bold text-text-main">12</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                <span class="material-symbols-outlined text-2xl">lightbulb</span>
            </div>
        </div>
        <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 shadow-sm flex items-center justify-between group hover:border-primary transition-colors">
            <div class="space-y-2">
                <p class="text-sm font-medium text-divider-subtle">KPI</p>
                <p class="text-3xl font-bold text-primary" id="totalKPICount">{{ $strategies->sum(function($s) { return $s->kpiRecords->count(); }) }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg text-purple-600">
                <span class="material-symbols-outlined text-2xl">assessment</span>
            </div>
        </div>
    </div>

    <div class="text-divider-subtle text-sm mb-6" id="lastUpdated"></div>

    <!-- KPI Dashboard Container -->
    <div id="groupsContainer"></div>
</div>

<!-- KPI Modal -->
<div id="kpiModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-divider-subtle/30 flex justify-between items-center">
            <h3 class="text-lg font-bold text-primary" id="kpiModalTitle">Maklumat</h3>
            <button id="closeModal" class="text-divider-subtle hover:text-text-main transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6" id="kpiModalBody"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
<script>
    // ============================================================
    // DATABASE DATA PASSED FROM LARAVEL
    // ============================================================
    let strategiesData = @json($strategies);
    let currentYear = {{ $currentYear }};
    const years = @json($years);

    // Convert Laravel data to match the expected structure
    let kpiMaster = [];
    let kpiIdCounter = 1;

    strategiesData.forEach(strategy => {
        strategy.kpi_records.forEach(kpi => {
            let kpiYear = (kpi.kpi_years || []).find(y => y.target_year == currentYear);
            let phases = kpiYear ? kpiYear.phases : [];
            let phasesByKey = {};
            phases.forEach(p => phasesByKey[p.phase] = p.achievement);

            let totalAchievement = (parseFloat(phasesByKey['Phase 1']) || 0) + 
                                    (parseFloat(phasesByKey['Phase 2']) || 0) + 
                                    (parseFloat(phasesByKey['Phase 3']) || 0) + 
                                    (parseFloat(phasesByKey['Phase 4']) || 0);
            
            let targetValue = 0;
            if (kpiYear && kpiYear.target_value) {
                let cleaned = kpiYear.target_value.toString().replace(/[RM,\s]/g, '');
                targetValue = parseFloat(cleaned) || 0;
            }

            let isRM = kpiYear && kpiYear.target_value && kpiYear.target_value.toString().includes('RM');
            
            // Build sasaran object for all years
            let sasaran = {};
            years.forEach(year => {
                let yearData = (kpi.kpi_years || []).find(y => y.target_year == year);
                if (yearData && yearData.target_value) {
                    let cleaned = yearData.target_value.toString().replace(/[RM,\s]/g, '');
                    sasaran[year] = parseFloat(cleaned) || 0;
                } else {
                    sasaran[year] = 0;
                }
            });

            // Build pencapaian for all years
            let pencapaian = {};
            years.forEach(year => {
                let yearData = (kpi.kpi_years || []).find(y => y.target_year == year);
                let yearPhases = yearData ? yearData.phases : [];
                let yearPhasesByKey = {};
                yearPhases.forEach(p => yearPhasesByKey[p.phase] = p.achievement);
                
                let yearTotal = (parseFloat(yearPhasesByKey['Phase 1']) || 0) + 
                                 (parseFloat(yearPhasesByKey['Phase 2']) || 0) + 
                                 (parseFloat(yearPhasesByKey['Phase 3']) || 0) + 
                                 (parseFloat(yearPhasesByKey['Phase 4']) || 0);
                
                pencapaian[year] = yearTotal;
            });

            // Build peratus for all years
            let peratus = {};
            years.forEach(year => {
                let yearData = (kpi.kpi_years || []).find(y => y.target_year == year);
                peratus[year] = yearData ? (yearData.achievement_percentage || 0) : 0;
            });

            // Build maklumatPencapaian for all years
            let maklumatPencapaian = {};
            years.forEach(year => {
                maklumatPencapaian[year] = kpi.achievement_info || 'Tiada';
            });

            // Determine chart type
            let chartType = 'bar';
            if (isRM || strategy.strategy_code === '2.4') {
                chartType = 'horizontal-bar';
            }

            kpiMaster.push({
                id: kpiIdCounter++,
                kod: strategy.strategy_code,
                inisiatif: kpi.initiative || '',
                nama: kpi.kpi_code + ' ' + kpi.performance_indicator,
                satuan: isRM ? 'RM' : '',
                sasaran: sasaran,
                pencapaian2026: pencapaian['2026'] || 0,
                pencapaian2027: pencapaian['2027'] || 0,
                pencapaian2028: pencapaian['2028'] || 0,
                pencapaian2029: pencapaian['2029'] || 0,
                pencapaian2030: pencapaian['2030'] || 0,
                peratus: peratus,
                maklumatPencapaian: maklumatPencapaian,
                pelanTindakan: kpi.action_plan || '',
                chartType: chartType
            });
        });
    });

    // ============================================================
    // DASHBOARD LOGIC
    // ============================================================
    var chartInstances = {};

    function calcPct(p, s) { if (!s || s === 0) return 0; return Math.min(100, (p / s) * 100); }

    function getStatus(p) { return p >= 100 ? "success" : p >= 80 ? "warning" : "danger"; }

    function trendIcon(p) { return p >= 100 ? "↑" : p >= 80 ? "→" : "↓"; }

    function getSasaran(k, y) { return k.sasaran[y] || k.sasaran["2026"] || 0; }

    function getPencapaian(k, y) { return k["pencapaian" + y] || 0; }

    function formatNum(num, satuan) {
        if (satuan === "RM") return "RM " + num.toLocaleString("id-ID");
        return num.toLocaleString("id-ID") + (satuan ? " " + satuan : "");
    }

    function esc(str) {
        if (!str) return "";
        return String(str).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function formatText(str) {
        if (!str || str === "Tiada") return "Tiada maklumat";
        return str.split("\n").filter(function(l) { return l.trim(); }).map(function(l) {
            return esc(l.trim().replace(/^\d+\.\s+/, "").replace(/^•\s*/, ""));
        }).join("<br>");
    }

    function isPieKpi(kpiId) {
        return KPI_STRATEGY_MAP[kpiId] === "2.2";
    }

    function buildKpiCard(kpi, year) {
        var s = getSasaran(kpi, year),
            p = getPencapaian(kpi, year),
            pct = calcPct(p, s);
        var st = getStatus(pct);
        var pc = pct >= 100 ? "#13deb9" : pct >= 80 ? "#ffc107" : "#f8285a";
        var sc = "#8a99b0";
        var pie = isPieKpi(kpi.id);
        
        var statusClass = st === "success" ? "bg-green-400 text-white" : st === "warning" ? "bg-yellow-400 text-gray-900" : "bg-red-400 text-white";
        
        var h = '<div class="bg-white border border-divider-subtle/30 rounded-xl p-4 shadow-sm">';
        h += '<div class="flex justify-between items-start mb-3">';
        h += '<h6 class="font-bold text-primary text-sm mb-0 mr-2">' + esc(kpi.nama) + '</h6>';
        h += '<span class="px-3 py-1 rounded-full text-xs font-bold ' + statusClass + ' flex items-center gap-0.5 leading-none">' + pct.toFixed(1) + '% <span class="text-[9px] leading-none">' + trendIcon(pct) + '</span></span>';
        h += '</div>';
        h += '<div class="grid grid-cols-2 gap-3 mb-3">';
        h += '<div class="bg-divider-subtle/5 rounded-lg p-3 relative cursor-pointer hover:bg-divider-subtle/10 transition-colors" data-kpi-type="sasaran" data-kpi-id="' + kpi.id + '">';
        h += '<span class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-primary text-primary text-xs font-bold flex items-center justify-center">i</span>';
        h += '<p class="text-xs text-divider-subtle font-bold uppercase mb-1">Sasaran ' + year + '</p>';
        h += '<p class="text-lg font-bold text-primary">' + formatNum(s, kpi.satuan) + '</p>';
        h += '</div>';
        h += '<div class="bg-primary/5 rounded-lg p-3 border border-primary/20 relative cursor-pointer hover:bg-primary/10 transition-colors" data-kpi-type="pencapaian" data-kpi-id="' + kpi.id + '">';
        h += '<span class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-primary text-primary text-xs font-bold flex items-center justify-center">i</span>';
        h += '<p class="text-xs text-divider-subtle font-bold uppercase mb-1">Pencapaian</p>';
        h += '<p class="text-lg font-bold text-primary">' + formatNum(p, kpi.satuan) + '</p>';
        h += '</div>';
        h += '</div>';
        h += '<div class="h-36' + (pie ? ' flex items-center justify-center' : '') + '"><div id="chart-' + kpi.id + '"></div></div>';
        if (!pie) {
            h += '<div class="flex justify-center gap-3 mt-6">';
            h += '<span class="text-xs text-divider-subtle"><span class="inline-block w-3 h-3 rounded mr-1" style="background:' + sc + '"></span>Sasaran</span>';
            h += '<span class="text-xs text-divider-subtle"><span class="inline-block w-3 h-3 rounded mr-1" style="background:' + pc + '"></span>Pencapaian</span>';
            h += '</div>';
        }
        h += '</div>';
        return h;
    }

    function chartOpts(kpi, year) {
        var s = getSasaran(kpi, year),
            p = getPencapaian(kpi, year),
            pct = calcPct(p, s);
        var pc = pct >= 100 ? "#13deb9" : pct >= 80 ? "#ffc107" : "#f8285a",
            sc = "#8a99b0";
        var fmt = function(v) { return v.toLocaleString("id-ID"); };
        var tip = function(v) { return kpi.satuan === "RM" ? "RM " + v.toLocaleString("id-ID") : v.toLocaleString("id-ID"); };

        if (isPieKpi(kpi.id)) {
            return {
                series: [s, p],
                labels: ["Sasaran", "Pencapaian"],
                colors: [sc, pc],
                chart: { type: "pie", height: 200, toolbar: { show: false }, redrawOnParentResize: true },
                legend: { show: true, position: "bottom", fontSize: "11px", markers: { width: 10, height: 10 } },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) { return fmt(opts.w.config.series[opts.seriesIndex]); },
                    style: { fontSize: "11px" }
                },
                tooltip: { y: { formatter: tip } },
                stroke: { width: 2, colors: ["#fff"] },
                responsive: [
                    { breakpoint: 768, options: { chart: { height: 180 }, legend: { fontSize: "9px" },
                            dataLabels: { style: { fontSize: "9px" } } } },
                    { breakpoint: 576, options: { chart: { height: 160 }, legend: { fontSize: "8px" },
                            dataLabels: { style: { fontSize: "8px" } } } }
                ]
            };
        }

        var base = {
            series: [{ name: "KPI", data: [s, p] }],
            colors: [sc, pc],
            legend: { show: false },
            dataLabels: { enabled: true, formatter: fmt },
            tooltip: { y: { formatter: tip } },
            xaxis: { categories: ["Sasaran", "Pencapaian"], labels: { style: { fontSize: "11px" } } },
            responsive: [
                { breakpoint: 768, options: { chart: { height: 145 }, xaxis: { labels: { style: { fontSize: "9px" } } },
                        dataLabels: { style: { fontSize: "9px" } } } },
                { breakpoint: 576, options: { chart: { height: 135 }, xaxis: { labels: { style: { fontSize: "8px" } } },
                        dataLabels: { style: { fontSize: "8px" } } } }
            ]
        };
        if (kpi.chartType === "horizontal-bar") {
            base.chart = { type: "bar", height: 165, toolbar: { show: false }, redrawOnParentResize: true };
            base.plotOptions = { bar: { borderRadius: 8, horizontal: true, distributed: true, barHeight: "55%" } };
        } else {
            base.chart = { type: "bar", height: 165, toolbar: { show: false }, redrawOnParentResize: true };
            base.plotOptions = { bar: { borderRadius: 8, columnWidth: "55%", distributed: true } };
            base.yaxis = { min: 0, labels: { style: { fontSize: "11px" } } };
        }
        return base;
    }

    function renderCharts(year) {
        kpiMaster.forEach(function(kpi) {
            var el = document.getElementById("chart-" + kpi.id);
            if (!el) return;
            if (chartInstances[kpi.id]) { try { chartInstances[kpi.id].destroy(); } catch (e) {} }
            var c = new ApexCharts(el, chartOpts(kpi, year));
            c.render();
            chartInstances[kpi.id] = c;
        });
    }

    // Build STRATEGIES structure from database data
    var STRATEGIES = [];
    var KPI_STRATEGY_MAP = {};

    strategiesData.forEach(strategy => {
        var sections = [];
        var groupedByInitiative = {};
        
        strategy.kpi_records.forEach(kpi => {
            var initiative = kpi.initiative || '';
            if (!groupedByInitiative[initiative]) {
                groupedByInitiative[initiative] = [];
            }
            groupedByInitiative[initiative].push(kpi);
        });

        // For Strategy 2.2, put all KPIs in one section regardless of initiative
        if (strategy.strategy_code === '2.2') {
            var allKpiIds = [];
            var initiativeName = '';
            strategy.kpi_records.forEach(kpi => {
                var kpiInMaster = kpiMaster.find(function(k) { 
                    return k.kod === strategy.strategy_code && 
                           k.nama.includes(kpi.kpi_code);
                });
                if (kpiInMaster) {
                    allKpiIds.push(kpiInMaster.id);
                    KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                    if (!initiativeName && kpi.initiative) {
                        initiativeName = kpi.initiative;
                    }
                }
            });

            // Determine Tailwind grid classes based on strategy code
            var cols = "col-span-1 md:col-span-1 lg:col-span-2";

            sections.push({
                sub: initiativeName,
                kpiIds: allKpiIds,
                cols: cols
            });
        } else if (strategy.strategy_code === '2.3') {
            // For Strategy 2.3, group by KPI code prefix (2.3.1.x and 2.3.2.x)
            var groupA = [];
            var groupB = [];
            var initiativeA = '';
            var initiativeB = '';
            
            strategy.kpi_records.forEach(kpi => {
                var kpiInMaster = kpiMaster.find(function(k) { 
                    return k.kod === strategy.strategy_code && 
                           k.nama.includes(kpi.kpi_code);
                });
                if (kpiInMaster) {
                    KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                    // Group by KPI code prefix
                    if (kpi.kpi_code.startsWith('2.3.1')) {
                        groupA.push(kpiInMaster.id);
                        if (!initiativeA && kpi.initiative) {
                            initiativeA = kpi.initiative;
                        }
                    } else if (kpi.kpi_code.startsWith('2.3.2')) {
                        groupB.push(kpiInMaster.id);
                        if (!initiativeB && kpi.initiative) {
                            initiativeB = kpi.initiative;
                        }
                    }
                }
            });

            // Determine Tailwind grid classes based on strategy code
            var cols = "col-span-1 md:col-span-1 lg:col-span-1";

            if (groupA.length > 0) {
                sections.push({
                    sub: initiativeA,
                    kpiIds: groupA,
                    cols: cols
                });
            }
            if (groupB.length > 0) {
                sections.push({
                    sub: initiativeB,
                    kpiIds: groupB,
                    cols: cols
                });
            }
        } else if (strategy.strategy_code === '2.4') {
            // For Strategy 2.4, put all KPIs in one section with initiative info stored
            var allKpiIds = [];
            var initiativeMap = {}; // Store initiative for each KPI ID
            
            strategy.kpi_records.forEach(kpi => {
                var kpiInMaster = kpiMaster.find(function(k) { 
                    return k.kod === strategy.strategy_code && 
                           k.nama.includes(kpi.kpi_code);
                });
                if (kpiInMaster) {
                    allKpiIds.push(kpiInMaster.id);
                    KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                    initiativeMap[kpiInMaster.id] = kpi.initiative || '';
                }
            });

            // Determine Tailwind grid classes based on strategy code
            var cols = "col-span-1 md:col-span-1 lg:col-span-2";

            sections.push({
                sub: null, // No single initiative label
                kpiIds: allKpiIds,
                cols: cols,
                initiativeMap: initiativeMap // Store initiative mapping
            });
        } else if (strategy.strategy_code === '2.5') {
            // For Strategy 2.5, put all KPIs in one section with single initiative label
            var allKpiIds = [];
            var initiativeName = '';
            
            strategy.kpi_records.forEach(kpi => {
                var kpiInMaster = kpiMaster.find(function(k) { 
                    return k.kod === strategy.strategy_code && 
                           k.nama.includes(kpi.kpi_code);
                });
                if (kpiInMaster) {
                    allKpiIds.push(kpiInMaster.id);
                    KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                    if (!initiativeName && kpi.initiative) {
                        initiativeName = kpi.initiative;
                    }
                }
            });

            // Determine Tailwind grid classes based on strategy code
            var cols = "col-span-1 md:col-span-1 lg:col-span-1";

            sections.push({
                sub: initiativeName, // Single initiative label
                kpiIds: allKpiIds,
                cols: cols
            });
        } else {
            // For other strategies, group by initiative
            Object.keys(groupedByInitiative).forEach(initiative => {
                var kpis = groupedByInitiative[initiative];
                var sectionKpiIds = [];
                
                kpis.forEach(kpi => {
                    var kpiInMaster = kpiMaster.find(function(k) { 
                        return k.kod === strategy.strategy_code && 
                               k.inisiatif === initiative &&
                               k.nama.includes(kpi.kpi_code);
                    });
                    if (kpiInMaster) {
                        sectionKpiIds.push(kpiInMaster.id);
                        KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                    }
                });

                // Determine Tailwind grid classes based on strategy code
                var cols;
                if (strategy.strategy_code === '2.1') {
                    cols = "col-span-1 md:col-span-2 lg:col-span-4";
                } else if (strategy.strategy_code === '2.4') {
                    cols = "col-span-1 md:col-span-1 lg:col-span-2";
                } else if (strategy.strategy_code === '2.5') {
                    cols = "col-span-1 md:col-span-1 lg:col-span-1";
                } else {
                    if (kpis.length === 1) {
                        cols = "col-span-1 md:col-span-2 lg:col-span-4";
                    } else if (kpis.length === 2) {
                        cols = "col-span-1 md:col-span-1 lg:col-span-2";
                    } else {
                        cols = "col-span-1 md:col-span-1 lg:col-span-1";
                    }
                }

                sections.push({
                    sub: initiative,
                    kpiIds: sectionKpiIds,
                    cols: cols
                });
            });
        }

        STRATEGIES.push({
            num: strategy.strategy_code,
            title: strategy.strategy_name,
            sections: sections
        });
    });

    function buildDashboard(year) {
        var container = document.getElementById("groupsContainer");
        if (!container || !kpiMaster.length) return;
        var html = "";
        STRATEGIES.forEach(function(strat) {
            html += '<div class="bg-white border border-divider-subtle/30 rounded-xl p-4 md:p-6 shadow-sm mb-6">';
            html += '<div class="flex gap-3 mb-4">';
            html += '<div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">';
            html += '<span class="material-symbols-outlined text-primary text-2xl">military_tech</span>';
            html += '</div>';
            html += '<div>';
            html += '<p class="text-xs text-divider-subtle font-bold uppercase tracking-wider">Strategi ' + strat.num + '</p>';
            html += '<h5 class="font-bold text-primary text-base">' + strat.title + '</h5>';
            html += '</div>';
            html += '</div>';
            
            strat.sections.forEach(function(sec) {
                if (sec.sub) {
                    html += '<div class="bg-gradient-to-r from-primary/5 to-primary/0 border-l-4 border-primary rounded-r-lg p-3 mb-4">';
                    html += '<p class="text-xs text-divider-subtle font-bold uppercase tracking-wider mb-1">Inisiatif</p>';
                    html += '<p class="text-primary font-bold text-sm">' + esc(sec.sub) + '</p>';
                    html += '</div>';
                }
                html += '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">';
                sec.kpiIds.forEach(function(id) {
                    var kpi = kpiMaster.find(function(k) { return k.id === id; });
                    if (!kpi) return;
                    // Show individual initiative label if initiativeMap exists and this KPI has an initiative
                    var initiativeLabel = '';
                    if (sec.initiativeMap && sec.initiativeMap[id]) {
                        initiativeLabel = '<div class="bg-gradient-to-r from-primary/5 to-primary/0 border-l-4 border-primary rounded-r-lg p-2 mb-3">';
                        initiativeLabel += '<p class="text-xs text-divider-subtle font-bold uppercase tracking-wider mb-1">Inisiatif</p>';
                        initiativeLabel += '<p class="text-primary font-bold text-xs">' + esc(sec.initiativeMap[id]) + '</p>';
                        initiativeLabel += '</div>';
                    }
                    html += '<div class="' + sec.cols + '">' + initiativeLabel + buildKpiCard(kpi, year) + '</div>';
                });
                html += '</div>';
            });
            html += '</div>';
        });
        container.innerHTML = html;
    }

    function openInfoModal(type, kpiId) {
        var kpi = kpiMaster.find(function(k) { return k.id == kpiId; });
        if (!kpi) return;
        var t = type === "sasaran" ? "Pelan Tindakan: " : "Maklumat Pencapaian: ";
        var txt = type === "sasaran" ? kpi.pelanTindakan : kpi.maklumatPencapaian[currentYear] || kpi.maklumatPencapaian["2026"];
        document.getElementById("kpiModalTitle").innerText = t + kpi.nama;
        document.getElementById("kpiModalBody").innerHTML = '<div class="bg-background-light rounded-lg border-l-4 border-primary p-4"><div class="text-divider-subtle leading-relaxed">' + formatText(txt) + '</div></div>';
        document.getElementById("kpiModal").classList.remove("hidden");
        document.getElementById("kpiModal").classList.add("flex");
    }

    function closeModal() {
        document.getElementById("kpiModal").classList.add("hidden");
        document.getElementById("kpiModal").classList.remove("flex");
    }

    document.getElementById("yearSelector").addEventListener("change", function(e) {
        currentYear = parseInt(e.target.value);
        refreshLiveData();
    });

    document.getElementById("closeModal").addEventListener("click", closeModal);
    document.getElementById("kpiModal").addEventListener("click", function(e) {
        if (e.target === this) closeModal();
    });

    document.body.addEventListener("click", function(e) {
        var btn = e.target.closest("[data-kpi-type]");
        if (!btn) return;
        e.preventDefault();
        openInfoModal(btn.getAttribute("data-kpi-type"), parseInt(btn.getAttribute("data-kpi-id"), 10));
    });

    // Live data refresh function
    async function refreshLiveData() {
        try {
            let response = await fetch(`{{ route('admin.kpi.data') }}?year=${currentYear}`);
            let data = await response.json();
            
            if (data && data.strategies) {
                // Update strategiesData with fresh data
                while(strategiesData.length > 0) {
                    strategiesData.pop();
                }
                strategiesData.push(...data.strategies);

                // Update kpiMaster with fresh data
                let newKpiMaster = [];
                let kpiIdCounter = 1;

                data.strategies.forEach(strategy => {
                    strategy.kpi_records.forEach(kpi => {
                        let kpiYear = (kpi.kpi_years || []).find(y => y.target_year == currentYear);
                        let phases = kpiYear ? kpiYear.phases : [];
                        let phasesByKey = {};
                        phases.forEach(p => phasesByKey[p.phase] = p.achievement);

                        let totalAchievement = (parseFloat(phasesByKey['Phase 1']) || 0) + 
                                                (parseFloat(phasesByKey['Phase 2']) || 0) + 
                                                (parseFloat(phasesByKey['Phase 3']) || 0) + 
                                                (parseFloat(phasesByKey['Phase 4']) || 0);
                        
                        let targetValue = 0;
                        if (kpiYear && kpiYear.target_value) {
                            let cleaned = kpiYear.target_value.toString().replace(/[RM,\s]/g, '');
                            targetValue = parseFloat(cleaned) || 0;
                        }

                        let isRM = kpiYear && kpiYear.target_value && kpiYear.target_value.toString().includes('RM');
                        
                        // Build sasaran object for all years
                        let sasaran = {};
                        years.forEach(year => {
                            let yearData = (kpi.kpi_years || []).find(y => y.target_year == year);
                            if (yearData && yearData.target_value) {
                                let cleaned = yearData.target_value.toString().replace(/[RM,\s]/g, '');
                                sasaran[year] = parseFloat(cleaned) || 0;
                            } else {
                                sasaran[year] = 0;
                            }
                        });

                        // Build pencapaian for all years
                        let pencapaian = {};
                        years.forEach(year => {
                            let yearData = (kpi.kpi_years || []).find(y => y.target_year == year);
                            let yearPhases = yearData ? yearData.phases : [];
                            let yearPhasesByKey = {};
                            yearPhases.forEach(p => yearPhasesByKey[p.phase] = p.achievement);
                            
                            let yearTotal = (parseFloat(yearPhasesByKey['Phase 1']) || 0) + 
                                             (parseFloat(yearPhasesByKey['Phase 2']) || 0) + 
                                             (parseFloat(yearPhasesByKey['Phase 3']) || 0) + 
                                             (parseFloat(yearPhasesByKey['Phase 4']) || 0);
                            
                            pencapaian[year] = yearTotal;
                        });

                        // Build peratus for all years
                        let peratus = {};
                        years.forEach(year => {
                            let yearData = (kpi.kpi_years || []).find(y => y.target_year == year);
                            peratus[year] = yearData ? (yearData.achievement_percentage || 0) : 0;
                        });

                        // Build maklumatPencapaian for all years
                        let maklumatPencapaian = {};
                        years.forEach(year => {
                            maklumatPencapaian[year] = kpi.achievement_info || 'Tiada';
                        });

                        // Determine chart type
                        let chartType = 'bar';
                        if (isRM || strategy.strategy_code === '2.4') {
                            chartType = 'horizontal-bar';
                        }

                        newKpiMaster.push({
                            id: kpiIdCounter++,
                            kod: strategy.strategy_code,
                            inisiatif: kpi.initiative || '',
                            nama: kpi.kpi_code + ' ' + kpi.performance_indicator,
                            satuan: isRM ? 'RM' : '',
                            sasaran: sasaran,
                            pencapaian2026: pencapaian['2026'] || 0,
                            pencapaian2027: pencapaian['2027'] || 0,
                            pencapaian2028: pencapaian['2028'] || 0,
                            pencapaian2029: pencapaian['2029'] || 0,
                            pencapaian2030: pencapaian['2030'] || 0,
                            peratus: peratus,
                            maklumatPencapaian: maklumatPencapaian,
                            pelanTindakan: kpi.action_plan || '',
                            chartType: chartType
                        });
                    });
                });

                // Update global kpiMaster
                while(kpiMaster.length > 0) {
                    kpiMaster.pop();
                }
                newKpiMaster.forEach(item => kpiMaster.push(item));

                // Rebuild STRATEGIES structure
                while(STRATEGIES.length > 0) {
                    STRATEGIES.pop();
                }
                for (var key in KPI_STRATEGY_MAP) {
                    delete KPI_STRATEGY_MAP[key];
                }
                
                strategiesData.forEach(strategy => {
                    var sections = [];
                    var groupedByInitiative = {};
                    
                    strategy.kpi_records.forEach(kpi => {
                        var initiative = kpi.initiative || '';
                        if (!groupedByInitiative[initiative]) {
                            groupedByInitiative[initiative] = [];
                        }
                        groupedByInitiative[initiative].push(kpi);
                    });

                    // For Strategy 2.2, put all KPIs in one section regardless of initiative
                    if (strategy.strategy_code === '2.2') {
                        var allKpiIds = [];
                        var initiativeName = '';
                        strategy.kpi_records.forEach(kpi => {
                            var kpiInMaster = kpiMaster.find(function(k) { 
                                return k.kod === strategy.strategy_code && 
                                       k.nama.includes(kpi.kpi_code);
                            });
                            if (kpiInMaster) {
                                allKpiIds.push(kpiInMaster.id);
                                KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                                if (!initiativeName && kpi.initiative) {
                                    initiativeName = kpi.initiative;
                                }
                            }
                        });

                        var cols = "col-span-1 md:col-span-1 lg:col-span-2";

                        sections.push({
                            sub: initiativeName,
                            kpiIds: allKpiIds,
                            cols: cols
                        });
                    } else if (strategy.strategy_code === '2.3') {
                        var groupA = [];
                        var groupB = [];
                        var initiativeA = '';
                        var initiativeB = '';
                        
                        strategy.kpi_records.forEach(kpi => {
                            var kpiInMaster = kpiMaster.find(function(k) { 
                                return k.kod === strategy.strategy_code && 
                                       k.nama.includes(kpi.kpi_code);
                            });
                            if (kpiInMaster) {
                                KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                                if (kpi.kpi_code.startsWith('2.3.1')) {
                                    groupA.push(kpiInMaster.id);
                                    if (!initiativeA && kpi.initiative) {
                                        initiativeA = kpi.initiative;
                                    }
                                } else if (kpi.kpi_code.startsWith('2.3.2')) {
                                    groupB.push(kpiInMaster.id);
                                    if (!initiativeB && kpi.initiative) {
                                        initiativeB = kpi.initiative;
                                    }
                                }
                            }
                        });

                        var cols = "col-span-1 md:col-span-1 lg:col-span-1";

                        if (groupA.length > 0) {
                            sections.push({
                                sub: initiativeA,
                                kpiIds: groupA,
                                cols: cols
                            });
                        }
                        if (groupB.length > 0) {
                            sections.push({
                                sub: initiativeB,
                                kpiIds: groupB,
                                cols: cols
                            });
                        }
                    } else if (strategy.strategy_code === '2.4') {
                        var allKpiIds = [];
                        var initiativeMap = {};
                        
                        strategy.kpi_records.forEach(kpi => {
                            var kpiInMaster = kpiMaster.find(function(k) { 
                                return k.kod === strategy.strategy_code && 
                                       k.nama.includes(kpi.kpi_code);
                            });
                            if (kpiInMaster) {
                                allKpiIds.push(kpiInMaster.id);
                                KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                                initiativeMap[kpiInMaster.id] = kpi.initiative || '';
                            }
                        });

                        var cols = "col-span-1 md:col-span-1 lg:col-span-2";

                        sections.push({
                            sub: null,
                            kpiIds: allKpiIds,
                            cols: cols,
                            initiativeMap: initiativeMap
                        });
                    } else if (strategy.strategy_code === '2.5') {
                        var allKpiIds = [];
                        var initiativeName = '';
                        
                        strategy.kpi_records.forEach(kpi => {
                            var kpiInMaster = kpiMaster.find(function(k) { 
                                return k.kod === strategy.strategy_code && 
                                       k.nama.includes(kpi.kpi_code);
                            });
                            if (kpiInMaster) {
                                allKpiIds.push(kpiInMaster.id);
                                KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                                if (!initiativeName && kpi.initiative) {
                                    initiativeName = kpi.initiative;
                                }
                            }
                        });

                        var cols = "col-span-1 md:col-span-1 lg:col-span-1";

                        sections.push({
                            sub: initiativeName,
                            kpiIds: allKpiIds,
                            cols: cols
                        });
                    } else {
                        Object.keys(groupedByInitiative).forEach(initiative => {
                            var kpis = groupedByInitiative[initiative];
                            var sectionKpiIds = [];
                            
                            kpis.forEach(kpi => {
                                var kpiInMaster = kpiMaster.find(function(k) { 
                                    return k.kod === strategy.strategy_code && 
                                           k.inisiatif === initiative &&
                                           k.nama.includes(kpi.kpi_code);
                                });
                                if (kpiInMaster) {
                                    sectionKpiIds.push(kpiInMaster.id);
                                    KPI_STRATEGY_MAP[kpiInMaster.id] = strategy.strategy_code;
                                }
                            });

                            var cols;
                            if (strategy.strategy_code === '2.1') {
                                cols = "col-span-1 md:col-span-2 lg:col-span-4";
                            } else if (strategy.strategy_code === '2.4') {
                                cols = "col-span-1 md:col-span-1 lg:col-span-2";
                            } else if (strategy.strategy_code === '2.5') {
                                cols = "col-span-1 md:col-span-1 lg:col-span-1";
                            } else {
                                if (kpis.length === 1) {
                                    cols = "col-span-1 md:col-span-2 lg:col-span-4";
                                } else if (kpis.length === 2) {
                                    cols = "col-span-1 md:col-span-1 lg:col-span-2";
                                } else {
                                    cols = "col-span-1 md:col-span-1 lg:col-span-1";
                                }
                            }

                            sections.push({
                                sub: initiative,
                                kpiIds: sectionKpiIds,
                                cols: cols
                            });
                        });
                    }

                    STRATEGIES.push({
                        num: strategy.strategy_code,
                        title: strategy.strategy_name,
                        sections: sections
                    });
                });

                // Rebuild dashboard and charts
                buildDashboard(currentYear);
                renderCharts(currentYear);

                // Update last updated time
                document.getElementById("lastUpdated").innerHTML = "Kemaskini: " + new Date().toLocaleString("ms-MY");
            }
        } catch (error) {
            console.error('Failed to refresh live data:', error);
        }
    }

    // ========== INIT ==========
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("totalKPICount").innerText = kpiMaster.length;
        document.getElementById("lastUpdated").innerHTML = "Kemaskini: " + new Date().toLocaleString("ms-MY");
        buildDashboard(currentYear);
        setTimeout(function() { renderCharts(currentYear); }, 100);

        // Set up live data refresh every 30 seconds
        setInterval(refreshLiveData, 30000);
    });
</script>
@endpush
