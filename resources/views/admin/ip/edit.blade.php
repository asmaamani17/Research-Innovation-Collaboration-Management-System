@extends('layouts.admin')

@section('title', 'Edit IP Record')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Edit IP Record</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Update the details associated with this intellectual property.</p>
        </div>
        <a href="{{ route('admin.ip.show', $ip->id) }}" class="inline-flex h-11 items-center gap-2 rounded-lg border border-divider-subtle/40 bg-white px-4 text-sm font-bold text-text-main transition hover:bg-background-light">
            <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_back</span> Back to Record
        </a>
    </header>
    <div class="px-4 pb-8 sm:px-8">
        @include('admin.ip._form', ['action' => route('admin.ip.update', $ip->id), 'method' => 'PUT'])
    </div>
@endsection
