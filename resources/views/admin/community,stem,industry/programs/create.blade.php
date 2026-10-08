@extends('layouts.admin')

@section('title', 'Add Program Record')
@section('content')
  @include('admin.community,stem,industry.programs._styles')
  <div class="community-programs">
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
    <h1 class="page-title">Tambah Rekod Baharu</h1>
    <p class="page-description">Enter the program details and supporting evidence to add a new record.</p>
  </header>

  <div class="card form-card">
    <form action="{{ route('admin.stem.programs.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('admin.community,stem,industry.programs._form')
    </form>
  </div>
  </div>
@endsection