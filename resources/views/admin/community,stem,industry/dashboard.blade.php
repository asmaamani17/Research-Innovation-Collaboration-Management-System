@extends('layouts.admin')
@section('title', 'Community, STEM & Industry Dashboard')

@section('content')
  <div class="w-full pb-8 text-text-main">
  @php
    // Sama konsep dgn Senarai Rekod -- bila diakses melalui public.dashboard,
    // kekalkan filter/reset dalam laluan public (bukan terlantar ke laluan
    // admin). Dashboard ni sendiri MEMANG dah read-only sepenuhnya (tiada
    // butang edit/tambah) -- cuma route filter yang perlu disesuaikan.
    $routeIndex = 'admin.stem.dashboard';
  @endphp
  <header class="flex items-center justify-between px-4 py-6 sm:px-8 sm:py-8">
    <div>
      <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Dashboard Program Komuniti, STEM &amp; Industri</h1>
      <p class="mt-1 text-sm font-medium leading-5 text-divider-subtle">Overview of community, STEM and industry programmes, participation, and monthly activity.</p>
    </div>
    <a href="{{ route('admin.stem.panduan') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-divider-subtle/50 bg-white px-5 py-3 text-sm font-bold text-text-main transition hover:bg-background-light">
      <span class="material-symbols-outlined" aria-hidden="true">menu_book</span>
      Panduan
    </a>
  </header>

  <div class="space-y-6 px-4 sm:px-8">
  {{-- Bar Penapis -- GET supaya boleh dikongsi/bookmark, dan senang di-reset.
       Sama struktur/class dgn bar penapis Senarai Rekod (.form-field), cuma
       2 medan (Tahun + Pusat) sebab dashboard ni paparan ringkasan/agregat. --}}
  <div class="rounded-xl border border-divider-subtle/30 bg-white p-4 shadow-sm sm:p-6">
    <form method="GET" action="{{ route($routeIndex) }}">
      <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-[1fr_1fr_auto]">
        <div>
          <label class="mb-2 block text-sm font-medium text-text-main">Tahun</label>
          <select name="tahun" onchange="this.form.submit()" class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
            <option value="">Semua Tahun</option>
            @foreach($senaraiTahunFilter as $tahun)
              <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>{{ $tahun }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-text-main">Pusat Tanggungjawab</label>
          <select name="pusat" onchange="this.form.submit()" class="w-full rounded-lg border border-divider-subtle/50 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
            <option value="">Semua Pusat</option>
            @foreach($senaraiPusatFilter as $pusat)
              <option value="{{ $pusat }}" @selected(request('pusat') == $pusat)>{{ $pusat }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <a href="{{ route($routeIndex) }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-divider-subtle/50 bg-white px-5 py-3 text-sm font-bold text-text-main transition hover:bg-background-light">Reset</a>
        </div>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="group flex items-center justify-between gap-4 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm transition-colors hover:border-primary sm:p-6">
      <div>
        <div class="text-sm font-medium text-divider-subtle">Jumlah Penghantaran</div>
        <div class="mt-2 text-3xl font-bold text-text-main" id="stat-total">{{ number_format($total) }}</div>
        <div class="text-xs text-divider-subtle">rekod {{ request('tahun') ? 'tahun ' . request('tahun') : 'semua tahun' }}</div>
      </div>
      <div class="flex shrink-0 items-center justify-center rounded-lg bg-primary/10 p-3 text-primary">
        <span class="material-symbols-outlined text-2xl" aria-hidden="true">bar_chart</span>
      </div>
    </div>
    <div class="group flex items-center justify-between gap-4 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm transition-colors hover:border-primary sm:p-6">
      <div>
        <div class="text-sm font-medium text-divider-subtle">Program STEM</div>
        <div class="mt-2 text-3xl font-bold text-[#3E7A5C]" id="stat-stem">{{ number_format($stemCount) }}</div>
        <div class="text-xs text-divider-subtle"><span id="stat-stem-pct">{{ $stemPct }}</span>% daripada jumlah</div>
      </div>
      <div class="flex shrink-0 items-center justify-center rounded-lg bg-[#3E7A5C]/10 p-3 text-[#3E7A5C]">
        <span class="material-symbols-outlined text-2xl" aria-hidden="true">science</span>
      </div>
    </div>
    <div class="group flex items-center justify-between gap-4 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm transition-colors hover:border-primary sm:p-6">
      <div>
        <div class="text-sm font-medium text-divider-subtle">Program Komuniti</div>
        <div class="mt-2 text-3xl font-bold text-[#B0623E]" id="stat-komuniti">{{ number_format($komunitiCount) }}</div>
        <div class="text-xs text-divider-subtle"><span id="stat-komuniti-pct">{{ $komunitiPct }}</span>% daripada jumlah</div>
      </div>
      <div class="flex shrink-0 items-center justify-center rounded-lg bg-[#B0623E]/10 p-3 text-[#B0623E]">
        <span class="material-symbols-outlined text-2xl" aria-hidden="true">groups</span>
      </div>
    </div>
    <div class="group flex items-center justify-between gap-4 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm transition-colors hover:border-primary sm:p-6">
      <div>
        <div class="text-sm font-medium text-divider-subtle">Program Industri</div>
        <div class="mt-2 text-3xl font-bold text-[#6E5AA8]" id="stat-industri">{{ number_format($industriCount) }}</div>
        <div class="text-xs text-divider-subtle"><span id="stat-industri-pct">{{ $industriPct }}</span>% daripada jumlah</div>
      </div>
      <div class="flex shrink-0 items-center justify-center rounded-lg bg-[#6E5AA8]/10 p-3 text-[#6E5AA8]">
        <span class="material-symbols-outlined text-2xl" aria-hidden="true">factory</span>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.4fr_1fr]">
    <div class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
      <div class="flex items-center justify-between mb-1">
        <div class="text-lg font-bold text-text-main">Penghantaran Mengikut Pusat</div>
        <button id="refresh-pusat" onclick="refreshChart('pusat')" class="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
          <span class="material-symbols-outlined text-sm">refresh</span>
        </button>
      </div>
      <div class="mb-4 text-sm text-divider-subtle">Pecahan STEM vs Komuniti vs Industri bagi setiap pusat</div>
      <div class="h-64">
        <canvas id="carta-pusat"></canvas>
      </div>
    </div>
    <div class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
      <div class="flex items-center justify-between mb-1">
        <div class="text-lg font-bold text-text-main">STEM vs Komuniti vs Industri</div>
        <button id="refresh-nisbah" onclick="refreshChart('nisbah')" class="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
          <span class="material-symbols-outlined text-sm">refresh</span>
        </button>
      </div>
      <div class="mb-4 text-sm text-divider-subtle">Nisbah keseluruhan klasifikasi program</div>
      <div class="h-64">
        <canvas id="carta-nisbah"></canvas>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.4fr_1fr]">
    <div class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
      <div class="flex items-center justify-between mb-1">
        <div class="text-lg font-bold text-text-main">Trend Bulanan</div>
        <button id="refresh-bulanan" onclick="refreshChart('bulanan')" class="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
          <span class="material-symbols-outlined text-sm">refresh</span>
        </button>
      </div>
      <div class="mb-4 text-sm text-divider-subtle">Jumlah program mengikut bulan.</div>
      <div class="h-64">
        <canvas id="carta-bulanan"></canvas>
      </div>
    </div>
    <div class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
      <div class="flex items-center justify-between mb-1">
        <div class="text-lg font-bold text-text-main">Kategori Program (KSTP / CSR)</div>
        <button id="refresh-kategori" onclick="refreshChart('kategori')" class="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
          <span class="material-symbols-outlined text-sm">refresh</span>
        </button>
      </div>
      <div class="mb-4 text-sm text-divider-subtle">Taburan mengikut kategori borang</div>
      <div class="h-64">
        <canvas id="carta-kategori"></canvas>
      </div>
    </div>
  </div>

  </div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartApiUrl = "{{ route('admin.stem.data') }}";
    let chartPusat, chartNisbah, chartBulanan, chartKategori;

    // Initialize all charts
    async function initCharts() {
        await Promise.all([
            loadStats(),
            loadPusatChart(),
            loadNisbahChart(),
            loadBulananChart(),
            loadKategoriChart()
        ]);
    }

    // Load Stats
    async function loadStats() {
        const response = await fetch(`${chartApiUrl}?type=stats${getFilterParams()}`);
        const data = await response.json();

        document.getElementById('stat-total').textContent = new Intl.NumberFormat().format(data.total);
        document.getElementById('stat-stem').textContent = new Intl.NumberFormat().format(data.stemCount);
        document.getElementById('stat-stem-pct').textContent = data.stemPct;
        document.getElementById('stat-komuniti').textContent = new Intl.NumberFormat().format(data.komunitiCount);
        document.getElementById('stat-komuniti-pct').textContent = data.komunitiPct;
        document.getElementById('stat-industri').textContent = new Intl.NumberFormat().format(data.industriCount);
        document.getElementById('stat-industri-pct').textContent = data.industriPct;
    }

    // Load Pusat Chart (Stacked Bar)
    async function loadPusatChart() {
        const response = await fetch(`${chartApiUrl}?type=pusat${getFilterParams()}`);
        const data = await response.json();

        const ctx = document.getElementById('carta-pusat');
        if (!ctx) return;

        if (chartPusat) chartPusat.destroy();

        chartPusat = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.senaraiPusat,
                datasets: [
                    { label: 'STEM',     data: data.stemSiri,     backgroundColor: '#3E7A5C' },
                    { label: 'Komuniti', data: data.komunitiSiri, backgroundColor: '#B0623E' },
                    { label: 'Industri', data: data.industriSiri, backgroundColor: '#6E5AA8' },
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: { x: { stacked: true }, y: { stacked: true } }
            }
        });
    }

    // Load Nisbah Chart (Doughnut)
    async function loadNisbahChart() {
        const response = await fetch(`${chartApiUrl}?type=nisbah${getFilterParams()}`);
        const data = await response.json();

        const ctx = document.getElementById('carta-nisbah');
        if (!ctx) return;

        if (chartNisbah) chartNisbah.destroy();

        chartNisbah = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['STEM', 'Komuniti', 'Industri'],
                datasets: [{
                    data: [data.stemCount, data.komunitiCount, data.industriCount],
                    backgroundColor: ['#3E7A5C', '#B0623E', '#6E5AA8']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Load Bulanan Chart (Line)
    async function loadBulananChart() {
        const response = await fetch(`${chartApiUrl}?type=bulanan${getFilterParams()}`);
        const data = await response.json();

        const ctx = document.getElementById('carta-bulanan');
        if (!ctx) return;

        if (chartBulanan) chartBulanan.destroy();

        chartBulanan = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.namaBulan,
                datasets: [
                    {
                        label: 'Community',
                        data: data.bulananKomuniti,
                        borderColor: '#B0623E',
                        backgroundColor: 'rgba(176, 98, 62, 0.2)',
                        fill: true,
                        tension: 0.35,
                    },
                    {
                        label: 'STEM',
                        data: data.bulananStem,
                        borderColor: '#3E7A5C',
                        backgroundColor: 'rgba(62, 122, 92, 0.15)',
                        fill: false,
                        tension: 0.35,
                    },
                    {
                        label: 'Industri',
                        data: data.bulananIndustri,
                        borderColor: '#6E5AA8',
                        backgroundColor: 'rgba(110, 90, 168, 0.15)',
                        fill: false,
                        tension: 0.35,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    // Load Kategori Chart (Pie)
    async function loadKategoriChart() {
        const response = await fetch(`${chartApiUrl}?type=kategori${getFilterParams()}`);
        const data = await response.json();

        const ctx = document.getElementById('carta-kategori');
        if (!ctx) return;

        if (chartKategori) chartKategori.destroy();

        chartKategori = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labelKategori,
                datasets: [{
                    data: data.dataKategori,
                    backgroundColor: ['#B08A3E', '#1F2A24', '#B0623E', '#6E5AA8', '#3E7A5C']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Get filter parameters from URL
    function getFilterParams() {
        const params = new URLSearchParams(window.location.search);
        let query = '';
        if (params.get('tahun')) query += `&tahun=${params.get('tahun')}`;
        if (params.get('pusat')) query += `&pusat=${params.get('pusat')}`;
        return query ? query : '';
    }

    // Refresh specific chart
    async function refreshChart(type) {
        switch(type) {
            case 'pusat': await loadPusatChart(); break;
            case 'nisbah': await loadNisbahChart(); break;
            case 'bulanan': await loadBulananChart(); break;
            case 'kategori': await loadKategoriChart(); break;
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