@extends('layouts.admin')

@section('title', 'IP Renewals')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Renewal Management</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Review and update renewal dates for patents and trademarks. Status is calculated from expiry and next renewal dates.</p>
        </div>
        <a href="{{ route('admin.ip.index') }}" class="inline-flex h-11 items-center gap-2 rounded-lg border border-divider-subtle/40 bg-white px-4 text-sm font-bold text-text-main transition hover:bg-background-light">
            <span class="material-symbols-outlined text-lg" aria-hidden="true">list_alt</span> IP Records
        </a>
    </header>

    <div class="space-y-5 px-4 pb-8 sm:px-8">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700" role="status">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        @endif

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Renewal status summary">
            @foreach([
                ['key' => 'ACTIVE', 'label' => 'Active', 'note' => 'Protection beyond 12 months', 'tone' => 'text-green-700', 'icon' => 'verified'],
                ['key' => 'DUE_SOON', 'label' => 'Renewal Due Soon', 'note' => 'Due within the next 12 months', 'tone' => 'text-amber-700', 'icon' => 'schedule'],
                ['key' => 'EXPIRED', 'label' => 'Expired', 'note' => 'Past the expiry date', 'tone' => 'text-red-700', 'icon' => 'event_busy'],
                ['key' => 'NOT_SET', 'label' => 'Not Set', 'note' => 'No renewal or expiry date', 'tone' => 'text-gray-600', 'icon' => 'help'],
            ] as $metric)
                <article class="flex items-center justify-between gap-3 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm">
                    <div><p class="text-sm font-medium text-divider-subtle">{{ $metric['label'] }}</p><p class="mt-2 text-3xl font-bold {{ $metric['tone'] }}">{{ number_format($renewalCounts[$metric['key']]) }}</p><p class="mt-1 text-xs text-divider-subtle">{{ $metric['note'] }}</p></div>
                    <span class="material-symbols-outlined rounded-lg bg-background-light p-3 text-2xl {{ $metric['tone'] }}" aria-hidden="true">{{ $metric['icon'] }}</span>
                </article>
            @endforeach
        </section>

        <form method="GET" action="{{ route('admin.ip.renewals') }}" class="flex flex-wrap items-end gap-3 rounded-xl border border-divider-subtle/30 bg-white p-4 shadow-sm">
            <div>
                <label for="renewal-category" class="mb-2 block text-sm font-medium text-text-main">Category</label>
                <select id="renewal-category" name="category" class="h-11 rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main">
                    <option value="">All eligible categories</option>
                    @foreach(['PATENT_GRANTED', 'PATENT', 'TRADEMARK'] as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ str_replace('_', ' ', $category) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="renewal-status" class="mb-2 block text-sm font-medium text-text-main">Renewal Status</label>
                <select id="renewal-status" name="renewal_status" class="h-11 rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main">
                    <option value="">All statuses</option>
                    @foreach(['ACTIVE' => 'Active', 'DUE_SOON' => 'Renewal Due Soon', 'EXPIRED' => 'Expired', 'NOT_SET' => 'Not Set'] as $key => $label)
                        <option value="{{ $key }}" @selected(request('renewal_status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="h-11 rounded-lg bg-primary px-5 text-sm font-bold text-white hover:bg-primary/90">Apply</button>
            <a href="{{ route('admin.ip.renewals') }}" class="inline-flex h-11 items-center rounded-lg border border-divider-subtle/40 px-5 text-sm font-bold text-text-main hover:bg-background-light">Reset</a>
        </form>

        <section class="overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-divider-subtle/20 px-5 py-4">
                <h2 class="text-lg font-bold text-text-main">Renewal Records</h2>
                <span class="text-sm text-divider-subtle">{{ number_format($renewals->total()) }} matching record(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-divider-subtle/20">
                    <thead class="bg-background-light">
                        <tr>
                            @foreach(['Category', 'Product', 'Filing Number', 'Gazette Date', 'Expiry Date', 'Last Renewal', 'Next Renewal', 'Renewal Cost (RM)', 'Status', 'Actions'] as $heading)
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-text-main">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($renewals as $ip)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-4 text-xs font-semibold text-primary">{{ str_replace('_', ' ', $ip->category) }}</td>
                                <td class="min-w-56 px-4 py-4 text-sm font-medium text-text-main">{{ $ip->nama_produk_baru ?: $ip->title }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->ip_number ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->tarikh_pewartaan?->format('d M Y') ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->expiry_date?->format('d M Y') ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->tarikh_renewal_terakhir?->format('d M Y') ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->tarikh_renewal_seterusnya?->format('d M Y') ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-text-main">{{ $ip->kos_renewal !== null ? number_format((float) $ip->kos_renewal, 2) : '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    @php($statusStyle = ['ACTIVE' => 'bg-green-100 text-green-700', 'DUE_SOON' => 'bg-amber-100 text-amber-700', 'EXPIRED' => 'bg-red-100 text-red-700', 'NOT_SET' => 'bg-gray-100 text-gray-700'][$ip->renewal_status])
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusStyle }}">{{ str_replace('_', ' ', $ip->renewal_status) }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    <a href="{{ route('admin.ip.edit', $ip->id) }}" class="inline-flex h-9 items-center gap-1 rounded-lg bg-primary px-3 text-xs font-bold text-white hover:bg-primary/90">
                                        <span class="material-symbols-outlined text-sm" aria-hidden="true">edit</span> Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-4 py-12 text-center text-sm text-divider-subtle">No renewal records match these filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($renewals->hasPages())
                <div class="border-t border-divider-subtle/20 px-5 py-4">{{ $renewals->links() }}</div>
            @endif
        </section>
    </div>
@endsection
