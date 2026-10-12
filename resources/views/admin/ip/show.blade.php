@extends('layouts.admin')

@section('title', $ip->nama_produk_baru ?: $ip->title)

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.ip.index') }}" class="text-primary hover:text-primary/80 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                    {{ $ip->nama_produk_baru ?: $ip->title }}
                </h2>
            </div>
            <p class="text-divider-subtle font-medium text-sm">
                @php
                    $typeColors = [
                        'PATENT' => 'bg-blue-100 text-blue-700',
                        'Patent' => 'bg-blue-100 text-blue-700',
                        'TRADEMARK' => 'bg-purple-100 text-purple-700',
                        'Trademark' => 'bg-purple-100 text-purple-700',
                        'COPYRIGHT' => 'bg-green-100 text-green-700',
                        'Copyright' => 'bg-green-100 text-green-700',
                        'INDUSTRIAL_DESIGN' => 'bg-orange-100 text-orange-700',
                        'Industrial Design' => 'bg-orange-100 text-orange-700',
                        'UTILITY_INNOVATION' => 'bg-cyan-100 text-cyan-700',
                        'Utility Innovation' => 'bg-cyan-100 text-cyan-700',
                    ];
                    $color = $typeColors[$ip->type] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $color }}">
                    {{ str_replace('_', ' ', $ip->type) }}
                </span>
                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-primary/10 text-primary">
                    {{ str_replace('_', ' ', $ip->category) }}
                </span>
                @if($ip->status)
                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                    {{ $ip->status }}
                </span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.ip.edit', $ip->id) }}"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium shadow-lg">
                <span class="material-symbols-outlined">edit</span>
                <span>Edit</span>
            </a>
        </div>
    </header>

    <!-- Two Column Layout -->
    <div class="px-8 pb-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- IP Details Section -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">IP Details</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="size-16 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-4xl text-primary">inventory_2</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-text-main">{{ $ip->nama_produk_baru ?: $ip->title }}</h4>
                            <p class="text-sm text-text-main/80 mt-1">
                                IP Type: <span class="font-semibold">{{ str_replace('_', ' ', $ip->type) }}</span>
                            </p>
                            <p class="text-sm text-text-main/80 mt-1">
                                Category: <span class="font-semibold">{{ str_replace('_', ' ', $ip->category) }}</span>
                            </p>
                            @if($ip->ip_number)
                            <p class="text-sm text-text-main/80 mt-1">
                                Filing Number: <span class="font-semibold">{{ $ip->ip_number }}</span>
                            </p>
                            @endif
                        </div>
                    </div>
                    @if($ip->status)
                    <div class="pt-4 border-t border-divider-subtle/30">
                        <p class="text-sm text-divider-subtle mb-1">Status</p>
                        <p class="text-text-main font-medium">{{ $ip->status }}</p>
                    </div>
                    @endif
                    @if($ip->filing_date)
                    <div class="pt-4 border-t border-divider-subtle/30">
                        <p class="text-sm text-divider-subtle mb-1">Filing Date</p>
                        <p class="text-text-main font-medium">{{ \Carbon\Carbon::parse($ip->filing_date)->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Abstract Section -->
            @if($ip->abstrak)
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Abstract</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-text-main whitespace-pre-line">{{ $ip->abstrak }}</p>
                </div>
            </div>
            @endif

            <!-- Filing & Renewal Information -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Filing & Renewal Information</h3>
                </div>
                <div class="divide-y divide-divider-subtle/20">
                    @if($ip->grant_date)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Grant Date</p>
                        <p class="text-text-main font-medium">{{ \Carbon\Carbon::parse($ip->grant_date)->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($ip->expiry_date)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Expiry Date</p>
                        <p class="text-text-main font-medium">{{ \Carbon\Carbon::parse($ip->expiry_date)->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($ip->tarikh_notis)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Notice Date</p>
                        <p class="text-text-main font-medium">{{ \Carbon\Carbon::parse($ip->tarikh_notis)->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($ip->tarikh_pewartaan)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Gazette Date</p>
                        <p class="text-text-main font-medium">{{ \Carbon\Carbon::parse($ip->tarikh_pewartaan)->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($ip->tarikh_renewal_terakhir)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Last Renewal Date</p>
                        <p class="text-text-main font-medium">{{ \Carbon\Carbon::parse($ip->tarikh_renewal_terakhir)->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($ip->tarikh_renewal_seterusnya)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Next Renewal Date</p>
                        <p class="text-text-main font-medium">{{ \Carbon\Carbon::parse($ip->tarikh_renewal_seterusnya)->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($ip->kos !== null)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Filing Cost</p>
                        <p class="text-text-main font-medium">RM {{ number_format((float) $ip->kos, 2) }}</p>
                    </div>
                    @endif
                    @if($ip->kos_renewal !== null)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Renewal Cost</p>
                        <p class="text-text-main font-medium">RM {{ number_format((float) $ip->kos_renewal, 2) }}</p>
                    </div>
                    @endif
                    @if($ip->ejen)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Agent</p>
                        <p class="text-text-main font-medium">{{ $ip->ejen }}</p>
                    </div>
                    @endif
                    @if($ip->country)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Country</p>
                        <p class="text-text-main font-medium">{{ $ip->country }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Remarks -->
            @if($ip->remarks)
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Remarks</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-text-main whitespace-pre-line">{{ $ip->remarks }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Researcher Information -->
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Researcher Information</h3>
                </div>
                <div class="divide-y divide-divider-subtle/20">
                    @if($ip->nama_penyelidik)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Researcher Name</p>
                        <p class="text-text-main font-medium">{{ $ip->nama_penyelidik }}</p>
                    </div>
                    @endif
                    @if($ip->penyelidik_bersama)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Co-researchers</p>
                        <p class="text-text-main font-medium whitespace-pre-line">{{ $ip->penyelidik_bersama }}</p>
                    </div>
                    @endif
                    @if($ip->gelaran)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Title</p>
                        <p class="text-text-main font-medium">{{ $ip->gelaran }}</p>
                    </div>
                    @endif
                    @if($ip->jawatan)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Position</p>
                        <p class="text-text-main font-medium">{{ $ip->jawatan }}</p>
                    </div>
                    @endif
                    @if($ip->no_staf)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Staff ID</p>
                        <p class="text-text-main font-medium">{{ $ip->no_staf }}</p>
                    </div>
                    @endif
                    @if($ip->fakulti)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Faculty</p>
                        <p class="text-text-main font-medium">{{ $ip->fakulti }}</p>
                    </div>
                    @endif
                    @if($ip->coe)
                    <div class="px-6 py-4">
                        <p class="text-sm text-divider-subtle mb-1">Centre of Excellence</p>
                        <p class="text-text-main font-medium">{{ $ip->coe }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Linked Staff -->
            @if($ip->staff && $ip->staff->count() > 0)
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Linked Staff</h3>
                </div>
                <div class="divide-y divide-divider-subtle/20">
                    @foreach($ip->staff as $staff)
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                {{ strtoupper(substr($staff->staff_name ?? 'Unknown', 0, 2)) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-text-main font-medium">{{ $staff->staff_name }}</p>
                                <p class="text-sm text-divider-subtle">{{ $staff->staff_id }}</p>
                                @if($staff->faculty)
                                <p class="text-sm text-divider-subtle">{{ $staff->faculty->faculty_code }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Linked Project -->
            @if($ip->project)
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Linked Project</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl text-primary">biotech</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-text-main font-medium">{{ $ip->project->project_title }}</p>
                            <p class="text-sm text-divider-subtle mt-1">{{ $ip->project->project_id }}</p>
                            @if($ip->project->grant_no)
                            <p class="text-sm text-divider-subtle mt-1">Grant: {{ $ip->project->grant_no }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Grant -->
            @if($ip->geran)
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Grant</h3>
                </div>
                <div class="p-6">
                    <p class="text-text-main font-medium">{{ $ip->geran }}</p>
                </div>
            </div>
            @endif

            <!-- Evidence Link -->
            @if($ip->link_to_evidence)
            <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-divider-subtle/30 bg-background-light/50">
                    <h3 class="text-lg font-bold text-text-main">Evidence</h3>
                </div>
                <div class="p-6">
                    <a href="{{ $ip->link_to_evidence }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-primary hover:underline break-all">
                        <span class="material-symbols-outlined text-lg">link</span>
                        <span class="text-sm">View Evidence</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Delete Action -->
            <form action="{{ route('admin.ip.destroy', $ip->id) }}" method="POST" onsubmit="return confirm('Delete this IP record?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 border border-red-200 bg-white text-red-700 rounded-lg hover:bg-red-50 transition-colors font-medium">
                    <span class="material-symbols-outlined">delete</span>
                    <span>Delete Record</span>
                </button>
            </form>
        </div>
    </div>
@endsection
