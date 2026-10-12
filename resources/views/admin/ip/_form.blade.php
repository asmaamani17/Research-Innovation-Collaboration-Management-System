<style>
  .ip-form {
    --paper:#F6F7F8; --paper-raised:#FFFFFF; --ink:#141A16; --ink-soft:#667085;
    --line:#DDE2EA; --gold:#184290; --radius:12px;
    min-height:100%; padding:0; background:transparent; color:var(--ink);
    font-family:Inter,sans-serif;
  }
  .ip-form *, .ip-form *::before, .ip-form *::after { box-sizing:border-box; }
  .ip-form h1 { margin:0 0 20px; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em; }
  .ip-form .page-title {
    margin:0; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em;
  }
  .ip-form .page-description {
    margin:6px 0 0; color:#B4BED4; font-size:14px; font-weight:500; line-height:1.5;
  }
  .ip-form .card {
    padding:24px; border:1px solid rgb(180 190 212 / 30%); border-radius:var(--radius);
    background:var(--paper-raised); box-shadow:0 1px 3px rgb(16 24 40 / 5%);
  }
  .ip-form .btn {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    min-height:44px; padding:10px 18px; border:1px solid #184290; border-radius:8px;
    background:#184290; color:#fff; font-size:14px; font-weight:700;
    text-decoration:none; cursor:pointer;
  }
  .ip-form .btn.secondary { border-color:rgb(180 190 212 / 50%); background:#fff; color:#344054; }
  .ip-form .btn.secondary:hover { background:#F2F4F7; }
  .ip-form .form-field { margin-bottom:16px; min-width:0; }
  .ip-form .form-field label {
    display:block; margin-bottom:8px; color:#344054; font-size:14px;
    font-weight:500; letter-spacing:0; text-transform:none;
  }
  .ip-form .form-field input,
  .ip-form .form-field select,
  .ip-form .form-field textarea {
    width:100%; min-height:46px; padding:11px 14px; border:1px solid rgb(180 190 212 / 45%); border-radius:8px;
    background:#fff; color:var(--ink); font-family:inherit; font-size:14px;
    outline:none; transition:border-color .15s ease,box-shadow .15s ease;
  }
  .ip-form .form-field input:focus,
  .ip-form .form-field select:focus,
  .ip-form .form-field textarea:focus {
    border-color:#184290; box-shadow:0 0 0 3px rgb(24 66 144 / 12%);
  }
  .ip-form .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .ip-form .form-grid .full { grid-column:1/-1; }
  .ip-form .error-list { margin:0 32px 16px; padding:14px 16px; border:1px solid #FECDCA; border-radius:8px; background:#FEF3F2; color:#B42318; }
  .ip-form > header { padding:32px 32px 24px; }
  .ip-form > .form-card { margin:0 32px 32px; overflow:hidden; }
  .ip-form .form-card form { padding:28px; }
  .ip-form .section-title {
    font-size:18px; font-weight:700; color:#344054; margin:0 0 8px 0; padding-bottom:8px;
    border-bottom:1px solid rgb(180 190 212 / 30%);
  }
  .ip-form .section-description {
    font-size:14px; color:#667085; margin:0 0 20px 0;
  }
  @media(max-width:640px) {
    .ip-form .form-grid { grid-template-columns:1fr; gap:0; }
    .ip-form .form-grid .full { grid-column:auto; }
    .ip-form .card { padding:15px; }
    .ip-form > .form-card { margin-right:16px !important;margin-left:16px !important; }
    .ip-form .error-list { margin-right:16px;margin-left:16px; }
    .ip-form > header { padding:24px 16px 20px; }
    .ip-form .form-card form { padding:18px; }
  }
</style>

<div class="ip-form">
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

<header>
  <div class="flex items-center justify-between gap-4 mb-2">
    <div>
      <h1 class="page-title">{{ $method === 'POST' ? 'Add IP Record' : 'Edit IP Record' }}</h1>
      <p class="page-description">{{ $method === 'POST' ? 'Create a new intellectual property record' : 'Update intellectual property record' }}</p>
    </div>
    <a href="{{ route('admin.ip.index') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
      <span class="material-symbols-outlined text-sm">arrow_back</span>
      <span>Back to Records</span>
    </a>
  </div>
</header>

<div class="card form-card">
  <form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    @php
        $fieldClass = 'w-full min-h-46 px-3 py-3 border border-divider-subtle/45 bg-white text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 rounded-lg';
        $textFields = [
            ['category', 'Portfolio Category', 'select', $categories],
            ['type', 'IP Type', 'select', $types],
            ['title', 'Product / IP Name', 'text'],
            ['nama_produk_baru', 'New Product Name', 'text'],
            ['ip_number', 'Filing Number', 'text'],
            ['status', 'Status', 'text'],
            ['bil', 'Record Number', 'text'],
            ['tahun', 'Year', 'number'],
            ['tahun_aktiviti', 'Activity Year', 'number'],
            ['fakulti', 'Faculty', 'text'],
            ['coe', 'Centre of Excellence (CoE)', 'text'],
            ['jawatan', 'Position', 'text'],
            ['no_staf', 'Staff ID', 'text'],
            ['gelaran', 'Researcher Title', 'text'],
            ['nama_penyelidik', 'Researcher Name', 'text'],
            ['penyelidik_bersama', 'Co-researchers / Contribution', 'textarea'],
            ['geran', 'Grant', 'text'],
            ['ejen', 'Agent', 'text'],
            ['pewartaan', 'Gazette / Publication', 'text'],
            ['kategori', 'Subcategory', 'text'],
            ['kos', 'Filing Cost (RM)', 'number'],
            ['kos_renewal', 'Renewal Cost (RM)', 'number'],
            ['country', 'Country', 'text'],
            ['link_to_evidence', 'Evidence URL', 'url'],
            ['abstrak', 'Abstract', 'textarea'],
            ['remarks', 'Remarks', 'textarea'],
        ];
        $dateFields = [
            ['filing_date', 'Filing Date'],
            ['grant_date', 'Grant Date'],
            ['expiry_date', 'Expiry Date'],
            ['tarikh_notis', 'Notice Date'],
            ['tarikh_pewartaan', 'Gazette Date'],
            ['tarikh_renewal_terakhir', 'Last Renewal Date'],
            ['tarikh_renewal_seterusnya', 'Next Renewal Date'],
        ];
    @endphp

    <div class="form-grid">
        <div class="form-field">
            <label for="ip-category">Portfolio Category <span style="color:#B42318">*</span></label>
            <select id="ip-category" name="category" class="{{ $fieldClass }}" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(old('category', $ip->category) === $category)>{{ str_replace('_', ' ', $category) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-field">
            <label for="ip-type">IP Type <span style="color:#B42318">*</span></label>
            <select id="ip-type" name="type" class="{{ $fieldClass }}" required>
                <option value="">Select IP Type</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected(old('type', $ip->type) === $type)>{{ str_replace('_', ' ', $type) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-field full">
            <label for="ip-title">Product / IP Name <span style="color:#B42318">*</span></label>
            <input id="ip-title" name="title" type="text" value="{{ old('title', $ip->title) }}" class="{{ $fieldClass }}" required placeholder="Enter product name">
        </div>

        <div class="form-field full">
            <label for="ip-nama-produk-baru">New Product Name</label>
            <input id="ip-nama-produk-baru" name="nama_produk_baru" type="text" value="{{ old('nama_produk_baru', $ip->nama_produk_baru) }}" class="{{ $fieldClass }}" placeholder="Enter new product name">
        </div>

        <div class="form-field">
            <label for="ip-ip-number">Filing Number</label>
            <input id="ip-ip-number" name="ip_number" type="text" value="{{ old('ip_number', $ip->ip_number) }}" class="{{ $fieldClass }}" placeholder="Enter filing number">
        </div>

        <div class="form-field">
            <label for="ip-status">Status</label>
            <input id="ip-status" name="status" type="text" value="{{ old('status', $ip->status) }}" class="{{ $fieldClass }}" placeholder="Enter status">
        </div>

        <div class="form-field">
            <label for="ip-bil">Record Number</label>
            <input id="ip-bil" name="bil" type="text" value="{{ old('bil', $ip->bil) }}" class="{{ $fieldClass }}" placeholder="Enter record number">
        </div>

        <div class="form-field">
            <label for="ip-tahun">Year</label>
            <input id="ip-tahun" name="tahun" type="number" value="{{ old('tahun', $ip->tahun) }}" class="{{ $fieldClass }}" min="1900" max="2100" placeholder="Enter year">
        </div>

        <div class="form-field">
            <label for="ip-tahun-aktiviti">Activity Year</label>
            <input id="ip-tahun-aktiviti" name="tahun_aktiviti" type="number" value="{{ old('tahun_aktiviti', $ip->tahun_aktiviti) }}" class="{{ $fieldClass }}" min="1900" max="2100" placeholder="Enter activity year">
        </div>

        <div class="form-field">
            <label for="ip-fakulti">Faculty</label>
            <input id="ip-fakulti" name="fakulti" type="text" value="{{ old('fakulti', $ip->fakulti) }}" class="{{ $fieldClass }}" placeholder="Enter faculty">
        </div>

        <div class="form-field">
            <label for="ip-coe">Centre of Excellence (CoE)</label>
            <input id="ip-coe" name="coe" type="text" value="{{ old('coe', $ip->coe) }}" class="{{ $fieldClass }}" placeholder="Enter CoE">
        </div>

        <div class="form-field">
            <label for="ip-jawatan">Position</label>
            <input id="ip-jawatan" name="jawatan" type="text" value="{{ old('jawatan', $ip->jawatan) }}" class="{{ $fieldClass }}" placeholder="Enter position">
        </div>

        <div class="form-field">
            <label for="ip-no-staf">Staff ID</label>
            <input id="ip-no-staf" name="no_staf" type="text" value="{{ old('no_staf', $ip->no_staf) }}" class="{{ $fieldClass }}" placeholder="Enter staff ID">
        </div>

        <div class="form-field">
            <label for="ip-gelaran">Researcher Title</label>
            <input id="ip-gelaran" name="gelaran" type="text" value="{{ old('gelaran', $ip->gelaran) }}" class="{{ $fieldClass }}" placeholder="Enter researcher title">
        </div>

        <div class="form-field">
            <label for="ip-nama-penyelidik">Researcher Name</label>
            <input id="ip-nama-penyelidik" name="nama_penyelidik" type="text" value="{{ old('nama_penyelidik', $ip->nama_penyelidik) }}" class="{{ $fieldClass }}" placeholder="Enter researcher name">
        </div>

        <div class="form-field full">
            <label for="ip-penyelidik-bersama">Co-researchers / Contribution</label>
            <textarea id="ip-penyelidik-bersama" name="penyelidik_bersama" rows="3" class="{{ str_replace('min-h-46', '', $fieldClass) }}">{{ old('penyelidik_bersama', $ip->penyelidik_bersama) }}</textarea>
        </div>

        <div class="form-field">
            <label for="ip-geran">Grant</label>
            <input id="ip-geran" name="geran" type="text" value="{{ old('geran', $ip->geran) }}" class="{{ $fieldClass }}" placeholder="Enter grant">
        </div>

        <div class="form-field">
            <label for="ip-ejen">Agent</label>
            <input id="ip-ejen" name="ejen" type="text" value="{{ old('ejen', $ip->ejen) }}" class="{{ $fieldClass }}" placeholder="Enter agent">
        </div>

        <div class="form-field">
            <label for="ip-pewartaan">Gazette / Publication</label>
            <input id="ip-pewartaan" name="pewartaan" type="text" value="{{ old('pewartaan', $ip->pewartaan) }}" class="{{ $fieldClass }}" placeholder="Enter gazette/publication">
        </div>

        <div class="form-field">
            <label for="ip-kategori">Subcategory</label>
            <input id="ip-kategori" name="kategori" type="text" value="{{ old('kategori', $ip->kategori) }}" class="{{ $fieldClass }}" placeholder="Enter subcategory">
        </div>

        <div class="form-field">
            <label for="ip-kos">Filing Cost (RM)</label>
            <input id="ip-kos" name="kos" type="number" value="{{ old('kos', $ip->kos) }}" class="{{ $fieldClass }}" step="0.01" min="0" placeholder="Enter filing cost">
        </div>

        <div class="form-field">
            <label for="ip-kos-renewal">Renewal Cost (RM)</label>
            <input id="ip-kos-renewal" name="kos_renewal" type="number" value="{{ old('kos_renewal', $ip->kos_renewal) }}" class="{{ $fieldClass }}" step="0.01" min="0" placeholder="Enter renewal cost">
        </div>

        <div class="form-field">
            <label for="ip-country">Country</label>
            <input id="ip-country" name="country" type="text" value="{{ old('country', $ip->country) }}" class="{{ $fieldClass }}" placeholder="Enter country">
        </div>

        <div class="form-field">
            <label for="ip-link-to-evidence">Evidence URL</label>
            <input id="ip-link-to-evidence" name="link_to_evidence" type="url" value="{{ old('link_to_evidence', $ip->link_to_evidence) }}" class="{{ $fieldClass }}" placeholder="Enter evidence URL">
        </div>

        <div class="form-field">
            <label for="ip-filing-date">Filing Date</label>
            <input id="ip-filing-date" name="filing_date" type="date" value="{{ old('filing_date', $ip->filing_date?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
        </div>

        <div class="form-field">
            <label for="ip-grant-date">Grant Date</label>
            <input id="ip-grant-date" name="grant_date" type="date" value="{{ old('grant_date', $ip->grant_date?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
        </div>

        <div class="form-field">
            <label for="ip-expiry-date">Expiry Date</label>
            <input id="ip-expiry-date" name="expiry_date" type="date" value="{{ old('expiry_date', $ip->expiry_date?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
        </div>

        <div class="form-field">
            <label for="ip-tarikh-notis">Notice Date</label>
            <input id="ip-tarikh-notis" name="tarikh_notis" type="date" value="{{ old('tarikh_notis', $ip->tarikh_notis?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
        </div>

        <div class="form-field">
            <label for="ip-tarikh-pewartaan">Gazette Date</label>
            <input id="ip-tarikh-pewartaan" name="tarikh_pewartaan" type="date" value="{{ old('tarikh_pewartaan', $ip->tarikh_pewartaan?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
        </div>

        <div class="form-field">
            <label for="ip-tarikh-renewal-terakhir">Last Renewal Date</label>
            <input id="ip-tarikh-renewal-terakhir" name="tarikh_renewal_terakhir" type="date" value="{{ old('tarikh_renewal_terakhir', $ip->tarikh_renewal_terakhir?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
        </div>

        <div class="form-field">
            <label for="ip-tarikh-renewal-seterusnya">Next Renewal Date</label>
            <input id="ip-tarikh-renewal-seterusnya" name="tarikh_renewal_seterusnya" type="date" value="{{ old('tarikh_renewal_seterusnya', $ip->tarikh_renewal_seterusnya?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
        </div>

        <div class="form-field full">
            <label for="ip-staff-ids">Linked RICM Staff</label>
            <select id="ip-staff-ids" name="staff_ids[]" multiple class="{{ str_replace('min-h-46', '', $fieldClass) }} min-h-32">
                @foreach($staff as $member)
                    <option value="{{ $member->id }}" @selected(in_array($member->id, old('staff_ids', $ip->exists ? $ip->staff->pluck('id')->all() : [])))>
                        {{ $member->staff_id }} - {{ $member->staff_name }}{{ $member->faculty?->faculty_code ? ' ('.$member->faculty->faculty_code.')' : '' }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-divider-subtle">Hold Ctrl/Cmd to select multiple staff members.</p>
        </div>

        <div class="form-field full">
            <label for="ip-project-id">Linked Project</label>
            <select id="ip-project-id" name="project_id" class="{{ $fieldClass }}">
                <option value="">No linked project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected((string) old('project_id', $ip->project_id) === (string) $project->id)>
                        {{ $project->project_id }} - {{ Str::limit($project->project_title, 70) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-field full">
            <label for="ip-abstrak">Abstract</label>
            <textarea id="ip-abstrak" name="abstrak" rows="4" class="{{ str_replace('min-h-46', '', $fieldClass) }}">{{ old('abstrak', $ip->abstrak) }}</textarea>
        </div>

        <div class="form-field full">
            <label for="ip-remarks">Remarks</label>
            <textarea id="ip-remarks" name="remarks" rows="4" class="{{ str_replace('min-h-46', '', $fieldClass) }}">{{ old('remarks', $ip->remarks) }}</textarea>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3 mt-6 pt-6 border-t border-divider-subtle/20">
        <a href="{{ route('admin.ip.index') }}" class="btn secondary">Cancel</a>
        <button type="submit" class="btn">
            <span class="material-symbols-outlined text-lg">save</span> {{ $method === 'POST' ? 'Create Record' : 'Save Changes' }}
        </button>
    </div>
  </form>
</div>
</div>
