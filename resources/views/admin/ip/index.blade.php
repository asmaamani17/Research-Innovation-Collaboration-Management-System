@extends('layouts.admin')

@section('title', 'Intellectual Properties Management')

@section('content')
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">Intellectual Properties</h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Manage patents, copyrights, trademarks, and other intellectual properties.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ip.export', request()->query()) }}"
                class="flex min-w-[120px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
                <span class="material-symbols-outlined text-sm">download</span>
                <span>Export</span>
            </a>
            <a href="{{ route('admin.ip.create') }}"
                class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>Add IP</span>
            </a>
        </div>
    </header>

    <div class="px-8 space-y-4">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-bold mb-1">Please fix these fields:</p>
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="px-8 my-4">
        <form method="GET" action="{{ route('admin.ip.index') }}"
            class="bg-white border border-divider-subtle/30 rounded-xl p-2 flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[260px] relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-divider-subtle">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <input name="search" value="{{ request('search') }}"
                    class="w-full h-11 pl-12 pr-4 bg-background-light border-none rounded-lg focus:ring-2 focus:ring-primary text-sm text-text-main placeholder:text-divider-subtle"
                    placeholder="Search by title, IP number, staff, project..." />
            </div>

            <select name="year"
                class="h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                <option value="">All Years</option>
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}</option>
                @endforeach
            </select>

            <select name="faculty_id"
                class="h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                <option value="">All Faculties</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" @selected((string) request('faculty_id') === (string) $faculty->id)>
                        {{ $faculty->faculty_code }} - {{ $faculty->faculty_name }}
                    </option>
                @endforeach
            </select>

            <select name="type"
                class="h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                <option value="">All Types</option>
                @foreach($ipTypes as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                @endforeach
            </select>

            <select name="status"
                class="h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                <option value="">All Statuses</option>
                @foreach($ipStatuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>

            <button type="submit"
                class="h-11 px-6 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-all">
                Search
            </button>

            <a href="{{ route('admin.ip.index') }}"
                class="h-11 px-6 bg-background-light text-text-main text-sm font-bold rounded-lg hover:bg-background-light/80 transition-all">
                Clear
            </a>
        </form>
    </div>

    <div class="px-8 pb-8">
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead class="bg-background-light">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-text-main uppercase tracking-wider">IP Number</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-text-main uppercase tracking-wider">Title</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-text-main uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-text-main uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-text-main uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-text-main uppercase tracking-wider">Filing Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-text-main uppercase tracking-wider">Country</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-text-main uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-divider-subtle/20">
                    @forelse($ips as $ip)
                        <tr class="hover:bg-background-light/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-text-main">{{ $ip->ip_number ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-text-main">{{ Str::limit($ip->title, 50) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    {{ $ip->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($ip->status === 'GRANTED') bg-green-100 text-green-700
                                    @elseif($ip->status === 'FILED') bg-yellow-100 text-yellow-700
                                    @elseif($ip->status === 'REGISTERED') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $ip->status ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-text-main">{{ $ip->staff->pluck('staff_name')->join(', ') ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-text-main">{{ $ip->filing_date ? $ip->filing_date->format('d M Y') : '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-text-main">{{ $ip->country ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.ip.show', $ip->id) }}"
                                        class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </a>
                                    <a href="{{ route('admin.ip.edit', $ip->id) }}"
                                        class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </a>
                                    <form action="{{ route('admin.ip.destroy', $ip->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this IP?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-4xl text-divider-subtle mb-2">inventory_2</span>
                                    <p class="text-sm text-divider-subtle">No intellectual properties found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ips->hasPages())
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-divider-subtle">
                    Showing {{ $ips->firstItem() }} to {{ $ips->lastItem() }} of {{ $ips->total() }} results
                </div>
                {{ $ips->links() }}
            </div>
        @endif
    </div>
@endsection
