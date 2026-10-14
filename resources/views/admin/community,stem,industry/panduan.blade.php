@extends('layouts.admin')
@section('title', 'Panduan Penggunaan Sistem')

@section('content')
<!-- Page Header -->
<header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
  <div class="flex flex-col">
    <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
      <span class="material-symbols-outlined align-middle mr-2">menu_book</span>
      Panduan Penggunaan Sistem
    </h2>
    <p class="text-divider-subtle font-medium text-sm mt-1">
      Panduan lengkap penggunaan sistem pengurusan program Komuniti, STEM & Industri
    </p>
  </div>
  <a href="{{ route('admin.stem.dashboard') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 border border-divider-subtle/50 bg-white text-text-main text-sm font-bold shadow-sm hover:bg-background-light transition-all">
    <span class="material-symbols-outlined text-sm">arrow_back</span>
    <span>Balik ke Dashboard</span>
  </a>
</header>

<div class="px-8 pb-8">
  <!-- Table of Contents -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined">list</span>
      Isi Kandungan
    </h3>
    <div class="flex flex-wrap gap-2">
      <a href="#gambaran" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">Gambaran Keseluruhan</a>
      <a href="#dashboard" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">Dashboard</a>
      <a href="#senarai-rekod" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">Senarai Rekod</a>
      <a href="#tambah-edit" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">Tambah / Edit Rekod</a>
      <a href="#ocr" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">Auto-Isi OCR</a>
      <a href="#ocr-pukal" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">OCR Pukal</a>
      <a href="#evidence" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">Gabung Evidence</a>
      <a href="#sync" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">Sync Database</a>
      <a href="#laporan" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">Cetak Laporan</a>
      <a href="#soalan-lazim" class="px-4 py-2 bg-background-light hover:bg-primary hover:text-white text-text-main text-sm font-medium rounded-lg transition-colors">Soalan Lazim</a>
    </div>
  </div>

  <!-- Gambaran Keseluruhan -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="gambaran">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">map</span>
      Gambaran Keseluruhan
    </h3>
    <p class="text-text-main text-sm leading-relaxed mb-4">
      Sistem ni menguruskan rekod program Komuniti/STEM/Industri (KSTP/CSR) UTeM.
      Data datang dari <strong>dua sumber</strong> yang saling berkait:
    </p>
    <ol class="list-decimal list-inside text-text-main text-sm leading-relaxed mb-4 space-y-2">
      <li><strong>Borang A (Pendaftaran)</strong> — diuruskan berasingan (Google Form/Sheet), <em>di luar</em> dashboard ni.</li>
      <li><strong>Borang B, C, D (Laporan)</strong> — ini yang dashboard ni uruskan. Data masuk secara <strong>automatik</strong> dari database, atau boleh ditambah <strong>manual</strong> terus di sini.</li>
    </ol>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-yellow-600 text-xl">lightbulb</span>
        <p class="text-yellow-800 text-sm">
          Sistem ni <strong>dua-hala</strong> — apa-apa yang awak tambah/edit/padam di dashboard akan refleks ke database secara automatik, dan sebaliknya.
        </p>
      </div>
    </div>
  </div>

  <!-- Dashboard -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="dashboard">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">dashboard</span>
      Dashboard
    </h3>
    <p class="text-text-main text-sm leading-relaxed mb-4">Halaman utama papar ringkasan keseluruhan data dalam bentuk carta:</p>
    <ul class="list-disc list-inside text-text-main text-sm leading-relaxed space-y-2">
      <li><strong>Kad ringkasan</strong> — jumlah penghantaran, pecahan STEM/Komuniti/Industri</li>
      <li><strong>Carta "Penghantaran Mengikut Pusat"</strong> — bandingkan setiap Fakulti/PTJ</li>
      <li><strong>Carta "Trend Bulanan"</strong> — lihat corak penghantaran sepanjang tahun</li>
      <li><strong>Penapis Tahun & Pusat Tanggungjawab</strong> — di bahagian atas, guna untuk fokus data tertentu</li>
    </ul>
  </div>

  <!-- Senarai Rekod -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="senarai-rekod">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">table_view</span>
      Senarai Rekod
    </h3>
    <p class="text-text-main text-sm leading-relaxed mb-4">Ini jadual utama semua rekod program. Beberapa ciri penting:</p>

    <h4 class="text-base font-semibold text-text-main mb-2 mt-4">Cari & Tapis</h4>
    <ul class="list-disc list-inside text-text-main text-sm leading-relaxed mb-4 space-y-2">
      <li>Kotak <strong>"Cari Program"</strong> — cari ikut nama program</li>
      <li>Penapis <strong>Tahun / Pusat / Klasifikasi / Kategori</strong> — boleh gabung beberapa sekali</li>
      <li>Chip <strong>"Penapis aktif"</strong> di bawah — klik <span class="material-symbols-outlined text-sm align-middle">close</span> untuk buang satu penapis sahaja</li>
    </ul>

    <h4 class="text-base font-semibold text-text-main mb-2 mt-4">Susun Lajur</h4>
    <p class="text-text-main text-sm leading-relaxed mb-4">Klik mana-mana tajuk lajur (ada ikon <span class="material-symbols-outlined text-sm align-middle">swap_vert</span>) untuk susun ikut lajur tu — klik sekali lagi untuk tukar arah (menaik/menurun).</p>

    <h4 class="text-base font-semibold text-text-main mb-2 mt-4">Lajur Evidence</h4>
    <p class="text-text-main text-sm leading-relaxed mb-4">Nombor tu tunjuk berapa banyak fail/pautan evidence rekod tu ada. Klik untuk buka dropdown — pautan Borang A/B/C/D, Kelulusan, dan Folder Gabungan (kalau dah pernah digabungkan).</p>

    <h4 class="text-base font-semibold text-text-main mb-2 mt-4">Menu Tindakan</h4>
    <ul class="list-disc list-inside text-text-main text-sm leading-relaxed space-y-2">
      <li><strong><span class="material-symbols-outlined text-sm align-middle">edit</span> Edit</strong> — kemaskini rekod</li>
      <li><strong><span class="material-symbols-outlined text-sm align-middle">link</span> Gabung Evidence</strong> — kumpulkan semua fail evidence rekod ni ke SATU folder Google Drive (lihat bahagian "Gabung Evidence" di bawah)</li>
      <li><strong><span class="material-symbols-outlined text-sm align-middle">delete</span> Padam</strong> — padam rekod (turut padam baris sepadan di database secara automatik)</li>
    </ul>
  </div>

  <!-- Tambah / Edit Rekod -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="tambah-edit">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">add_circle</span>
      Tambah / Edit Rekod
    </h3>
    <p class="text-text-main text-sm leading-relaxed mb-4">Guna borang ni untuk masukkan rekod Borang B/C/D <strong>secara manual</strong> (kalau bukan datang dari Google Form).</p>
    <div class="bg-red-50 border-l-4 border-red-400 rounded-lg p-4 mb-4">
      <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-red-600 text-xl">warning</span>
        <p class="text-red-800 text-sm">
          <strong>ID Projek</strong> ada peraturan huruf: Community = <strong>C</strong>, STEM = <strong>B</strong>, Industri = <strong>A</strong>.
          Kalau isi manual dan huruf tak sepadan dengan Klasifikasi, sistem akan tunjuk amaran (bukan sekatan) —
          boleh biarkan kosong untuk sistem jana automatik.
        </p>
      </div>
    </div>
    <p class="text-text-main text-sm leading-relaxed"><strong>Pautan Borang A/B/C/D</strong> — kalau staf dah upload fail ke Google Drive sendiri, paste pautan tu di medan berkenaan supaya ia boleh dimasukkan ke Folder Gabungan Evidence nanti.</p>
  </div>

  <!-- Auto-Isi OCR -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="ocr">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">document_scanner</span>
      Auto-Isi dari PDF (OCR)
    </h3>
    <p class="text-text-main text-sm leading-relaxed mb-4">
      Kalau ada fail PDF Borang A, boleh muat naik di borang Tambah/Edit dan klik
      <strong>"Auto-Isi dari PDF"</strong> — sistem akan cuba baca & isi automatik:
      Nama Program, Tarikh Mula/Tamat, Ringkasan Manfaat, Nama Komuniti/Industri, Nilai Tajaan.
    </p>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-yellow-600 text-xl">lightbulb</span>
        <p class="text-yellow-800 text-sm">
          OCR bukan 100% tepat — <strong>SILA SEMAK</strong> selalu medan yang diisi automatik sebelum simpan.
          Anggaran 10-20% rekod akan perlukan pembetulan manual — ini had semula jadi teknologi OCR, bukan bug.
        </p>
      </div>
    </div>
  </div>

  <!-- OCR Pukal -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="ocr-pukal">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">settings_suggest</span>
      OCR Pukal
    </h3>
    <p class="text-text-main text-sm leading-relaxed mb-4">
      Kalau ada BANYAK rekod (dari sync database) yang "Ringkasan Manfaat" masih kosong,
      guna ciri ni untuk proses OCR SEMUA sekali gus (bukan satu-satu manual).
    </p>
    <ol class="list-decimal list-inside text-text-main text-sm leading-relaxed space-y-2">
      <li>Klik <strong><span class="material-symbols-outlined text-sm align-middle">document_scanner</span> OCR Pukal</strong> di toolbar Senarai Rekod</li>
      <li>Klik <strong><span class="material-symbols-outlined text-sm align-middle">play_arrow</span> Proses Semua</strong></li>
      <li><strong>JANGAN tutup tab</strong> sepanjang proses (boleh ambil masa beberapa minit ikut jumlah rekod)</li>
      <li>Kalau tertutup separuh jalan, buka semula page — rekod yang dah siap TAK akan diproses ulang</li>
    </ol>
  </div>

  <!-- Gabung Evidence -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="evidence">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">link</span>
      Gabung Evidence
    </h3>
    <p class="text-text-main text-sm leading-relaxed mb-4">
      Fungsi ni kumpulkan SEMUA fail evidence (Borang A/B/C/D + Kelulusan) untuk satu rekod
      ke dalam SATU folder Google Drive, guna <strong>pautan pintasan (shortcut)</strong> —
      fail asal kekal di lokasi asal, cuma dipautkan ke satu tempat untuk senang rujuk.
    </p>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-yellow-600 text-xl">lightbulb</span>
        <p class="text-yellow-800 text-sm">
          Proses ni jalan di latar belakang (queue) — mungkin ambil beberapa saat. Refresh page
          lepas seminit untuk lihat pautan "Folder Gabungan" muncul di dropdown Evidence.
        </p>
      </div>
    </div>
  </div>

  <!-- Sync Database -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="sync">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">sync</span>
      Sync Database
    </h3>
    <p class="text-text-main text-sm leading-relaxed mb-4">Data dari database masuk ke dashboard secara <strong>automatik setiap 15 minit</strong> — admin TAK perlu buat apa-apa.</p>
    <p class="text-text-main text-sm leading-relaxed mb-4">Kalau nak update SEGERA (tak nak tunggu 15 minit), klik butang <strong><span class="material-symbols-outlined text-sm align-middle">sync</span> Tarik Data Terkini</strong> di toolbar Senarai Rekod.</p>

    <h4 class="text-base font-semibold text-text-main mb-2 mt-4">Apa yang sync buat?</h4>
    <ul class="list-disc list-inside text-text-main text-sm leading-relaxed space-y-2">
      <li>Baris <strong>baharu</strong> di database → rekod baharu di dashboard</li>
      <li>Baris <strong>diedit</strong> di database → isi medan yang MASIH KOSONG sahaja (tak timpa apa yang dah diedit di dashboard)</li>
      <li>Baris <strong>dipadam</strong> dari database → rekod sepadan dipadam dari dashboard</li>
      <li>Rekod <strong>dipadam/ditambah/diedit</strong> di dashboard → refleks balik ke database</li>
    </ul>
  </div>

  <!-- Cetak Laporan -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="laporan">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">print</span>
      Cetak Laporan
    </h3>
    <p class="text-text-main text-sm leading-relaxed mb-4">Klik <strong><span class="material-symbols-outlined text-sm align-middle">print</span> Cetak Laporan</strong> di toolbar Senarai Rekod untuk pilih:</p>
    <ul class="list-disc list-inside text-text-main text-sm leading-relaxed mb-4 space-y-2">
      <li><strong>Laporan Templat MyRA</strong> — format rasmi untuk penilaian MyRA</li>
      <li><strong>Laporan Keseluruhan</strong> — ringkasan lengkap semua rekod</li>
    </ul>
    <div class="bg-red-50 border-l-4 border-red-400 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-red-600 text-xl">warning</span>
        <p class="text-red-800 text-sm">
          Sebelum cetak laporan RASMI, pastikan tiada rekod ujian/percubaan tertinggal dalam Senarai Rekod —
          semak & padam dulu kalau ada.
        </p>
      </div>
    </div>
  </div>

  <!-- Soalan Lazim -->
  <div class="bg-white border border-divider-subtle/30 rounded-xl p-6 mb-6 shadow-sm" id="soalan-lazim">
    <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">help</span>
      Soalan Lazim
    </h3>

    <div class="space-y-4">
      <div>
        <h4 class="text-base font-semibold text-text-main mb-2">Kenapa data baharu dari database tak muncul serta-merta?</h4>
        <p class="text-text-main text-sm leading-relaxed">Sync automatik jalan setiap 15 minit. Kalau nak segera, guna butang "Tarik Data Terkini".</p>
      </div>

      <div>
        <h4 class="text-base font-semibold text-text-main mb-2">Saya edit rekod tapi database tak berubah?</h4>
        <p class="text-text-main text-sm leading-relaxed">Pastikan rekod tu ada Sync ID (rekod yang datang dari sync database sepatutnya ada). Rekod yang dicipta manual sebelum ciri sync dua-hala dibina mungkin tiada Sync ID lagi.</p>
      </div>

      <div>
        <h4 class="text-base font-semibold text-text-main mb-2">"Ringkasan Manfaat" kosong untuk banyak rekod, kenapa?</h4>
        <p class="text-text-main text-sm leading-relaxed">OCR TIDAK auto-jalan bila rekod disync dari database — cuma pautan fail disalin. Guna "OCR Pukal" untuk proses beramai-ramai, atau buka rekod & klik "Auto-Isi dari PDF" satu-satu.</p>
      </div>

      <div>
        <h4 class="text-base font-semibold text-text-main mb-2">ID Projek nampak tak sepadan dengan Klasifikasi?</h4>
        <p class="text-text-main text-sm leading-relaxed">Ini amaran sahaja (bukan sekatan) — ID Projek TAK auto-tukar bila Klasifikasi diedit (elak pecahkan rujukan folder/laporan lama). Boleh betulkan manual kalau perlu.</p>
      </div>
    </div>
  </div>
</div>
@endsection
