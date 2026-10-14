@extends('layouts.admin')

@section('title', 'Import IP Data')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Import IP Data</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Import Excel or CSV files directly into RICM’s database.</p>
        </div>
        <a href="{{ route('admin.ip.index') }}" class="inline-flex h-11 items-center gap-2 rounded-lg border border-divider-subtle/40 bg-white px-4 text-sm font-bold text-text-main transition hover:bg-background-light">
            <span class="material-symbols-outlined text-lg" aria-hidden="true">list_alt</span> IP Records
        </a>
    </header>

    <div class="mx-auto max-w-4xl space-y-5 px-4 pb-8 sm:px-8">
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                <p class="font-bold">The import could not be completed:</p>
                <ul class="mt-1 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('admin.ip.import.store') }}" method="POST" enctype="multipart/form-data" class="overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm">
            @csrf
            <div class="space-y-5 p-5 sm:p-7">
                <div>
                    <label for="import-category" class="block text-sm font-semibold text-text-main">IP Category</label>
                    <select id="import-category" name="category" required class="mt-2 h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ str_replace('_', ' ', $category) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="import-mode" class="block text-sm font-semibold text-text-main">Import Mode</label>
                    <select id="import-mode" name="mode" required class="mt-2 h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="append" @selected(old('mode', 'append') === 'append')>Append to existing records</option>
                        <option value="replace" @selected(old('mode') === 'replace')>Replace records in this category</option>
                    </select>
                    <p class="mt-2 text-xs text-amber-700">Replace mode deletes the selected category’s current records before importing the file.</p>
                </div>
                <div>
                    <label for="ip-import-file" class="block text-sm font-semibold text-text-main">Excel / CSV File</label>
                    <input id="ip-import-file" name="file" type="file" required accept=".xlsx,.xls,.csv,.txt" class="mt-2 block w-full rounded-lg border border-divider-subtle/40 bg-white px-3 py-2 text-sm text-text-main file:mr-4 file:rounded-md file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-primary/15">
                    <p class="mt-2 text-xs text-divider-subtle">Maximum file size: 20 MB. The first row must contain column headings. The file is processed locally by RICM.</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-divider-subtle/20 bg-background-light px-5 py-4 sm:px-7">
                <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-lg bg-primary px-6 text-sm font-bold text-white transition hover:bg-primary/90" onclick="return document.getElementById('import-mode').value !== 'replace' || confirm('Replace all records in the selected category? This cannot be undone.')">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">upload_file</span> Import Records
                </button>
            </div>
        </form>

        <section class="rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm sm:p-7">
            <h2 class="text-lg font-bold text-text-main">Supported Column Headings</h2>
            <p class="mt-1 text-sm text-divider-subtle">A Product Name / Title column is required. Other headings are optional.</p>
            <div class="mt-4 grid gap-2 text-sm text-text-main sm:grid-cols-2">
                <p><strong>Product:</strong> PRODUCT, PRODUCT NAME, NAMA PRODUK, TITLE</p>
                <p><strong>Filing number:</strong> FILING NUMBER, NO PEMFAILAN</p>
                <p><strong>IP type:</strong> IP TYPE, JENIS IPR, JENIS PEMFAILAN</p>
                <p><strong>Researcher:</strong> RESEARCHER, NAMA PENYELIDIK</p>
                <p><strong>Co-researchers:</strong> CO-RESEARCHERS, PENYELIDIK BERSAMA</p>
                <p><strong>Faculty / year:</strong> FACULTY / FAKULTI, YEAR / TAHUN</p>
                <p><strong>Status:</strong> STATUS, STATUS PRODUK, PANEL DECISION</p>
                <p><strong>Dates:</strong> FILING DATE, NOTICE DATE, GAZETTE DATE, EXPIRY DATE</p>
                <p><strong>Costs:</strong> COST, KOS, RENEWAL COST, KOS RENEWAL</p>
                <p><strong>Additional:</strong> ABSTRACT, GRANT, AGENT, COUNTRY, REMARKS</p>
            </div>
        </section>
    </div>
@endsection
