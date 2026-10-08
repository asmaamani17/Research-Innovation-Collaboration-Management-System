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
  <header class="px-4 py-6 sm:px-8 sm:py-8">
    <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Dashboard Program Komuniti, STEM &amp; Industri</h1>
    <p class="mt-1 text-sm font-medium leading-5 text-divider-subtle">Overview of community, STEM and industry programmes, participation, and monthly activity.</p>
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
        <div class="mt-2 text-3xl font-bold text-text-main">{{ number_format($total) }}</div>
        <div class="text-xs text-divider-subtle">rekod {{ request('tahun') ? 'tahun ' . request('tahun') : 'semua tahun' }}</div>
      </div>
      <div class="flex shrink-0 items-center justify-center rounded-lg bg-primary/10 p-3 text-primary">
        <span class="material-symbols-outlined text-2xl" aria-hidden="true">bar_chart</span>
      </div>
    </div>
    <div class="group flex items-center justify-between gap-4 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm transition-colors hover:border-primary sm:p-6">
      <div>
        <div class="text-sm font-medium text-divider-subtle">Program STEM</div>
        <div class="mt-2 text-3xl font-bold text-[#3E7A5C]">{{ number_format($stemCount) }}</div>
        <div class="text-xs text-divider-subtle">{{ $stemPct }}% daripada jumlah</div>
      </div>
      <div class="flex shrink-0 items-center justify-center rounded-lg bg-[#3E7A5C]/10 p-3 text-[#3E7A5C]">
        <span class="material-symbols-outlined text-2xl" aria-hidden="true">science</span>
      </div>
    </div>
    <div class="group flex items-center justify-between gap-4 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm transition-colors hover:border-primary sm:p-6">
      <div>
        <div class="text-sm font-medium text-divider-subtle">Program Komuniti</div>
        <div class="mt-2 text-3xl font-bold text-[#B0623E]">{{ number_format($komunitiCount) }}</div>
        <div class="text-xs text-divider-subtle">{{ $komunitiPct }}% daripada jumlah</div>
      </div>
      <div class="flex shrink-0 items-center justify-center rounded-lg bg-[#B0623E]/10 p-3 text-[#B0623E]">
        <span class="material-symbols-outlined text-2xl" aria-hidden="true">groups</span>
      </div>
    </div>
    <div class="group flex items-center justify-between gap-4 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm transition-colors hover:border-primary sm:p-6">
      <div>
        <div class="text-sm font-medium text-divider-subtle">Program Industri</div>
        <div class="mt-2 text-3xl font-bold text-[#6E5AA8]">{{ number_format($industriCount) }}</div>
        <div class="text-xs text-divider-subtle">{{ $industriPct }}% daripada jumlah</div>
      </div>
      <div class="flex shrink-0 items-center justify-center rounded-lg bg-[#6E5AA8]/10 p-3 text-[#6E5AA8]">
        <span class="material-symbols-outlined text-2xl" aria-hidden="true">factory</span>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.4fr_1fr]">
    <div class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
      <div class="mb-1 text-lg font-bold text-text-main">Penghantaran Mengikut Pusat</div>
      <div class="mb-4 text-sm text-divider-subtle">Pecahan STEM vs Komuniti vs Industri bagi setiap pusat</div>
      @if(count($senaraiPusat) > 0)
        <canvas id="carta-pusat" style="max-height: {{ 60 + (count($senaraiPusat) * 32) }}px;"></canvas>
      @else
        <div class="px-5 py-6 text-center text-sm text-divider-subtle">Tiada rekod untuk penapis ini.</div>
      @endif
    </div>
    <div class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
      <div class="mb-1 text-lg font-bold text-text-main">STEM vs Komuniti vs Industri</div>
      <div class="mb-4 text-sm text-divider-subtle">Nisbah keseluruhan klasifikasi program</div>
      <canvas id="carta-nisbah" height="90"></canvas>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.4fr_1fr]">
    <div class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
      <div class="mb-1 text-lg font-bold text-text-main">Trend Bulanan</div>
      <div class="mb-4 text-sm text-divider-subtle">Jumlah program mengikut bulan.</div>
      <canvas id="carta-bulanan" height="90"></canvas>
    </div>
    <div class="min-w-0 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-6">
      <div class="mb-1 text-lg font-bold text-text-main">Kategori Program (KSTP / CSR)</div>
      <div class="mb-4 text-sm text-divider-subtle">Taburan mengikut kategori borang</div>
      <canvas id="carta-kategori" height="90"></canvas>
    </div>
  </div>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
  <script>
    Chart.defaults.font.family = 'Inter, sans-serif';

    const senaraiPusat = @json($senaraiPusat);

    // BARU: format label legend "Nama: Kiraan (X%)" -- dikongsi carta
    // donut & pie. Chart.js panggil ni setiap kali carta di-render.
    function labelDenganPeratus(chart) {
      const dataset = chart.data.datasets[0];
      const jumlah = dataset.data.reduce((a, b) => a + b, 0);
      return chart.data.labels.map((label, i) => {
        const nilai = dataset.data[i];
        const peratus = jumlah > 0 ? ((nilai / jumlah) * 100).toFixed(1) : 0;
        return {
          text: `${label}: ${nilai} (${peratus}%)`,
          fillStyle: dataset.backgroundColor[i],
          index: i,
        };
      });
    }

    @if(count($senaraiPusat) > 0)
    new Chart(document.getElementById('carta-pusat'), {
      type: 'bar',
      data: {
        labels: senaraiPusat,
        datasets: [
          { label: 'STEM',     data: @json($stemSiri),     backgroundColor: '#3E7A5C' },
          { label: 'Komuniti', data: @json($komunitiSiri), backgroundColor: '#B0623E' },
          { label: 'Industri', data: @json($industriSiri), backgroundColor: '#6E5AA8' },
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
    @endif

    new Chart(document.getElementById('carta-nisbah'), {
      type: 'doughnut',
      data: {
        labels: ['STEM', 'Komuniti', 'Industri'],
        datasets: [{
          data: [{{ $stemCount }}, {{ $komunitiCount }}, {{ $industriCount }}],
          backgroundColor: ['#3E7A5C', '#B0623E', '#6E5AA8']
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { generateLabels: labelDenganPeratus } } }
      }
    });

    // Trend Bulanan -- carta kawasan (area chart).
    new Chart(document.getElementById('carta-bulanan'), {
      type: 'line',
      data: {
        labels: @json($namaBulan),
        datasets: [
          {
            label: 'Community',
            data: @json($bulananKomuniti),
            borderColor: '#B0623E',
            backgroundColor: 'rgba(176, 98, 62, 0.2)',
            fill: true,
            tension: 0.35,
          },
          {
            label: 'STEM',
            data: @json($bulananStem),
            borderColor: '#3E7A5C',
            backgroundColor: 'rgba(62, 122, 92, 0.15)',
            fill: false,
            tension: 0.35,
          },
          {
            label: 'Industri',
            data: @json($bulananIndustri),
            borderColor: '#6E5AA8',
            backgroundColor: 'rgba(110, 90, 168, 0.15)',
            fill: false,
            tension: 0.35,
          },
        ]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });

    // Kategori Program (KSTP/CSR).
    new Chart(document.getElementById('carta-kategori'), {
      type: 'pie',
      data: {
        labels: @json($labelKategori),
        datasets: [{
          data: @json($dataKategori),
          backgroundColor: ['#B08A3E', '#1F2A24', '#B0623E', '#6E5AA8', '#3E7A5C']
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { generateLabels: labelDenganPeratus } } }
      }
    });
  </script>
  </div>
@endsection