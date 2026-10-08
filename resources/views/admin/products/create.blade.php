@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Add Product</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Add a product record and its development details.</p>
        </div>
        <a href="{{ route('admin.products.records') }}" class="inline-flex h-11 items-center gap-2 rounded-lg border border-divider-subtle/40 bg-white px-5 text-sm font-bold text-text-main transition hover:bg-background-light">
            <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_back</span> Back to Products
        </a>
    </header>
    <div class="px-4 pb-8 sm:px-8">
        <div class="mx-auto max-w-5xl overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                @include('admin.products._form')
            </form>
        </div>
    </div>
@endsection
