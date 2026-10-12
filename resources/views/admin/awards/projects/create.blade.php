@extends('layouts.admin')

@section('title', 'New Project')

@section('content')
<style>
  .projects-form {
    --paper:#F6F7F8; --paper-raised:#FFFFFF; --ink:#141A16; --ink-soft:#667085;
    --line:#DDE2EA; --gold:#184290; --radius:12px;
    min-height:100%; padding:0; background:transparent; color:var(--ink);
    font-family:Inter,sans-serif;
  }
  .projects-form *, .projects-form *::before, .projects-form *::after { box-sizing:border-box; }
  .projects-form h1 { margin:0 0 20px; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em; }
  .projects-form .page-title {
    margin:0; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em;
  }
  .projects-form .page-description {
    margin:6px 0 0; color:#B4BED4; font-size:14px; font-weight:500; line-height:1.5;
  }
  .projects-form .card {
    padding:24px; border:1px solid rgb(180 190 212 / 30%); border-radius:var(--radius);
    background:var(--paper-raised); box-shadow:0 1px 3px rgb(16 24 40 / 5%);
  }
  .projects-form .btn {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    min-height:44px; padding:10px 18px; border:1px solid #184290; border-radius:8px;
    background:#184290; color:#fff; font-size:14px; font-weight:700;
    text-decoration:none; cursor:pointer;
  }
  .projects-form .btn.secondary { border-color:rgb(180 190 212 / 50%); background:#fff; color:#344054; }
  .projects-form .btn.secondary:hover { background:#F2F4F7; }
  .projects-form .form-field { margin-bottom:16px; min-width:0; }
  .projects-form .form-field label {
    display:block; margin-bottom:8px; color:#344054; font-size:14px;
    font-weight:500; letter-spacing:0; text-transform:none;
  }
  .projects-form .form-field input,
  .projects-form .form-field select,
  .projects-form .form-field textarea {
    width:100%; min-height:46px; padding:11px 14px; border:1px solid rgb(180 190 212 / 45%); border-radius:8px;
    background:#fff; color:var(--ink); font-family:inherit; font-size:14px;
    outline:none; transition:border-color .15s ease,box-shadow .15s ease;
  }
  .projects-form .form-field input:focus,
  .projects-form .form-field select:focus,
  .projects-form .form-field textarea:focus {
    border-color:#184290; box-shadow:0 0 0 3px rgb(24 66 144 / 12%);
  }
  .projects-form .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .projects-form .form-grid .full { grid-column:1/-1; }
  .projects-form .error-list { margin:0 32px 16px; padding:14px 16px; border:1px solid #FECDCA; border-radius:8px; background:#FEF3F2; color:#B42318; }
  .projects-form > header { padding:32px 32px 24px; }
  .projects-form > .form-card { margin:0 32px 32px; overflow:hidden; }
  .projects-form .form-card form { padding:28px; }
  @media(max-width:640px) {
    .projects-form .form-grid { grid-template-columns:1fr; gap:0; }
    .projects-form .form-grid .full { grid-column:auto; }
    .projects-form .card { padding:15px; }
    .projects-form > .form-card { margin-right:16px !important;margin-left:16px !important; }
    .projects-form .error-list { margin-right:16px;margin-left:16px; }
    .projects-form > header { padding:24px 16px 20px; }
    .projects-form .form-card form { padding:18px; }
  }
</style>

<div class="projects-form">
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
      <h1 class="page-title">New Project</h1>
      <p class="page-description">Create a new research project</p>
    </div>
    <a href="{{ route('admin.projects.index') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
      <span class="material-symbols-outlined text-sm">arrow_back</span>
      <span>Back to Projects</span>
    </a>
  </div>
</header>

<div class="card form-card">
  <form action="{{ route('admin.projects.store') }}" method="POST">
    @csrf

    <div class="form-grid">
      <div class="form-field">
        <label for="project_id">Project ID <span style="color:#B42318">*</span></label>
        <input type="number" id="project_id" name="project_id" required
          placeholder="Enter project ID"
          value="{{ old('project_id') }}">
        @error('project_id')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field full">
        <label for="project_title">Project Title <span style="color:#B42318">*</span></label>
        <input type="text" id="project_title" name="project_title" required
          placeholder="Enter project title"
          value="{{ old('project_title') }}">
        @error('project_title')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-field full">
        <label for="grant_no">Grant No</label>
        <input type="text" id="grant_no" name="grant_no"
          placeholder="Enter grant number (optional)"
          value="{{ old('grant_no') }}">
        @error('grant_no')
          <p style="margin-top:4px;font-size:12px;color:#B42318">{{ $message }}</p>
        @enderror
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:24px;border-top:1px solid rgb(180 190 212 / 30%);">
      <a href="{{ route('admin.projects.index') }}" class="btn secondary">Cancel</a>
      <button type="submit" class="btn">Create Project</button>
    </div>
  </form>
</div>
</div>
@endsection
