@extends('layouts.admin')

@section('title', 'Add IP Record')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Add IP Record</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Add an intellectual property record to the RICM database.</p>
        </div>
        <a href="{{ route('admin.ip.index') }}" class="inline-flex h-11 items-center gap-2 rounded-lg border border-divider-subtle/40 bg-white px-4 text-sm font-bold text-text-main transition hover:bg-background-light">
            <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_back</span> Back to Records
        </a>
    </header>
    <div class="px-4 pb-8 sm:px-8">
        @include('admin.ip._form', ['action' => route('admin.ip.store'), 'method' => 'POST'])
    </div>
@endsection
