@extends('layouts.admin')

@section('title', 'Edit Award')

@section('content')
<style>
  .awards-form {
    --paper:#F6F7F8; --paper-raised:#FFFFFF; --ink:#141A16; --ink-soft:#667085;
    --line:#DDE2EA; --gold:#184290; --radius:12px;
    min-height:100%; padding:0; background:transparent; color:var(--ink);
    font-family:Inter,sans-serif;
  }
  .awards-form *, .awards-form *::before, .awards-form *::after { box-sizing:border-box; }
  .awards-form h1 { margin:0 0 20px; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em; }
  .awards-form .page-title {
    margin:0; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em;
  }
  .awards-form .page-description {
    margin:6px 0 0; color:#B4BED4; font-size:14px; font-weight:500; line-height:1.5;
  }
  .awards-form .card {
    padding:24px; border:1px solid rgb(180 190 212 / 30%); border-radius:var(--radius);
    background:var(--paper-raised); box-shadow:0 1px 3px rgb(16 24 40 / 5%);
  }
  .awards-form .btn {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    min-height:44px; padding:10px 18px; border:1px solid #184290; border-radius:8px;
    background:#184290; color:#fff; font-size:14px; font-weight:700;
    text-decoration:none; cursor:pointer;
  }
  .awards-form .btn.secondary { border-color:rgb(180 190 212 / 50%); background:#fff; color:#344054; }
  .awards-form .btn.secondary:hover { background:#F2F4F7; }
  .awards-form .form-field { margin-bottom:16px; min-width:0; }
  .awards-form .form-field label {
    display:block; margin-bottom:8px; color:#344054; font-size:14px;
    font-weight:500; letter-spacing:0; text-transform:none;
  }
  .awards-form .form-field input,
  .awards-form .form-field select,
  .awards-form .form-field textarea {
    width:100%; min-height:46px; padding:11px 14px; border:1px solid rgb(180 190 212 / 45%); border-radius:8px;
    background:#fff; color:var(--ink); font-family:inherit; font-size:14px;
    outline:none; transition:border-color .15s ease,box-shadow .15s ease;
  }
  .awards-form .form-field input:focus,
  .awards-form .form-field select:focus,
  .awards-form .form-field textarea:focus {
    border-color:#184290; box-shadow:0 0 0 3px rgb(24 66 144 / 12%);
  }
  .awards-form .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .awards-form .form-grid .full { grid-column:1/-1; }
  .awards-form .error-list { margin:0 32px 16px; padding:14px 16px; border:1px solid #FECDCA; border-radius:8px; background:#FEF3F2; color:#B42318; }
  .awards-form > header { padding:32px 32px 24px; }
  .awards-form > .form-card { margin:0 32px 32px; overflow:hidden; }
  .awards-form .form-card form { padding:28px; }
  .awards-form .drop-zone {
    border:2px dashed rgb(180 190 212 / 45%); border-radius:8px; padding:24px;
    text-align:center; cursor:pointer; transition:border-color .15s ease;
  }
  .awards-form .drop-zone:hover { border-color:#184290; }
  @media(max-width:640px) {
    .awards-form .form-grid { grid-template-columns:1fr; gap:0; }
    .awards-form .form-grid .full { grid-column:auto; }
    .awards-form .card { padding:15px; }
    .awards-form > .form-card { margin-right:16px !important;margin-left:16px !important; }
    .awards-form .error-list { margin-right:16px;margin-left:16px; }
    .awards-form > header { padding:24px 16px 20px; }
    .awards-form .form-card form { padding:18px; }
  }
</style>

<div class="awards-form">
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
      <h1 class="page-title">Edit Award</h1>
      <p class="page-description">Update award details</p>
    </div>
    <a href="{{ route('admin.awards') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
      <span class="material-symbols-outlined text-sm">arrow_back</span>
      <span>Back to Awards</span>
    </a>
  </div>
</header>

<div class="card form-card">
  <form action="{{ route('admin.awards.update', $award->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-grid">
      <div class="form-field full">
        <label for="staff_id">Staff Member <span style="color:#B42318">*</span></label>
        <input list="staff_list" id="staff_id" name="staff_id" required placeholder="Search staff member..."
          value="{{ old('staff_id', $award->staff_id) }}">
        <datalist id="staff_list">
          @foreach($staff as $s)
            <option value="{{ $s->id }}" {{ $award->staff_id == $s->id ? 'selected' : '' }}>{{ $s->staff_id }} - {{ $s->staff_name }}</option>
          @endforeach
        </datalist>
        @error('staff_id')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field full">
        <label for="project_id">Project <span style="color:#B42318">*</span></label>
        <input list="project_list" id="project_id" name="project_id" required placeholder="Search project..."
          value="{{ old('project_id', $award->project_id) }}">
        <datalist id="project_list">
          @foreach($projects as $p)
            <option value="{{ $p->id }}" {{ $award->project_id == $p->id ? 'selected' : '' }}>{{ $p->project_id }} - {{ $p->project_title }}</option>
          @endforeach
        </datalist>
        @error('project_id')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field full">
        <label for="award_name">Award Name <span style="color:#B42318">*</span></label>
        <input list="award_name_list" id="award_name" name="award_name" required placeholder="Search award name..."
          value="{{ old('award_name', $award->award_name) }}">
        <datalist id="award_name_list">
          <option value="PLATINUM" {{ $award->award_name == 'PLATINUM' ? 'selected' : '' }}>Platinum</option>
          <option value="SPECIAL" {{ $award->award_name == 'SPECIAL' ? 'selected' : '' }}>Special</option>
          <option value="GOLD" {{ $award->award_name == 'GOLD' ? 'selected' : '' }}>Gold</option>
          <option value="SILVER" {{ $award->award_name == 'SILVER' ? 'selected' : '' }}>Silver</option>
          <option value="BRONZE" {{ $award->award_name == 'BRONZE' ? 'selected' : '' }}>Bronze</option>
          <option value="OTHERS" {{ $award->award_name == 'OTHERS' ? 'selected' : '' }}>Others</option>
        </datalist>
        @error('award_name')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label for="award_level">Award Level <span style="color:#B42318">*</span></label>
        <input list="award_level_list" id="award_level" name="award_level" required placeholder="Search award level..."
          value="{{ old('award_level', $award->award_level) }}">
        <datalist id="award_level_list">
          <option value="INDIVIDUAL" {{ $award->award_level == 'INDIVIDUAL' ? 'selected' : '' }}>Individual</option>
          <option value="INSTITUTIONAL" {{ $award->award_level == 'INSTITUTIONAL' ? 'selected' : '' }}>Institutional</option>
        </datalist>
        @error('award_level')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label for="award_type">Award Type <span style="color:#B42318">*</span></label>
        <input list="award_type_list" id="award_type" name="award_type" required placeholder="Search award type..."
          value="{{ old('award_type', $award->award_type) }}">
        <datalist id="award_type_list">
          <option value="AWARD" {{ $award->award_type == 'AWARD' ? 'selected' : '' }}>Award</option>
          <option value="RECOGNITION" {{ $award->award_type == 'RECOGNITION' ? 'selected' : '' }}>Recognition</option>
          <option value="STEWARDSHIP" {{ $award->award_type == 'STEWARDSHIP' ? 'selected' : '' }}>Stewardship</option>
          <option value="EXHIBITION" {{ $award->award_type == 'EXHIBITION' ? 'selected' : '' }}>Exhibition</option>
          <option value="OTHER RESEARCH AWARDS" {{ $award->award_type == 'OTHER RESEARCH AWARDS' ? 'selected' : '' }}>Other Research Awards</option>
          <option value="CLARIVATE HIGHLY AWARD" {{ $award->award_type == 'CLARIVATE HIGHLY AWARD' ? 'selected' : '' }}>Clarivate Highly Award</option>
        </datalist>
        @error('award_type')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label for="invention_award">Invention Award</label>
        <select id="invention_award" name="invention_award">
          <option value="">Select</option>
          <option value="1" {{ old('invention_award', $award->invention_award) == 1 ? 'selected' : '' }}>Ya (Yes)</option>
          <option value="2" {{ old('invention_award', $award->invention_award) == 2 ? 'selected' : '' }}>Tidak (No)</option>
        </select>
        @error('invention_award')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field full">
        <label for="event_id">Event <span style="color:#B42318">*</span></label>
        <input list="event_list" id="event_id" name="event_id" required placeholder="Search event..."
          value="{{ old('event_id', $award->competition_id) }}">
        <datalist id="event_list">
          @foreach($events as $e)
            <option value="{{ $e->id }}" {{ $award->competition_id == $e->id ? 'selected' : '' }}>{{ $e->event_name }}</option>
          @endforeach
        </datalist>
        @error('event_id')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label for="event_exhibition_level">Exhibition Level <span style="color:#B42318">*</span></label>
        <input list="exhibition_level_list" id="event_exhibition_level" name="event_exhibition_level" required placeholder="Search exhibition level..."
          value="{{ old('event_exhibition_level', $award->event->exhibition_level ?? '') }}">
        <datalist id="exhibition_level_list">
          <option value="International" {{ ($award->event->exhibition_level ?? '') == 'International' ? 'selected' : '' }}>International</option>
          <option value="National" {{ ($award->event->exhibition_level ?? '') == 'National' ? 'selected' : '' }}>National</option>
          <option value="Regional" {{ ($award->event->exhibition_level ?? '') == 'Regional' ? 'selected' : '' }}>Regional</option>
          <option value="University" {{ ($award->event->exhibition_level ?? '') == 'University' ? 'selected' : '' }}>University</option>
        </datalist>
        @error('event_exhibition_level')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label for="award_date">Award Date <span style="color:#B42318">*</span></label>
        <input type="date" id="award_date" name="award_date" required
          value="{{ old('award_date', $award->award_date ? $award->award_date->format('Y-m-d') : '') }}">
        @error('award_date')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field full">
        <label>Evidence Document</label>
        <div id="drop-zone" class="drop-zone">
          <input type="file" name="evidence_document" id="file-input" class="hidden"
            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
          <div id="drop-content">
            <span class="material-symbols-outlined text-4xl" style="color:#B4BED4;margin-bottom:8px;display:block">cloud_upload</span>
            <p style="font-size:14px;color:var(--ink)">Drag & drop file here or <span style="color:#184290;font-weight:600">browse</span></p>
            <p style="font-size:12px;color:#B4BED4;margin-top:4px">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP (Max 10MB)</p>
            @if($award->evidence_document)
              <p style="font-size:12px;color:#184290;margin-top:8px">Current: <a href="{{ asset('storage/' . $award->evidence_document) }}" target="_blank" style="text-decoration:underline">View existing file</a></p>
            @endif
          </div>
          <div id="file-preview" style="display:none">
            <span class="material-symbols-outlined text-4xl" style="color:#184290;margin-bottom:8px;display:block">description</span>
            <p id="file-name" style="font-size:14px;color:var(--ink);font-weight:600"></p>
            <button type="button" onclick="clearFile()" style="font-size:12px;color:#B42318;margin-top:8px;background:none;border:none;cursor:pointer">Remove file</button>
          </div>
        </div>
        @error('evidence_document')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:24px;padding-top:24px;border-top:1px solid rgb(180 190 212 / 30%);">
      <div style="display:flex;align-items:center;gap:8px;color:#B4BED4;font-size:14px;">
        <span class="material-symbols-outlined" style="font-size:18px">info</span>
        <span>Last updated {{ $award->updated_at ? $award->updated_at->format('M d, Y') : 'N/A' }}</span>
      </div>
      <div style="display:flex;gap:12px;">
        <a href="{{ route('admin.awards') }}" class="btn secondary">Cancel</a>
        <button type="submit" class="btn">Update Award</button>
      </div>
    </div>
  </form>
</div>
</div>
@endsection

@push('scripts')
<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const dropContent = document.getElementById('drop-content');
const filePreview = document.getElementById('file-preview');
const fileName = document.getElementById('fileName');

dropZone.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        showFilePreview(e.target.files[0]);
    }
});

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = '#184290';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = 'rgb(180 190 212 / 45%)';
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'rgb(180 190 212 / 45%)';
    
    if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        showFilePreview(e.dataTransfer.files[0]);
    }
});

function showFilePreview(file) {
    dropContent.style.display = 'none';
    filePreview.style.display = 'block';
    fileName.textContent = file.name;
}

function clearFile() {
    fileInput.value = '';
    dropContent.style.display = 'block';
    filePreview.style.display = 'none';
}
</script>
@endpush
