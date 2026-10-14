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
    <div class="flex items-center justify-between gap-4 mb-2">
      <div>
        <h1 class="page-title">Tambah Rekod Baharu</h1>
        <p class="page-description">Enter the program details and supporting evidence to add a new record.</p>
      </div>
      <a href="{{ route('admin.stem.programs.index') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Back to Programs</span>
      </a>
    </div>
  </header>

  <div class="card form-card">
    <form action="{{ route('admin.stem.programs.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('admin.community,stem,industry.programs._form')
    </form>
  </div>
  </div>
@endsection