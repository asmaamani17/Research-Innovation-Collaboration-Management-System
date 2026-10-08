@extends('admin.community,stem,industry.layout')

@section('content')
  <style>
    .panduan-section {
      background:var(--paper-raised);border:1px solid var(--line);border-radius:var(--radius);
      padding:24px;margin-bottom:18px;
    }
    .panduan-section h2 {
      font-family:'Newsreader',serif;font-size:20px;margin:0 0 14px;
      display:flex;align-items:center;gap:8px;
    }
    .panduan-section h3 {
      font-size:14px;margin:18px 0 8px;color:var(--ink);
    }
    .panduan-section p, .panduan-section li {
      font-size:13.5px;line-height:1.6;color:var(--ink);
    }
    .panduan-section ul, .panduan-section ol {
      margin:6px 0;padding-left:22px;
    }
    .panduan-nota {
      background:var(--paper);border-left:3px solid var(--gold);border-radius:6px;
      padding:10px 14px;margin:10px 0;font-size:13px;color:var(--ink-soft);
    }
    .panduan-toc {
      display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;
    }
    .panduan-toc a {
      font-size:12.5px;padding:6px 12px;background:var(--paper);border:1px solid var(--line);
      border-radius:999px;color:var(--ink);text-decoration:none;
    }
    .panduan-toc a:hover { background:var(--gold);color:#fff;border-color:var(--gold); }
    .kbd {
      display:inline-block;padding:1px 6px;border:1px solid var(--line);border-radius:4px;
      background:var(--paper);font-family:monospace;font-size:12px;
    }
  </style>

  <div class="top-bar">
    <h1>📖 Panduan Penggunaan Sistem</h1>
    <a href="{{ route('dashboard.index') }}" class="btn secondary">← Balik ke Dashboard</a>
  </div>

  <div class="panduan-toc">
    <a href="#gambaran">Gambaran Keseluruhan</a>
    <a href="#dashboard">Dashboard</a>
    <a href="#senarai-rekod">Senarai Rekod</a>
    <a href="#tambah-edit">Tambah / Edit Rekod</a>
    <a href="#ocr">Auto-Isi OCR</a>
    <a href="#ocr-pukal">OCR Pukal</a>
    <a href="#evidence">Gabung Evidence</a>
    <a href="#sync">Sync Google Sheet</a>
    <a href="#laporan">Cetak Laporan</a>
    <a href="#soalan-lazim">Soalan Lazim</a>
  </div>

  <div class="panduan-section" id="gambaran">
    <h2>🗺️ Gambaran Keseluruhan</h2>
    <p>
      Sistem ni menguruskan rekod program Komuniti/STEM/Industri (KSTP/CSR) UTeM.
      Data datang dari <strong>dua sumber</strong> yang saling berkait:
    </p>
    <ol>
      <li><strong>Borang A (Pendaftaran)</strong> — diuruskan berasingan (Google Form/Sheet), <em>di luar</em> dashboard ni.</li>
      <li><strong>Borang B, C, D (Laporan)</strong> — ini yang dashboard ni uruskan. Data masuk secara <strong>automatik</strong> dari Google Sheet, atau boleh ditambah <strong>manual</strong> terus di sini.</li>
    </ol>
    <div class="panduan-nota">
      💡 Sistem ni <strong>dua-hala</strong> — apa-apa yang awak tambah/edit/padam di dashboard akan refleks ke Google Sheet secara automatik, dan sebaliknya.
    </div>
  </div>

  <div class="panduan-section" id="dashboard">
    <h2>📊 Dashboard</h2>
    <p>Halaman utama papar ringkasan keseluruhan data dalam bentuk carta:</p>
    <ul>
      <li><strong>Kad ringkasan</strong> — jumlah penghantaran, pecahan STEM/Komuniti/Industri</li>
      <li><strong>Carta "Penghantaran Mengikut Pusat"</strong> — bandingkan setiap Fakulti/PTJ</li>
      <li><strong>Carta "Trend Bulanan"</strong> — lihat corak penghantaran sepanjang tahun</li>
      <li><strong>Penapis Tahun & Pusat Tanggungjawab</strong> — di bahagian atas, guna untuk fokus data tertentu</li>
    </ul>
  </div>

  <div class="panduan-section" id="senarai-rekod">
    <h2>📋 Senarai Rekod</h2>
    <p>Ini jadual utama semua rekod program. Beberapa ciri penting:</p>
    <h3>Cari & Tapis</h3>
    <ul>
      <li>Kotak <strong>"Cari Program"</strong> — cari ikut nama program</li>
      <li>Penapis <strong>Tahun / Pusat / Klasifikasi / Kategori</strong> — boleh gabung beberapa sekali</li>
      <li>Chip <strong>"Penapis aktif"</strong> di bawah — klik ✕ untuk buang satu penapis sahaja</li>
    </ul>
    <h3>Susun Lajur</h3>
    <p>Klik mana-mana tajuk lajur (ada ikon ↕) untuk susun ikut lajur tu — klik sekali lagi untuk tukar arah (menaik/menurun).</p>
    <h3>Lajur Evidence (📎)</h3>
    <p>Nombor tu tunjuk berapa banyak fail/pautan evidence rekod tu ada. Klik untuk buka dropdown — pautan Borang A/B/C/D, Kelulusan, dan Folder Gabungan (kalau dah pernah digabungkan).</p>
    <h3>Menu Tindakan (⋮)</h3>
    <ul>
      <li><strong>✏️ Edit</strong> — kemaskini rekod</li>
      <li><strong>🔗 Gabung Evidence</strong> — kumpulkan semua fail evidence rekod ni ke SATU folder Google Drive (lihat bahagian "Gabung Evidence" di bawah)</li>
      <li><strong>🗑️ Padam</strong> — padam rekod (turut padam baris sepadan di Google Sheet secara automatik)</li>
    </ul>
  </div>

  <div class="panduan-section" id="tambah-edit">
    <h2>➕ Tambah / Edit Rekod</h2>
    <p>Guna borang ni untuk masukkan rekod Borang B/C/D <strong>secara manual</strong> (kalau bukan datang dari Google Form).</p>
    <div class="panduan-nota">
      ⚠️ <strong>ID Projek</strong> ada peraturan huruf: Community = <strong>C</strong>, STEM = <strong>B</strong>, Industri = <strong>A</strong>.
      Kalau isi manual dan huruf tak sepadan dengan Klasifikasi, sistem akan tunjuk amaran (bukan sekatan) —
      boleh biarkan kosong untuk sistem jana automatik.
    </div>
    <p><strong>Pautan Borang A/B/C/D</strong> — kalau staf dah upload fail ke Google Drive sendiri, paste pautan tu di medan berkenaan supaya ia boleh dimasukkan ke Folder Gabungan Evidence nanti.</p>
  </div>

  <div class="panduan-section" id="ocr">
    <h2>🗂️ Auto-Isi dari PDF (OCR)</h2>
    <p>
      Kalau ada fail PDF Borang A, boleh muat naik di borang Tambah/Edit dan klik
      <strong>"Auto-Isi dari PDF"</strong> — sistem akan cuba baca & isi automatik:
      Nama Program, Tarikh Mula/Tamat, Ringkasan Manfaat, Nama Komuniti/Industri, Nilai Tajaan.
    </p>
    <div class="panduan-nota">
      💡 OCR bukan 100% tepat — <strong>SILA SEMAK</strong> selalu medan yang diisi automatik sebelum simpan.
      Anggaran 10-20% rekod akan perlukan pembetulan manual — ini had semula jadi teknologi OCR, bukan bug.
    </div>
  </div>

  <div class="panduan-section" id="ocr-pukal">
    <h2>⚙️ OCR Pukal</h2>
    <p>
      Kalau ada BANYAK rekod (dari sync Google Sheet) yang "Ringkasan Manfaat" masih kosong,
      guna ciri ni untuk proses OCR SEMUA sekali gus (bukan satu-satu manual).
    </p>
    <ol>
      <li>Klik <strong>"🗂️ OCR Pukal"</strong> di toolbar Senarai Rekod</li>
      <li>Klik <strong>"▶️ Proses Semua"</strong></li>
      <li><strong>JANGAN tutup tab</strong> sepanjang proses (boleh ambil masa beberapa minit ikut jumlah rekod)</li>
      <li>Kalau tertutup separuh jalan, buka semula page — rekod yang dah siap TAK akan diproses ulang</li>
    </ol>
  </div>

  <div class="panduan-section" id="evidence">
    <h2>🔗 Gabung Evidence</h2>
    <p>
      Fungsi ni kumpulkan SEMUA fail evidence (Borang A/B/C/D + Kelulusan) untuk satu rekod
      ke dalam SATU folder Google Drive, guna <strong>pautan pintasan (shortcut)</strong> —
      fail asal kekal di lokasi asal, cuma dipautkan ke satu tempat untuk senang rujuk.
    </p>
    <div class="panduan-nota">
      💡 Proses ni jalan di latar belakang (queue) — mungkin ambil beberapa saat. Refresh page
      lepas seminit untuk lihat pautan "Folder Gabungan" muncul di dropdown Evidence.
    </div>
  </div>

  <div class="panduan-section" id="sync">
    <h2>🔄 Sync Google Sheet</h2>
    <p>Data dari Google Sheet masuk ke dashboard secara <strong>automatik setiap 15 minit</strong> — admin TAK perlu buat apa-apa.</p>
    <p>Kalau nak update SEGERA (tak nak tunggu 15 minit), klik butang <strong>"🔄 Tarik Data Terkini"</strong> di toolbar Senarai Rekod.</p>
    <h3>Apa yang sync buat?</h3>
    <ul>
      <li>Baris <strong>baharu</strong> di Sheet → rekod baharu di dashboard</li>
      <li>Baris <strong>diedit</strong> di Sheet → isi medan yang MASIH KOSONG sahaja (tak timpa apa yang dah diedit di dashboard)</li>
      <li>Baris <strong>dipadam</strong> dari Sheet → rekod sepadan dipadam dari dashboard</li>
      <li>Rekod <strong>dipadam/ditambah/diedit</strong> di dashboard → refleks balik ke Sheet</li>
    </ul>
  </div>

  <div class="panduan-section" id="laporan">
    <h2>🖨️ Cetak Laporan</h2>
    <p>Klik <strong>"🖨️ Cetak Laporan"</strong> di toolbar Senarai Rekod untuk pilih:</p>
    <ul>
      <li><strong>Laporan Templat MyRA</strong> — format rasmi untuk penilaian MyRA</li>
      <li><strong>Laporan Keseluruhan</strong> — ringkasan lengkap semua rekod</li>
    </ul>
    <div class="panduan-nota">
      ⚠️ Sebelum cetak laporan RASMI, pastikan tiada rekod ujian/percubaan tertinggal dalam Senarai Rekod —
      semak & padam dulu kalau ada.
    </div>
  </div>

  <div class="panduan-section" id="soalan-lazim">
    <h2>❓ Soalan Lazim</h2>

    <h3>Kenapa data baharu dari Sheet tak muncul serta-merta?</h3>
    <p>Sync automatik jalan setiap 15 minit. Kalau nak segera, guna butang "Tarik Data Terkini".</p>

    <h3>Saya edit rekod tapi Sheet tak berubah?</h3>
    <p>Pastikan rekod tu ada Sync ID (rekod yang datang dari sync Sheet sepatutnya ada). Rekod yang dicipta manual sebelum ciri sync dua-hala dibina mungkin tiada Sync ID lagi.</p>

    <h3>"Ringkasan Manfaat" kosong untuk banyak rekod, kenapa?</h3>
    <p>OCR TIDAK auto-jalan bila rekod disync dari Sheet — cuma pautan fail disalin. Guna "OCR Pukal" untuk proses beramai-ramai, atau buka rekod & klik "Auto-Isi dari PDF" satu-satu.</p>

    <h3>ID Projek nampak tak sepadan dengan Klasifikasi?</h3>
    <p>Ini amaran sahaja (bukan sekatan) — ID Projek TAK auto-tukar bila Klasifikasi diedit (elak pecahkan rujukan folder/laporan lama). Boleh betulkan manual kalau perlu.</p>
  </div>
@endsection
