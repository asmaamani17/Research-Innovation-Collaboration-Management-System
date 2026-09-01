@extends('layouts.admin')

@section('title', 'Faculty Staff')

@section('content')
    <!-- Page Header -->
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">
                {{ $faculty->faculty_name }} - Staff Members
            </h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Manage staff members in this faculty.
            </p>
        </div>
        <a href="{{ route('admin.faculty.show', $faculty->id) }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-white border border-divider-subtle/40 text-text-main text-sm font-bold shadow-sm hover:bg-background-light transition-all">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Back to Faculty</span>
        </a>
    </header>

    <div class="px-8 pb-8">
        <!-- Data Table Section -->
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-background-light">
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">STAFF ID</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">STAFF NAME</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">AWARDS</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($staff as $staffMember)
                            <tr class="hover:bg-background-light/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-primary">{{ $staffMember->staff_id ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                            {{ strtoupper(substr($staffMember->staff_name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-medium text-text-main">{{ $staffMember->staff_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($staffMember->awards_count > 0)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                            {{ $staffMember->awards_count }} awards
                                        </span>
                                    @else
                                        <span class="text-xs text-divider-subtle">No awards</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.staff.show', $staffMember->id) }}" class="size-8 rounded flex items-center justify-center text-blue-500 hover:bg-blue-50/10 transition-colors" title="View Details">
                                            <span class="material-symbols-outlined text-xl">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-divider-subtle">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-4xl">person_off</span>
                                        <span>No staff members found</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
