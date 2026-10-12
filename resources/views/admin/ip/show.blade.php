@extends('layouts.admin')

@section('title', 'IP Record Details')

@section('content')
    <header class="flex flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-8 sm:py-8">
        <div>
            <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">IP Record Details</h1>
            <p class="mt-1 text-sm font-medium text-divider-subtle">{{ $ip->nama_produk_baru ?: $ip->title }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.ip.edit', $ip->id) }}" class="inline-flex h-11 items-center gap-2 rounded-lg bg-primary px-4 text-sm font-bold text-white transition hover:bg-primary/90">
                <span class="material-symbols-outlined text-lg" aria-hidden="true">edit</span> Edit
            </a>
            <a href="{{ route('admin.ip.index') }}" class="inline-flex h-11 items-center gap-2 rounded-lg border border-divider-subtle/40 bg-white px-4 text-sm font-bold text-text-main transition hover:bg-background-light">
                <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_back</span> Records
            </a>
        </div>
    </header>

    <div class="mx-auto max-w-6xl space-y-5 px-4 pb-8 sm:px-8">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700" role="status">{{ session('success') }}</div>
        @endif
        @php
            $sections = [
                'Record Details' => [
                    'Category' => str_replace('_', ' ', $ip->category ?: $ip->type),
                    'IP Type' => str_replace('_', ' ', $ip->type),
                    'Product / IP Name' => $ip->title,
                    'New Product Name' => $ip->nama_produk_baru,
                    'Filing Number' => $ip->ip_number,
                    'Status' => $ip->status,
                    'Record Number' => $ip->bil,
                    'Year' => $ip->tahun ?: $ip->filing_date?->year,
                    'Activity Year' => $ip->tahun_aktiviti,
                ],
                'Researchers & Project' => [
                    'Researcher' => $ip->nama_penyelidik ?: $ip->staff->pluck('staff_name')->join(', '),
                    'Co-researchers / Contribution' => $ip->penyelidik_bersama,
                    'Faculty' => $ip->fakulti ?: $ip->staff->map(fn ($staff) => $staff->faculty?->faculty_code)->filter()->unique()->join(', '),
                    'Staff ID' => $ip->no_staf ?: $ip->staff->pluck('staff_id')->join(', '),
                    'Centre of Excellence (CoE)' => $ip->coe,
                    'Position' => $ip->jawatan,
                    'Researcher Title' => $ip->gelaran,
                    'Linked Project' => $ip->project ? $ip->project->project_id.' - '.$ip->project->project_title : null,
                    'Grant' => $ip->geran ?: $ip->project?->grant_no,
                ],
                'Filing & Renewal' => [
                    'Filing Date' => $ip->filing_date?->format('d M Y'),
                    'Grant Date' => $ip->grant_date?->format('d M Y'),
                    'Expiry Date' => $ip->expiry_date?->format('d M Y'),
                    'Notice Date' => $ip->tarikh_notis?->format('d M Y'),
                    'Gazette Date' => $ip->tarikh_pewartaan?->format('d M Y'),
                    'Gazette / Publication' => $ip->pewartaan,
                    'Filing Cost' => $ip->kos !== null ? 'RM '.number_format((float) $ip->kos, 2) : null,
                    'Last Renewal' => $ip->tarikh_renewal_terakhir?->format('d M Y'),
                    'Next Renewal' => $ip->tarikh_renewal_seterusnya?->format('d M Y'),
                    'Renewal Cost' => $ip->kos_renewal !== null ? 'RM '.number_format((float) $ip->kos_renewal, 2) : null,
                    'Agent' => $ip->ejen,
                    'Country' => $ip->country,
                ],
            ];
        @endphp

        @foreach($sections as $title => $items)
            <section class="overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm">
                <div class="border-b border-divider-subtle/20 bg-background-light px-5 py-4"><h2 class="text-lg font-bold text-text-main">{{ $title }}</h2></div>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-5 p-5 sm:grid-cols-2 xl:grid-cols-3 sm:p-6">
                    @foreach($items as $label => $value)
                        <div class="{{ in_array($label, ['Product / IP Name', 'New Product Name', 'Co-researchers / Contribution', 'Linked Project']) ? 'sm:col-span-2' : '' }}">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-divider-subtle">{{ $label }}</dt>
                            <dd class="mt-1 whitespace-pre-line break-words text-sm font-medium text-text-main">{{ filled($value) ? $value : '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endforeach

        @if($ip->abstrak || $ip->link_to_evidence || $ip->remarks || $ip->kategori)
            <section class="overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm">
                <div class="border-b border-divider-subtle/20 bg-background-light px-5 py-4"><h2 class="text-lg font-bold text-text-main">Additional Information</h2></div>
                <div class="space-y-5 p-5 sm:p-6">
                    @foreach(['Subcategory' => $ip->kategori, 'Abstract' => $ip->abstrak, 'Remarks' => $ip->remarks] as $label => $value)
                        @if(filled($value))
                            <div><h3 class="text-xs font-semibold uppercase tracking-wide text-divider-subtle">{{ $label }}</h3><p class="mt-1 whitespace-pre-line text-sm text-text-main">{{ $value }}</p></div>
                        @endif
                    @endforeach
                    @if($ip->link_to_evidence)
                        <div><h3 class="text-xs font-semibold uppercase tracking-wide text-divider-subtle">Evidence</h3><a href="{{ $ip->link_to_evidence }}" target="_blank" rel="noopener noreferrer" class="mt-1 inline-block break-all text-sm font-medium text-primary hover:underline">{{ $ip->link_to_evidence }}</a></div>
                    @endif
                </div>
            </section>
        @endif

        <div class="flex justify-end">
            <form action="{{ route('admin.ip.destroy', $ip->id) }}" method="POST" onsubmit="return confirm('Delete this IP record?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-lg border border-red-200 bg-white px-5 text-sm font-bold text-red-700 transition hover:bg-red-50">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">delete</span> Delete Record
                </button>
            </form>
        </div>
    </div>
@endsection
