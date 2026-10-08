@extends('layouts.admin')

@section('title', 'Edit Program Record')
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
  <header>
    <h1 class="page-title">Kemaskini Rekod</h1>
    <p class="page-description">Review and update this Community, STEM, or Industry program record.</p>
  </header>

  <div class="card form-card">
    <form action="{{ route('admin.stem.programs.update', $program) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('admin.community,stem,industry.programs._form')
    </form>
  </div>
  </div>
@endsection