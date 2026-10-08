@extends('layouts.admin')

@section('title', 'Community, STEM & Industry Records')
@section('content')
  @include('admin.community,stem,industry.programs._styles')
  <div class="community-programs">
  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="error-list">
      <strong>Please review the following errors:</strong>
      <ul style="margin:6px 0 0;padding-left:18px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @php
    $isPublic = false;
    $routeIndex = 'admin.stem.programs.index';
  @endphp
  <style>
    /* --- Tambahan gaya untuk Senarai Rekod (guna semula palet CSS var sedia ada) --- */
    .chip {
      display:inline-block;background:var(--paper);border:1px solid var(--line);
      border-radius:999px;padding:3px 10px;font-size:12px;color:var(--ink-soft);
      margin:2px 4px 2px 0;
    }
    .chip-removable { text-decoration:none;cursor:pointer;transition:background .15s; }
    .chip-removable:hover { background:#fdeaea;color:#b42318;border-color:#f3c6c2; }
    .id-badge {
      display:inline-block;padding:3px 10px;border:1px solid var(--line);border-radius:6px;
      background:var(--paper);font-weight:700;font-size:13px;
    }
    .sort-link { color:inherit;text-decoration:none; }
    .sort-icon { opacity:.45;font-size:11px;margin-left:2px; }
    .sort-link:hover .sort-icon { opacity:.9; }
    details.evidence-dropdown, details.action-menu, details.report-dropdown { position:relative; }
    details.evidence-dropdown summary, details.action-menu summary, details.report-dropdown summary { list-style:none;cursor:pointer; }
    details.evidence-dropdown summary::-webkit-details-marker,
    details.action-menu summary::-webkit-details-marker,
    details.report-dropdown summary::-webkit-details-marker { display:none; }
    details.report-dropdown { display:inline-block; }
    details.report-dropdown .dropdown-panel { left:0;right:auto;min-width:200px; }
    .evidence-badge {
      display:inline-flex;align-items:center;gap:4px;font-size:12px;
      padding:3px 9px;border:1px solid var(--line);border-radius:999px;
      background:var(--paper);color:var(--ink);
    }
    .action-trigger {
      display:inline-flex;align-items:center;justify-content:center;
      width:28px;height:28px;border:1px solid var(--line);border-radius:6px;
      background:var(--paper);font-weight:700;
    }
    .dropdown-panel {
      position:absolute;top:calc(100% + 4px);right:0;background:#fff;border:1px solid var(--line);
      border-radius:8px;padding:8px;box-shadow:0 4px 14px rgba(0,0,0,0.1);z-index:20;
      white-space:nowrap;display:flex;flex-direction:column;gap:2px;min-width:140px;
    }
    .dropdown-panel a, .dropdown-panel button {
      display:block;text-align:left;font-size:12px;color:var(--ink);text-decoration:none;
      background:none;border:none;padding:4px 6px;border-radius:4px;cursor:pointer;width:100%;
    }
    .dropdown-panel a:hover, .dropdown-panel button:hover { background:var(--paper); }
    .dropdown-panel button.danger { color:#b42318; }

    .pager { margin-top:16px;display:flex;flex-direction:column;gap:8px;align-items:center; }
    .pager-links { display:flex;flex-wrap:wrap;gap:4px;justify-content:center; }
    .pager-btn {
      display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;
      padding:0 8px;border:1px solid var(--line);border-radius:6px;background:var(--paper);
      color:var(--ink);font-size:13px;text-decoration:none;box-sizing:border-box;
    }
    a.pager-btn:hover { background:var(--gold);color:#fff;border-color:var(--gold); }
    .pager-btn.pager-active { background:var(--ink);color:#fff;border-color:var(--ink);font-weight:700; }
    .pager-btn.pager-disabled { color:var(--ink-soft);opacity:.5;cursor:default; }
    .pager-summary { font-size:12px;color:var(--ink-soft); }
    .program-actions { display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap; }
    .program-actions .btn, .program-actions .report-trigger {
      display:inline-flex;align-items:center;justify-content:center;gap:6px;
    }
    .program-actions .material-symbols-outlined, .dropdown-panel .material-symbols-outlined {
      font-size:18px;line-height:1;
    }
    .dropdown-panel a, .dropdown-panel button { display:flex;align-items:center;gap:7px; }
    .duplicate-modal {
      position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;
      padding:20px;background:rgb(15 23 42 / 55%);
    }
    .duplicate-modal[hidden] { display:none; }
    .duplicate-modal-card {
      width:min(680px,100%);max-height:85vh;overflow:auto;padding:24px;
      border:1px solid var(--line);border-radius:var(--radius);background:var(--paper-raised);
      box-shadow:0 20px 60px rgb(0 0 0 / 20%);
    }
    .duplicate-group { padding:12px 0;border-bottom:1px solid var(--line);font-size:13px;line-height:1.6; }
    .duplicate-group:last-child { border-bottom:0; }
    .duplicate-error { margin-top:12px;color:var(--danger);font-size:13px; }
    @media(max-width:640px) { .program-actions { justify-content:flex-start; } }
  </style>

  <div class="top-bar">
    <div>
      <h1 class="page-title">Senarai Rekod</h1>
      <p class="page-description">Browse, search, and manage Community, STEM, and Industry program records.</p>
    </div>
    <div class="program-actions">
      <button type="button" class="btn secondary" title="Reload records from the RICM database" onclick="window.location.reload()">
        <span class="material-symbols-outlined" aria-hidden="true">refresh</span>
        Tarik Data Terkini
      </button>
      <button type="button" class="btn secondary" id="btnBuangPendua"
              data-preview-url="{{ route('admin.stem.programs.duplicates.preview') }}">
        <span class="material-symbols-outlined" aria-hidden="true">delete_sweep</span>
        Buang Pendua
      </button>
      <details class="report-dropdown">
        <summary class="btn secondary report-trigger">
          <span class="material-symbols-outlined" aria-hidden="true">print</span>
          Cetak Laporan
          <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
        </summary>
        <div class="dropdown-panel">
          <a href="{{ route('admin.stem.reports.myra') }}">
            <span class="material-symbols-outlined" aria-hidden="true">description</span>
            Laporan Templat MyRA
          </a>
          <a href="{{ route('admin.stem.reports.overall') }}">
            <span class="material-symbols-outlined" aria-hidden="true">analytics</span>
            Laporan Keseluruhan
          </a>
        </div>
      </details>
      <a href="{{ route('admin.stem.programs.create') }}" class="btn">
        <span class="material-symbols-outlined" aria-hidden="true">add</span>
        Add Program Record
      </a>
    </div>
  </div>

  


  {{-- Bar Penapis -- GET supaya boleh dikongsi/bookmark, dan senang di-reset --}}
  <div class="card filter-card">
    <form method="GET" action="{{ route($routeIndex) }}">
      {{-- Kekalkan sort semasa bila penapis ditukar --}}
      <input type="hidden" name="sort" value="{{ request('sort') }}">
      <input type="hidden" name="dir" value="{{ request('dir') }}">
      <div style="display:grid;grid-template-columns:repeat(4, 1fr) 2fr auto;gap:12px;align-items:end;">
        <div class="form-field" style="margin-bottom:0;">
          <label>Tahun</label>
          <select name="tahun" onchange="this.form.submit()">
            <option value="">Semua Tahun</option>
            @foreach($senaraiTahun as $tahun)
              <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>{{ $tahun }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-field" style="margin-bottom:0;">
          <label>Pusat Tanggungjawab</label>
          <select name="pusat" onchange="this.form.submit()">
            <option value="">Semua Pusat</option>
            @foreach($senaraiPusat as $pusat)
              <option value="{{ $pusat }}" @selected(request('pusat') == $pusat)>{{ $pusat }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-field" style="margin-bottom:0;">
          <label>Klasifikasi</label>
          <select name="klasifikasi" onchange="this.form.submit()">
            <option value="">Semua</option>
            <option value="Community" @selected(request('klasifikasi') == 'Community')>Komuniti</option>
            <option value="STEM" @selected(request('klasifikasi') == 'STEM')>STEM</option>
            <option value="Industri" @selected(request('klasifikasi') == 'Industri')>Industri</option>
          </select>
        </div>
        <div class="form-field" style="margin-bottom:0;">
          <label>Kategori Program</label>
          <select name="kategori" onchange="this.form.submit()">
            <option value="">Semua</option>
            @foreach($senaraiKategori as $kategori)
              <option value="{{ $kategori }}" @selected(request('kategori') == $kategori)>{{ $kategori }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-field" style="margin-bottom:0;">
          <label>Cari Program</label>
          <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Taip untuk cari...">
        </div>
        <div>
          <a href="{{ route($routeIndex) }}" class="btn secondary">Reset</a>
        </div>
      </div>
    </form>

    {{-- Chip penapis aktif --}}
    @php
      $chipLabels = [
        'tahun'       => 'Tahun: ' . request('tahun'),
        'pusat'       => 'Pusat: ' . request('pusat'),
        'klasifikasi' => 'Klasifikasi: ' . request('klasifikasi'),
        'kategori'    => 'Kategori: ' . request('kategori'),
        'cari'        => 'Cari: "' . request('cari') . '"',
      ];
      $activeChips = collect($chipLabels)->filter(fn ($label, $key) => request()->filled($key));
    @endphp
    <div style="margin-top:14px;font-size:12px;color:var(--ink-soft);">
      <strong style="color:var(--ink);">Penapis aktif:</strong>
      @if($activeChips->isEmpty())
        <span class="chip">Tiada penapis digunakan</span>
      @else
        @foreach($activeChips as $key => $label)
          <a href="{{ route($routeIndex, request()->except([$key, 'page'])) }}"
             class="chip chip-removable" title="Buang penapis ini">{{ $label }} ✕</a>
        @endforeach
        <a href="{{ route($routeIndex) }}" style="margin-left:6px;font-size:12px;text-decoration:underline;color:var(--ink-soft);">Reset semua</a>
      @endif
    </div>
  </div>

  <div class="card records-card">
    <div class="records-toolbar">
      <div style="font-size:12px;color:var(--ink-soft);">
        {{ $programs->total() }} rekod dijumpai
      </div>
      <form method="GET" action="{{ route($routeIndex) }}" style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--ink-soft);">
        @foreach(request()->except(['per_page', 'page']) as $key => $value)
          <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        Papar:
        <select name="per_page" onchange="this.form.submit()">
          @foreach([10, 25, 50, 100] as $opt)
            <option value="{{ $opt }}" @selected((int) request('per_page', 10) === $opt)>{{ $opt }}</option>
          @endforeach
        </select>
        rekod setiap halaman
      </form>
    </div>

    @php
      // Bina URL sort untuk satu lajur -- toggle asc/desc, kekalkan penapis semasa.
      $sortInfo = function (string $column) use ($routeIndex) {
          $isActive = request('sort') === $column;
          $nextDir  = ($isActive && request('dir', 'desc') === 'asc') ? 'desc' : 'asc';
          $icon     = $isActive ? (request('dir', 'desc') === 'asc' ? '↑' : '↓') : '↕';
          $url      = route($routeIndex, array_merge(
              request()->except(['sort', 'dir', 'page']),
              ['sort' => $column, 'dir' => $nextDir]
          ));
          return ['url' => $url, 'icon' => $icon];
      };
    @endphp

    <div class="records-table-wrap">
    <table class="records-table">
      <thead>
        <tr>
          @foreach([
              'submitted_at'   => 'Tarikh Dihantar',
              'start_date'     => 'Tarikh Mula',
              'project_id'     => 'ID Projek',
              'faculty'        => 'Pusat Tanggungjawab',
              'program'        => 'Program',
              'kategori'       => 'Kategori',
              'classification' => 'Klasifikasi',
          ] as $column => $label)
            @php
              $s = $sortInfo($column);
            @endphp
            <th><a href="{{ $s['url'] }}" class="sort-link">{{ $label }} <span class="sort-icon">{{ $s['icon'] }}</span></a></th>
          @endforeach
          <th>Evidence</th>
          @unless($isPublic)
            <th>Tindakan</th>
          @endunless
        </tr>
      </thead>
      <tbody>
        @forelse($programs as $p)
          <tr>
            <td>{{ optional($p->submitted_at)->format('d-m-Y H:i') ?? '-' }}</td>
            <td>{{ optional($p->start_date)->format('Y-m-d') ?? '-' }}</td>
            <td><span class="id-badge">{{ $p->project_id ?? '-' }}</span></td>
            <td>{{ $p->faculty_singkat ?: '-' }}</td>
            <td>{{ $p->program }}</td>
            <td>{{ $p->kategori ?? '-' }}</td>
            <td><span class="badge {{ strtolower($p->classification) }}">{{ $p->classification }}</span></td>
            <td>
              @php
                $senaraiLink = collect([
                    'Borang A'               => $p->link_borang_a,
                    'Borang B'               => $p->link_borang_b,
                    'Borang C'               => $p->link_borang_c,
                    'Borang D'               => $p->link_borang_d,
                    'Kelulusan Kertas Kerja' => $p->link_kelulusan_kertas_kerja,
                    'Folder Gabungan (lama)' => $p->link_evidence,
                    'Folder Gabungan'        => $p->combined_evidence_folder,
                ])->filter();
              @endphp
              @if($senaraiLink->isNotEmpty())
                <details class="evidence-dropdown">
                  <summary class="evidence-badge">📎 {{ $senaraiLink->count() }}</summary>
                  <div class="dropdown-panel">
                    @foreach($senaraiLink as $label => $url)
                      <a href="{{ $url }}" target="_blank" rel="noopener">{{ $label }}</a>
                    @endforeach
                  </div>
                </details>
              @else
                <span style="color:var(--ink-soft);font-size:12px;">—</span>
              @endif
            </td>
            @unless($isPublic)
              <td>
                <details class="action-menu">
                  <summary class="action-trigger" title="Tindakan">⋮</summary>
                  <div class="dropdown-panel">
                    <a href="{{ route('admin.stem.programs.edit', $p) }}">
                      <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                      Edit
                    </a>
                    <form action="{{ route('admin.stem.programs.destroy', $p) }}" method="POST"
                          onsubmit="return confirm('Padam rekod ini? Tindakan ini tidak boleh dibatalkan.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="danger">
                        <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                        Padam
                      </button>
                    </form>
                  </div>
                </details>
              </td>
            @endunless
          </tr>
        @empty
          <tr>
            <td colspan="{{ $isPublic ? 8 : 9 }}" style="text-align:center;color:var(--ink-soft);padding:24px;">
              Tiada rekod sepadan dengan penapis semasa.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
    </div>

    {{-- Pagination custom -- projek ni TAK guna Tailwind, jadi ELAK
         $programs->links() bawaan Laravel (ia bergantung penuh pada class
         Tailwind untuk saiz/responsive; tanpa Tailwind, ikon SVG previous/
         next terpapar dalam saiz asal yang besar & bahagian mobile+desktop
         terpapar serentak). Bina sendiri guna CSS .pager di atas. --}}
    @if ($programs->hasPages())
      @php
        $currentPage = $programs->currentPage();
        $lastPage    = $programs->lastPage();
        $delta       = 2; // berapa nombor halaman ditunjuk di kiri/kanan halaman semasa
        $rangeStart  = max(1, $currentPage - $delta);
        $rangeEnd    = min($lastPage, $currentPage + $delta);
      @endphp
      <nav class="pager" aria-label="Pagination">
        <div class="pager-links">
          @if ($programs->onFirstPage())
            <span class="pager-btn pager-disabled">‹ Sebelum</span>
          @else
            <a href="{{ $programs->previousPageUrl() }}" class="pager-btn">‹ Sebelum</a>
          @endif

          {{-- Halaman 1 sentiasa ditunjuk --}}
          @if ($rangeStart > 1)
            <a href="{{ $programs->url(1) }}" class="pager-btn">1</a>
            @if ($rangeStart > 2)
              <span class="pager-btn pager-disabled">…</span>
            @endif
          @endif

          @for ($page = $rangeStart; $page <= $rangeEnd; $page++)
            @if ($page == $currentPage)
              <span class="pager-btn pager-active">{{ $page }}</span>
            @else
              <a href="{{ $programs->url($page) }}" class="pager-btn">{{ $page }}</a>
            @endif
          @endfor

          {{-- Halaman terakhir sentiasa ditunjuk --}}
          @if ($rangeEnd < $lastPage)
            @if ($rangeEnd < $lastPage - 1)
              <span class="pager-btn pager-disabled">…</span>
            @endif
            <a href="{{ $programs->url($lastPage) }}" class="pager-btn">{{ $lastPage }}</a>
          @endif

          @if ($programs->hasMorePages())
            <a href="{{ $programs->nextPageUrl() }}" class="pager-btn">Seterusnya ›</a>
          @else
            <span class="pager-btn pager-disabled">Seterusnya ›</span>
          @endif
        </div>
        <div class="pager-summary">
          Menunjukkan {{ $programs->firstItem() ?? 0 }} hingga {{ $programs->lastItem() ?? 0 }} drpd {{ $programs->total() }} rekod
        </div>
      </nav>
    @endif
  </div>

  <div class="duplicate-modal" id="duplicateModal" role="dialog" aria-modal="true"
       aria-labelledby="duplicateModalTitle" hidden>
    <div class="duplicate-modal-card">
      <div class="top-bar">
        <h1 id="duplicateModalTitle">Duplicate Program Records</h1>
        <button type="button" class="btn secondary" id="closeDuplicateModal" aria-label="Close">Close</button>
      </div>
      <p id="duplicateModalSummary" style="color:var(--ink-soft);font-size:14px;"></p>
      <div id="duplicateGroups"></div>
      <div class="duplicate-error" id="duplicateError" role="alert" hidden></div>
      <form method="POST" action="{{ route('admin.stem.programs.duplicates.remove') }}"
            id="removeDuplicatesForm" onsubmit="return confirm('Remove the duplicate records shown above? This cannot be undone.');" hidden>
        @csrf
        <div id="duplicateIds"></div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:18px;">
          <button type="button" class="btn secondary" id="cancelDuplicateRemoval">Cancel</button>
          <button type="submit" class="btn danger">Remove Duplicates</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    (() => {
      const openButton = document.getElementById('btnBuangPendua');
      const modal = document.getElementById('duplicateModal');
      const groupsContainer = document.getElementById('duplicateGroups');
      const idsContainer = document.getElementById('duplicateIds');
      const summary = document.getElementById('duplicateModalSummary');
      const error = document.getElementById('duplicateError');
      const removeForm = document.getElementById('removeDuplicatesForm');

      function closeModal() {
        modal.hidden = true;
      }

      document.getElementById('closeDuplicateModal').addEventListener('click', closeModal);
      document.getElementById('cancelDuplicateRemoval').addEventListener('click', closeModal);

      openButton.addEventListener('click', async () => {
        openButton.disabled = true;
        error.hidden = true;
        groupsContainer.replaceChildren();
        idsContainer.replaceChildren();
        removeForm.hidden = true;
        summary.textContent = 'Checking the current RICM database for duplicate submissions...';
        modal.hidden = false;

        try {
          const response = await fetch(openButton.dataset.previewUrl, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
          });
          if (!response.ok) throw new Error(`Could not check for duplicates (HTTP ${response.status}).`);

          const result = await response.json();
          const groups = result.groups || [];
          if (groups.length === 0) {
            summary.textContent = 'No duplicate groups were found in the RICM database.';
            return;
          }

          summary.textContent = `${groups.length} duplicate group(s) found. The most complete record in each group will be kept.`;
          groups.forEach((group) => {
            const item = document.createElement('div');
            item.className = 'duplicate-group';
            const kept = document.createElement('div');
            kept.textContent = `Keep: ${group.keep.project_id || '(no project ID)'} — ${group.keep.program || '(untitled)'}`;
            item.appendChild(kept);

            group.remove.forEach((record) => {
              const duplicate = document.createElement('div');
              duplicate.textContent = `Remove: ${record.project_id || '(no project ID)'} — ${record.program || '(untitled)'}`;
              item.appendChild(duplicate);

              const idInput = document.createElement('input');
              idInput.type = 'hidden';
              idInput.name = 'duplicate_ids[]';
              idInput.value = record.id;
              idsContainer.appendChild(idInput);
            });

            groupsContainer.appendChild(item);
          });
          removeForm.hidden = false;
        } catch (requestError) {
          summary.textContent = '';
          error.textContent = requestError.message || 'Unable to check for duplicates.';
          error.hidden = false;
        } finally {
          openButton.disabled = false;
        }
      });
    })();
  </script>

  </div>
@endsection