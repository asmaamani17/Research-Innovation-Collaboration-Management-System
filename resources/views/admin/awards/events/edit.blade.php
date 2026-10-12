@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')
<style>
  .events-form {
    --paper:#F6F7F8; --paper-raised:#FFFFFF; --ink:#141A16; --ink-soft:#667085;
    --line:#DDE2EA; --gold:#184290; --radius:12px;
    min-height:100%; padding:0; background:transparent; color:var(--ink);
    font-family:Inter,sans-serif;
  }
  .events-form *, .events-form *::before, .events-form *::after { box-sizing:border-box; }
  .events-form h1 { margin:0 0 20px; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em; }
  .events-form .page-title {
    margin:0; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em;
  }
  .events-form .page-description {
    margin:6px 0 0; color:#B4BED4; font-size:14px; font-weight:500; line-height:1.5;
  }
  .events-form .card {
    padding:24px; border:1px solid rgb(180 190 212 / 30%); border-radius:var(--radius);
    background:var(--paper-raised); box-shadow:0 1px 3px rgb(16 24 40 / 5%);
  }
  .events-form .btn {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    min-height:44px; padding:10px 18px; border:1px solid #184290; border-radius:8px;
    background:#184290; color:#fff; font-size:14px; font-weight:700;
    text-decoration:none; cursor:pointer;
  }
  .events-form .btn.secondary { border-color:rgb(180 190 212 / 50%); background:#fff; color:#344054; }
  .events-form .btn.secondary:hover { background:#F2F4F7; }
  .events-form .form-field { margin-bottom:16px; min-width:0; }
  .events-form .form-field label {
    display:block; margin-bottom:8px; color:#344054; font-size:14px;
    font-weight:500; letter-spacing:0; text-transform:none;
  }
  .events-form .form-field input,
  .events-form .form-field select,
  .events-form .form-field textarea {
    width:100%; min-height:46px; padding:11px 14px; border:1px solid rgb(180 190 212 / 45%); border-radius:8px;
    background:#fff; color:var(--ink); font-family:inherit; font-size:14px;
    outline:none; transition:border-color .15s ease,box-shadow .15s ease;
  }
  .events-form .form-field input:focus,
  .events-form .form-field select:focus,
  .events-form .form-field textarea:focus {
    border-color:#184290; box-shadow:0 0 0 3px rgb(24 66 144 / 12%);
  }
  .events-form .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .events-form .form-grid .full { grid-column:1/-1; }
  .events-form .error-list { margin:0 32px 16px; padding:14px 16px; border:1px solid #FECDCA; border-radius:8px; background:#FEF3F2; color:#B42318; }
  .events-form > header { padding:32px 32px 24px; }
  .events-form > .form-card { margin:0 32px 32px; overflow:hidden; }
  .events-form .form-card form { padding:28px; }
  @media(max-width:640px) {
    .events-form .form-grid { grid-template-columns:1fr; gap:0; }
    .events-form .form-grid .full { grid-column:auto; }
    .events-form .card { padding:15px; }
    .events-form > .form-card { margin-right:16px !important;margin-left:16px !important; }
    .events-form .error-list { margin-right:16px;margin-left:16px; }
    .events-form > header { padding:24px 16px 20px; }
    .events-form .form-card form { padding:18px; }
  }
</style>

<div class="events-form">
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
      <h1 class="page-title">Edit Event</h1>
      <p class="page-description">Update event details and information</p>
    </div>
    <a href="{{ route('admin.events') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
      <span class="material-symbols-outlined text-sm">arrow_back</span>
      <span>Back to Events</span>
    </a>
  </div>
</header>

<div class="card form-card">
  <form action="{{ route('admin.event.update', $event->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-grid">
      <div class="form-field full">
        <label for="event_name">Event Name <span style="color:#B42318">*</span></label>
        <input type="text" id="event_name" name="event_name" required
          placeholder="Enter event name"
          value="{{ old('event_name', $event->event_name) }}">
        @error('event_name')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field full">
        <label for="organizer">Organizer <span style="color:#B42318">*</span></label>
        <input type="text" id="organizer" name="organizer" required
          placeholder="Organization name"
          value="{{ old('organizer', $event->organizer) }}">
        @error('organizer')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field full">
        <label for="exhibition_place">Exhibition Place <span style="color:#B42318">*</span></label>
        <input type="text" id="exhibition_place" name="exhibition_place" required
          placeholder="Search for a location or URL"
          value="{{ old('exhibition_place', $event->exhibition_place) }}">
        @error('exhibition_place')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label for="exhibition_level">Exhibition Level <span style="color:#B42318">*</span></label>
        <select id="exhibition_level" name="exhibition_level" required>
          <option value="">Select Level</option>
          <option value="International" {{ $event->exhibition_level === 'International' ? 'selected' : '' }}>International</option>
          <option value="Regional" {{ $event->exhibition_level === 'Regional' ? 'selected' : '' }}>Regional</option>
          <option value="National" {{ $event->exhibition_level === 'National' ? 'selected' : '' }}>National</option>
          <option value="University" {{ $event->exhibition_level === 'University' ? 'selected' : '' }}>University</option>
        </select>
        @error('exhibition_level')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label for="start_date">Start Date <span style="color:#B42318">*</span></label>
        <input type="date" id="start_date" name="start_date" required
          value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d') : '') }}">
        @error('start_date')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field">
        <label for="end_date">End Date <span style="color:#B42318">*</span></label>
        <input type="date" id="end_date" name="end_date" required
          value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d') : '') }}">
        @error('end_date')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:24px;padding-top:24px;border-top:1px solid rgb(180 190 212 / 30%);">
      <div style="display:flex;align-items:center;gap:8px;color:#B4BED4;font-size:14px;">
        <span class="material-symbols-outlined" style="font-size:18px">info</span>
        <span>Last updated {{ $event->updated_at ? $event->updated_at->format('M d, Y') : 'N/A' }}</span>
      </div>
      <div style="display:flex;gap:12px;">
        <a href="{{ route('admin.events') }}" class="btn secondary">Cancel</a>
        <button type="submit" class="btn">Update Event</button>
      </div>
    </div>
  </form>
</div>
</div>
@endsection
