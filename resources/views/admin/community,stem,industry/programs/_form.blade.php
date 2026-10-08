{{-- Borang ni DIKONGSI antara create.blade.php & edit.blade.php -- elak tulis dua kali --}}

<div class="form-grid">
  <div class="form-field">
    <label>Fakulti / Pusat Tanggungjawab</label>
    <select name="faculty">
      <option value="">- Pilih Pusat Tanggungjawab -</option>
      @foreach($senaraiPusat as $unit)
        <option value="{{ $unit['value'] }}" @selected(old('faculty', $program->faculty) === $unit['value'])>
          {{ $unit['label'] }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="form-field">
    <label>Kategori Program</label>
    <input type="text" name="kategori" value="{{ old('kategori', $program->kategori) }}" placeholder="Cth: KSTP">
  </div>

  <div class="form-field full">
    <label>Nama Program / Aktiviti</label>
    <input type="text" name="program" value="{{ old('program', $program->program) }}" required placeholder="Nama penuh program">
  </div>

  <div class="form-field">
    <label>ID Projek</label>
    <input type="text" name="project_id" id="project_id_input" value="{{ old('project_id', $program->project_id) }}" placeholder="Cth: A01, B13, C13">
    @error('project_id')
      <div style="font-size:12px;color:#b42318;margin-top:4px;">⛔ {{ $message }}</div>
    @enderror
    <div id="amaranProjectId" style="display:none;font-size:12px;color:#b45309;margin-top:4px;line-height:1.4;"></div>
  </div>
  <div class="form-field">
    <label>Staff ID</label>
    <input type="text" name="staff_id" value="{{ old('staff_id', $program->staff_id) }}" required placeholder="Cth: 01111">
  </div>

  <div class="form-field">
    <label>Tarikh Mula</label>
    <input type="date" name="start_date" value="{{ old('start_date', optional($program->start_date)->format('Y-m-d')) }}">
  </div>
  <div class="form-field">
    <label>Tarikh Tamat</label>
    <input type="date" name="end_date" value="{{ old('end_date', optional($program->end_date)->format('Y-m-d')) }}">
  </div>

  <div class="form-field">
    <label>ID Projek/Inovasi Berkaitan (Lepas)</label>
    <input type="text" name="past_research_id" id="past_research_id" value="{{ old('past_research_id', $program->past_research_id) }}" placeholder="Taip 'Tiada' jika tiada">
  </div>
  <div class="form-field">
    <label>Nama Komuniti / Industri</label>
    <input type="text" name="community_industry_name" id="community_industry_name" value="{{ old('community_industry_name', $program->community_industry_name) }}" placeholder="Cth: Kelana Jaya Medical Centre">
  </div>

  <div class="form-field">
    <label>Penganjur / Penaja / Pemfunder / Kolaborator</label>
    <input type="text" name="organiser" id="organiser" value="{{ old('organiser', $program->organiser) }}" placeholder="Taip 'Tiada' jika tiada">
  </div>
  <div class="form-field">
    <label>Nilai Tajaan (RM)</label>
    <input type="text" name="sponsorship_value" id="sponsorship_value" value="{{ old('sponsorship_value', $program->sponsorship_value) }}" placeholder="Cth: 500 atau 'Tiada'">
  </div>

  <div class="form-field full">
    <label>Ringkasan Manfaat</label>
    <textarea name="brief_benefit" id="brief_benefit" rows="3" placeholder="Ringkasan manfaat program/aktiviti ini">{{ old('brief_benefit', $program->brief_benefit) }}</textarea>
  </div>

  <div class="form-field">
    <label>Penilaian Bintang (Manfaat)</label>
    <select name="star_rating" id="star_rating">
      <option value="">- Pilih -</option>
      @for($i = 1; $i <= 5; $i++)
        <option value="{{ $i }}" @selected(old('star_rating', $program->star_rating) == $i)>{{ $i }} Bintang</option>
      @endfor
    </select>
  </div>
  <div class="form-field">
    <label>Klasifikasi</label>
    <select name="classification" required>
      <option value="Community" @selected(old('classification', $program->classification) == 'Community')>Komuniti</option>
      <option value="STEM" @selected(old('classification', $program->classification) == 'STEM')>STEM</option>
      <option value="Industri" @selected(old('classification', $program->classification) == 'Industri')>Industri</option>
    </select>
  </div>

  {{-- BARU (22 Sept 2026): Status pendaftaran DIKUNCI kpd "New" -- dropdown
       manual dibuang sebab bercanggah dgn ciri Senarai Penerima Manfaat.
       Templat MyRA rasmi MESTI ada 1 baris "New" per projek; baris
       "Benefited" kini SEPENUHNYA diuruskan drpd Senarai Penerima Manfaat
       (satu baris per penerima), bukan dropdown ni. Kekalkan sbg hidden
       input (bukan buang terus) supaya value "New" tetap dihantar & tak
       terjejas cara ProgramController proses data borang. --}}
  <input type="hidden" name="status" value="New">
  <div class="form-field full">
    <label>Link Bukti (Pautan Luar, cth Google Drive — pilihan)</label>
    <input type="text" name="link_evidence" value="{{ old('link_evidence', $program->link_evidence) }}" placeholder="https://drive.google.com/...">
  </div>

  {{-- BAHARU: pautan Google Drive berasingan untuk Borang B/C/D. Staf
       upload fail SENDIRI ke Google Drive dulu (elak isu kuota storan
       Service Account -- lihat nota web.php), lepas tu paste pautan
       "boleh lihat" di sini. Padan terus dgn lajur Sheet:
       "Muatnaik Borang B (format pdf)", "Muatnaik Kelulusan Borang C
       (format pdf)", "Muatnaik Kelulusan Borang D (format pdf)". --}}
  <div class="form-field">
    <label>Pautan Borang B (Google Drive)</label>
    <input type="text" name="link_borang_b" value="{{ old('link_borang_b', $program->link_borang_b) }}" placeholder="https://drive.google.com/...">
  </div>
  <div class="form-field">
    <label>Pautan Borang C / Kelulusan (Google Drive)</label>
    <input type="text" name="link_borang_c" value="{{ old('link_borang_c', $program->link_borang_c) }}" placeholder="https://drive.google.com/...">
  </div>
  <div class="form-field">
    <label>Pautan Borang D / Kelulusan (Google Drive)</label>
    <input type="text" name="link_borang_d" value="{{ old('link_borang_d', $program->link_borang_d) }}" placeholder="https://drive.google.com/...">
  </div>

  <div class="form-field">
    <label>Pautan Borang A (Google Drive)</label>
    <input type="text" name="link_borang_a" value="{{ old('link_borang_a', $program->link_borang_a) }}" placeholder="https://drive.google.com/...">
    <div class="content-description" style="font-size:11px;margin-top:4px;">
      Untuk masuk Folder Gabungan Evidence -- berasingan dari OCR (upload fail bawah cuma untuk auto-isi, tak disimpan).
    </div>
  </div>
  <div class="form-field">
    <label>Muat Naik Fail PDF Borang A</label>
    <input type="file" name="evidence_file" id="evidence_file_input" accept="application/pdf">
    @if($program->evidence_file)
      <div style="font-size:12px;color:var(--stem);margin-top:5px;">
        Fail sedia ada: <a href="{{ asset('storage/' . $program->evidence_file) }}" target="_blank">Lihat PDF semasa</a>
        (muat naik fail baharu untuk gantikan)
      </div>
    @endif
  </div>

  <div class="form-field full" style="background:var(--paper);border:1px solid var(--line);border-radius:10px;padding:12px 14px;">
    <div class="content-description" style="font-size:12px;">
      PDF Borang A boleh disimpan sebagai evidence. Auto-fill/OCR is not enabled in RICM yet; enter the program details manually.
    </div>
  </div>

  <div class="form-field full" style="background:var(--paper);border:1px solid var(--line);border-radius:10px;padding:12px 14px;">
    <label>Senarai Penerima Manfaat (Sekolah/Organisasi)</label>
    <div class="content-description" style="font-size:11px;margin-bottom:10px;line-height:1.4;">
      Senaraikan sekolah/organisasi yang BENAR-BENAR terlibat/dapat manfaat (biasanya diisi SELEPAS program berjalan). Setiap satu akan jadi SATU baris "Benefited" berasingan dalam Laporan Templat MyRA.
    </div>
    <div id="senarai-penerima-manfaat">
      @forelse(($program->exists ? $program->beneficiaries : collect()) as $penerima)
        <div class="baris-penerima-manfaat" style="display:flex;gap:8px;margin-bottom:8px;">
          <input type="text" name="beneficiaries[]" value="{{ $penerima->name }}" placeholder="Cth: SMK Hang Kasturi" style="flex:1;">
          <button type="button" class="btn secondary btn-buang-penerima">Buang</button>
        </div>
      @empty
      @endforelse
    </div>
    <button type="button" class="btn secondary" id="btnTambahPenerima">+ Tambah Penerima Manfaat</button>
  </div>

  <div class="form-field full">
    <label>Catatan / Remarks</label>
    <textarea name="remarks" id="remarks" rows="2" placeholder="Taip 'Tiada' jika tiada">{{ old('remarks', $program->remarks) }}</textarea>
  </div>
</div>

<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:22px;padding-top:18px;border-top:1px solid var(--line);">
  <a href="{{ route('admin.stem.programs.index') }}" class="btn secondary">Batal</a>
  <button type="submit" class="btn">Simpan Rekod</button>
</div>

<script>
// Amaran (bukan sekatan) -- semak sama ada huruf pertama ID Projek padan
// dgn klasifikasi yg dipilih (Community=C, STEM=B, Industri=A, ikut
// ProjectIdGenerator.php). ID Projek TAK auto-tukar bila klasifikasi
// diedit (ID dianggap kekal -- tukar automatik boleh pecahkan rujukan
// sedia ada macam folder Google Drive/laporan lama) -- ni SEKADAR
// makluman visual supaya staf sedar & boleh betulkan manual jika perlu.
(function () {
  const prefixMap = { Community: 'C', STEM: 'B', Industri: 'A' };
  const projectIdInput = document.getElementById('project_id_input');
  const classificationSelect = document.querySelector('[name="classification"]');
  const amaranEl = document.getElementById('amaranProjectId');

  if (!projectIdInput || !classificationSelect || !amaranEl) return;

  function semakPadanan() {
    const projectId = (projectIdInput.value || '').trim().toUpperCase();
    const classification = classificationSelect.value;

    if (!projectId || !classification) {
      amaranEl.style.display = 'none';
      return;
    }

    const hurufSepatutnya = prefixMap[classification];
    const hurufSemasa = projectId.charAt(0);

    if (hurufSepatutnya && hurufSemasa !== hurufSepatutnya) {
      amaranEl.style.display = 'block';
      amaranEl.textContent =
        `⚠️ ID Projek "${projectId}" bermula huruf "${hurufSemasa}", tapi klasifikasi "${classification}" biasanya guna huruf "${hurufSepatutnya}". ` +
        `Ini TIDAK menghalang simpan -- ID Projek TAK auto-tukar (dianggap kekal). Semak & betulkan manual jika perlu.`;
    } else {
      amaranEl.style.display = 'none';
    }
  }

  classificationSelect.addEventListener('change', semakPadanan);
  projectIdInput.addEventListener('input', semakPadanan);
  semakPadanan(); // semak sekali bila borang dibuka (penting utk Edit rekod sedia ada)
})();
</script>

<script>
// BARU (21 Sept 2026): tambah/buang baris Penerima Manfaat secara dinamik.
// Vanilla JS -- tiada dependency tambahan, padan corak sedia ada dlm borang ni.
document.getElementById('btnTambahPenerima').addEventListener('click', function () {
  const container = document.getElementById('senarai-penerima-manfaat');
  const baris = document.createElement('div');
  baris.className = 'baris-penerima-manfaat';
  baris.style.cssText = 'display:flex;gap:8px;margin-bottom:8px;';
  baris.innerHTML = `
    <input type="text" name="beneficiaries[]" placeholder="Cth: SMK Hang Kasturi" style="flex:1;">
    <button type="button" class="btn secondary btn-buang-penerima">Buang</button>
  `;
  container.appendChild(baris);
});

document.getElementById('senarai-penerima-manfaat').addEventListener('click', function (e) {
  if (e.target.classList.contains('btn-buang-penerima')) {
    e.target.closest('.baris-penerima-manfaat').remove();
  }
});
</script>
