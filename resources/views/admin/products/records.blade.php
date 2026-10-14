@extends('layouts.admin')

@section('title', 'Product Records')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Product Records</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Search, filter, import, export, and manage TRL product records.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.products.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-primary px-5 text-sm font-bold text-white transition hover:bg-primary/90">
                <span class="material-symbols-outlined text-lg" aria-hidden="true">add</span> Add Product
            </a>
        </div>
    </header>

    <div class="space-y-5 px-4 pb-8 sm:px-8">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="mb-1 font-bold">Please check the import or form errors:</p>
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <section class="overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-divider-subtle/30 px-5 py-4 sm:px-6">
                <div>
                    <h2 class="text-lg font-bold text-text-main">All Product Records</h2>
                    <p class="mt-1 text-sm text-divider-subtle">Showing {{ number_format($products->firstItem() ?? 0) }}–{{ number_format($products->lastItem() ?? 0) }} of {{ number_format($products->total()) }} products</p>
                </div>
                <a href="{{ route('admin.products.export', request()->query()) }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-divider-subtle/40 bg-white px-4 text-sm font-bold text-text-main transition hover:bg-background-light">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">download</span> Export Excel
                </a>
            </div>

            <form method="GET" action="{{ route('admin.products.records') }}" class="grid grid-cols-1 gap-3 border-b border-divider-subtle/20 bg-background-light/50 p-4 sm:grid-cols-2 xl:grid-cols-[minmax(220px,1fr)_150px_190px_180px_140px_auto_auto] sm:p-5">
                <input name="search" value="{{ request('search') }}" placeholder="Search product, researcher, or category..." class="h-11 rounded-lg border border-divider-subtle/30 bg-white px-4 text-sm text-text-main placeholder:text-divider-subtle focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                <select name="trl" class="h-11 rounded-lg border border-divider-subtle/30 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All TRL Levels</option>
                    @foreach(range(1, 9) as $level)
                        <option value="{{ $level }}" @selected((string) request('trl') === (string) $level)>TRL {{ $level }}</option>
                    @endforeach
                </select>
                <select name="status" class="h-11 rounded-lg border border-divider-subtle/30 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All Statuses</option>
                    <option value="__not_set__" @selected(request('status') === '__not_set__')>Not Set</option>
                    @foreach($allStatuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select name="faculty" class="h-11 rounded-lg border border-divider-subtle/30 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All Faculties</option>
                    <option value="__not_set__" @selected(request('faculty') === '__not_set__')>Not Set</option>
                    @foreach($allFaculties as $faculty)
                        <option value="{{ $faculty }}" @selected(request('faculty') === $faculty)>{{ $faculty }}</option>
                    @endforeach
                </select>
                <select name="year" class="h-11 rounded-lg border border-divider-subtle/30 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All Years</option>
                    @foreach($allYears as $year)
                        <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
                <button type="submit" class="h-11 rounded-lg bg-primary px-5 text-sm font-bold text-white transition hover:bg-primary/90">Filter</button>
                <a href="{{ route('admin.products.records') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-divider-subtle/40 bg-white px-5 text-sm font-bold text-text-main transition hover:bg-background-light">Reset</a>
            </form>

            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 border-b border-divider-subtle/20 px-4 py-4 sm:flex-row sm:items-center sm:px-6">
                @csrf
                <div class="min-w-0 flex-1">
                    <label for="product-import-file" class="mb-1 block text-sm font-semibold text-text-main">Import Excel / CSV into RICM</label>
                    <p class="text-xs text-divider-subtle">Columns: UUID, TRL, Product, Researcher, Faculty, Category, Status, Action, Abstract, Year. Matching UUIDs update records.</p>
                </div>
                <input id="product-import-file" type="file" name="file" required accept=".xlsx,.xls,.csv,.txt" class="block w-full max-w-sm text-sm text-text-main file:mr-3 file:rounded-lg file:border-0 file:bg-background-light file:px-4 file:py-2 file:text-sm file:font-semibold file:text-text-main">
                <button type="submit" class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-lg border border-primary px-4 text-sm font-bold text-primary transition hover:bg-primary/5">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">upload_file</span> Import
                </button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] border-collapse text-left text-sm">
                    <thead class="bg-background-light">
                        <tr>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main sm:px-6">TRL</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main">Product</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main">Researcher</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main">Faculty</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main">Category</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main">Status</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main">Action</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main">Year</th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-text-main sm:px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($products as $product)
                            <tr class="transition-colors hover:bg-background-light/60">
                                <td class="px-5 py-4 sm:px-6"><span class="inline-flex rounded-full bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary">TRL {{ $product->trl_level }}</span></td>
                                <td class="max-w-sm px-5 py-4">
                                    <p class="font-semibold text-text-main">{{ $product->product }}</p>
                                    @if($product->abstract)<p class="mt-1 max-w-sm truncate text-xs text-divider-subtle" title="{{ $product->abstract }}">{{ $product->abstract }}</p>@endif
                                </td>
                                <td class="px-5 py-4 text-text-main">{{ $product->researcher ?: '—' }}</td>
                                <td class="px-5 py-4 text-text-main">{{ $product->faculty ?: '—' }}</td>
                                <td class="max-w-xs px-5 py-4 text-text-main">{{ $product->category ?: '—' }}</td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-full bg-background-light px-2.5 py-1 text-xs font-semibold text-text-main">{{ $product->status ?: 'Not Set' }}</span></td>
                                <td class="px-5 py-4 text-text-main">{{ $product->action ?: '—' }}</td>
                                <td class="px-5 py-4 text-text-main">{{ $product->product_year ?: '—' }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex h-9 items-center gap-1 rounded-lg border border-divider-subtle/40 px-3 text-xs font-bold text-text-main transition hover:bg-background-light"><span class="material-symbols-outlined text-base" aria-hidden="true">edit</span>Edit</a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Move this product record to the recycle bin?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-9 items-center gap-1 rounded-lg border border-red-200 px-3 text-xs font-bold text-red-700 transition hover:bg-red-50"><span class="material-symbols-outlined text-base" aria-hidden="true">delete</span>Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-6 py-12 text-center text-sm text-divider-subtle">No product records match the current filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-divider-subtle/20 px-5 py-4 sm:px-6">{{ $products->links() }}</div>
        </section>
    </div>
@endsection
