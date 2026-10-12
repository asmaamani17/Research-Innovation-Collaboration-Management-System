@extends('layouts.admin')

@section('title', 'IP Records')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">IP Records</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Search, filter, import, and manage intellectual property records stored in RICM.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.ip.export', request()->query()) }}" class="inline-flex h-11 items-center gap-2 rounded-lg border border-divider-subtle/40 bg-white px-4 text-sm font-bold text-text-main transition hover:bg-background-light">
                <span class="material-symbols-outlined text-lg" aria-hidden="true">download</span> Export Excel
            </a>
            <a href="{{ route('admin.ip.create') }}" class="inline-flex h-11 items-center gap-2 rounded-lg bg-primary px-4 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                <span class="material-symbols-outlined text-lg" aria-hidden="true">add</span> Add IP
            </a>
        </div>
    </header>

    <div class="space-y-4 px-4 pb-8 sm:px-8">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700" role="status">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                <p class="mb-1 font-bold">Please review the following:</p>
                <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="GET" action="{{ route('admin.ip.index') }}" class="grid grid-cols-1 items-end gap-3 rounded-xl border border-divider-subtle/30 bg-white p-4 shadow-sm sm:grid-cols-2 xl:grid-cols-8">
            <div class="xl:col-span-2">
                <label for="ip-record-search" class="mb-2 block text-sm font-medium text-text-main">Search records</label>
                <input id="ip-record-search" name="search" value="{{ request('search') }}" placeholder="Product, researcher, filing no."
                    class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main placeholder:text-divider-subtle focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
            </div>
            <div>
                <label for="ip-record-category" class="mb-2 block text-sm font-medium text-text-main">Category</label>
                <select id="ip-record-category" name="category" class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ str_replace('_', ' ', $category) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="ip-record-type" class="mb-2 block text-sm font-medium text-text-main">IP Type</label>
                <select id="ip-record-type" name="type" class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All IP Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_', ' ', $type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="ip-record-year" class="mb-2 block text-sm font-medium text-text-main">Year</label>
                <select id="ip-record-year" name="year" class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="ip-record-faculty" class="mb-2 block text-sm font-medium text-text-main">Faculty</label>
                <select id="ip-record-faculty" name="faculty" class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All Faculties</option>
                    @foreach($faculties as $faculty)
                        <option value="{{ $faculty['value'] }}" @selected(request('faculty') === $faculty['value'])>{{ $faculty['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="ip-record-status" class="mb-2 block text-sm font-medium text-text-main">Status</label>
                <select id="ip-record-status" name="status" class="h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-11 flex-1 rounded-lg bg-primary px-4 text-sm font-bold text-white transition hover:bg-primary/90">Apply</button>
                <a href="{{ route('admin.ip.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-divider-subtle/40 bg-white px-4 text-sm font-bold text-text-main transition hover:bg-background-light">Reset</a>
            </div>
        </form>

        <section class="overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm" aria-label="IP records table">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-divider-subtle/20 px-5 py-4">
                <h2 class="text-lg font-bold text-text-main">Intellectual Property</h2>
                <p class="text-sm text-divider-subtle">{{ number_format($ips->total()) }} matching record(s)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-divider-subtle/20">
                    <thead class="bg-background-light">
                        <tr>
                            @foreach(['Category', 'Product', 'Year', 'Faculty', 'Researcher(s)', 'Filing Number', 'Status / Gazette', 'Cost (RM)', 'Actions'] as $heading)
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-text-main">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($ips as $ip)
                            <tr class="transition-colors hover:bg-background-light/50">
                                <td class="whitespace-nowrap px-4 py-4">
                                    <span class="inline-flex rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">{{ str_replace('_', ' ', $ip->category ?: $ip->type) }}</span>
                                </td>
                                <td class="min-w-64 px-4 py-4">
                                    <a href="{{ route('admin.ip.show', $ip->id) }}" class="text-sm font-semibold text-text-main hover:text-primary">{{ $ip->nama_produk_baru ?: $ip->title }}</a>
                                    <p class="mt-1 text-xs text-divider-subtle">{{ $ip->type }}</p>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->tahun ?: optional($ip->filing_date)->year ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->fakulti ?: $ip->staff->map(fn ($staff) => $staff->faculty?->faculty_code)->filter()->unique()->join(', ') ?: '-' }}</td>
                                <td class="min-w-48 px-4 py-4 text-sm text-text-main">
                                    {{ $ip->nama_penyelidik ?: $ip->staff->pluck('staff_name')->join(', ') ?: '-' }}
                                    @if($ip->penyelidik_bersama)
                                        <p class="mt-1 text-xs text-divider-subtle">With: {{ Str::limit($ip->penyelidik_bersama, 50) }}</p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->ip_number ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ in_array(strtoupper((string) $ip->status), ['REGISTERED', 'GRANTED']) ? 'bg-green-100 text-green-700' : (in_array(strtoupper((string) $ip->status), ['FILED', 'PENDING']) ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ $ip->status ?: 'Not Set' }}
                                    </span>
                                    @if($ip->pewartaan)
                                        <p class="mt-1 text-xs text-divider-subtle">{{ $ip->pewartaan }}</p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->kos !== null ? number_format((float) $ip->kos, 2) : '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.ip.show', $ip->id) }}" aria-label="View record" class="rounded-lg p-2 text-primary transition hover:bg-primary/10"><span class="material-symbols-outlined text-lg">visibility</span></a>
                                        <a href="{{ route('admin.ip.edit', $ip->id) }}" aria-label="Edit record" class="rounded-lg p-2 text-primary transition hover:bg-primary/10"><span class="material-symbols-outlined text-lg">edit</span></a>
                                        <form action="{{ route('admin.ip.destroy', $ip->id) }}" method="POST" onsubmit="return confirm('Delete this IP record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Delete record" class="rounded-lg p-2 text-red-600 transition hover:bg-red-50"><span class="material-symbols-outlined text-lg">delete</span></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-4 py-14 text-center">
                                <span class="material-symbols-outlined text-4xl text-divider-subtle" aria-hidden="true">inventory_2</span>
                                <p class="mt-2 text-sm font-medium text-text-main">No intellectual property records found.</p>
                                <p class="mt-1 text-sm text-divider-subtle">Try changing your filters or add a new record.</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ips->hasPages())
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-divider-subtle/20 px-5 py-4">
                    <p class="text-sm text-divider-subtle">Showing {{ $ips->firstItem() }}–{{ $ips->lastItem() }} of {{ $ips->total() }} records</p>
                    {{ $ips->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
