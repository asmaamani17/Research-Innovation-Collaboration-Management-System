@extends('layouts.admin')

@section('title', 'IP Reports')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">IP Reports</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">Portfolio totals, filing status, and category breakdowns from the RICM database.</p>
        </div>
        <a href="{{ route('admin.ip.export') }}" class="inline-flex h-11 items-center gap-2 rounded-lg bg-primary px-4 text-sm font-bold text-white transition hover:bg-primary/90">
            <span class="material-symbols-outlined text-lg" aria-hidden="true">download</span> Export Records
        </a>
    </header>

    <div class="space-y-6 px-4 pb-8 sm:px-8">
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5" aria-label="IP report summary">
            @foreach([
                ['label' => 'Total Records', 'value' => number_format($summary['total_ips']), 'icon' => 'inventory_2', 'tone' => 'bg-primary/10 text-primary'],
                ['label' => 'Granted / Registered', 'value' => number_format($summary['granted_ips']), 'icon' => 'verified', 'tone' => 'bg-green-100 text-green-700'],
                ['label' => 'Filed / Pending', 'value' => number_format($summary['pending_ips']), 'icon' => 'pending_actions', 'tone' => 'bg-amber-100 text-amber-700'],
                ['label' => 'Filed This Year', 'value' => number_format($summary['this_year_ips']), 'icon' => 'event', 'tone' => 'bg-blue-100 text-blue-700'],
                ['label' => 'Total Filing Cost', 'value' => 'RM '.number_format($summary['total_cost'], 2), 'icon' => 'payments', 'tone' => 'bg-purple-100 text-purple-700'],
            ] as $metric)
                <article class="flex items-center justify-between gap-3 rounded-xl border border-divider-subtle/30 bg-white p-5 shadow-sm">
                    <div><p class="text-sm font-medium text-divider-subtle">{{ $metric['label'] }}</p><p class="mt-2 text-2xl font-bold text-text-main">{{ $metric['value'] }}</p></div>
                    <span class="material-symbols-outlined rounded-lg p-3 text-2xl {{ $metric['tone'] }}" aria-hidden="true">{{ $metric['icon'] }}</span>
                </article>
            @endforeach
        </section>

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            <section class="overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm">
                <div class="border-b border-divider-subtle/20 px-5 py-4"><h2 class="text-lg font-bold text-text-main">Records by Category</h2></div>
                <div class="divide-y divide-divider-subtle/20">
                    @forelse($byCategory as $item)
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <span class="text-sm font-medium text-text-main">{{ str_replace('_', ' ', $item->label) }}</span>
                            <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-bold text-primary">{{ number_format($item->total) }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-sm text-divider-subtle">No category data available.</p>
                    @endforelse
                </div>
            </section>
            <section class="overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm">
                <div class="border-b border-divider-subtle/20 px-5 py-4"><h2 class="text-lg font-bold text-text-main">Records by Status</h2></div>
                <div class="divide-y divide-divider-subtle/20">
                    @forelse($byStatus as $item)
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <span class="text-sm font-medium text-text-main">{{ $item->label }}</span>
                            <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-bold text-primary">{{ number_format($item->total) }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-sm text-divider-subtle">No status data available.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
