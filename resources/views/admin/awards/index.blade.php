@extends('layouts.admin')

@section('title', 'Awards Management')

@php
    $awardStyles = [
        'GOLD' => ['text' => 'text-yellow-600', 'icon' => 'text-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-700'],
        'SILVER' => ['text' => 'text-gray-600', 'icon' => 'text-gray-400', 'badge' => 'bg-gray-100 text-gray-700'],
        'BRONZE' => ['text' => 'text-orange-700', 'icon' => 'text-orange-600', 'badge' => 'bg-orange-100 text-orange-700'],
        'SPECIAL' => ['text' => 'text-primary', 'icon' => 'text-primary', 'badge' => 'bg-primary/10 text-primary'],
        'PLATINUM' => ['text' => 'text-cyan-700', 'icon' => 'text-cyan-500', 'badge' => 'bg-cyan-100 text-cyan-700'],
    ];
@endphp

@section('content')
    <header class="px-8 py-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h2 class="text-text-main text-3xl font-black leading-tight tracking-tight">Awards Management</h2>
            <p class="text-divider-subtle font-medium text-sm mt-1">
                Manage real award records from staff, projects, and events.
            </p>
        </div>
        <button type="button" onclick="openAddAwardModal()"
            class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>Add Award</span>
        </button>
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
        <form method="GET" action="{{ route('admin.awards') }}"
            class="bg-white border border-divider-subtle/30 rounded-xl p-2 flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[260px] relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-divider-subtle">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <input name="search" value="{{ request('search') }}"
                    class="w-full h-11 pl-12 pr-4 bg-background-light border-none rounded-lg focus:ring-2 focus:ring-primary text-sm text-text-main placeholder:text-divider-subtle"
                    placeholder="Search by award, staff, project, event, faculty..." />
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

            <select name="level"
                class="h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                <option value="">All Levels</option>
                @foreach($awardLevels as $level)
                    <option value="{{ $level }}" @selected(request('level') === $level)>{{ $level }}</option>
                @endforeach
            </select>

            <select name="type"
                class="h-11 px-4 bg-background-light border-none rounded-lg text-sm text-text-main focus:ring-2 focus:ring-primary">
                <option value="">All Types</option>
                @foreach($awardTypes as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                @endforeach
            </select>

            <button type="submit"
                class="h-11 px-4 flex items-center gap-2 border border-divider-subtle/40 rounded-lg text-sm font-medium text-text-main hover:bg-background-light transition-colors">
                <span class="material-symbols-outlined text-lg">filter_list</span>
                Filter
            </button>
            <a href="{{ route('admin.awards') }}"
                class="h-11 px-4 flex items-center rounded-lg text-sm font-medium text-divider-subtle hover:bg-background-light transition-colors">
                Reset
            </a>
        </form>
    </div>

    <div class="px-8 pb-8 flex-1">
        <div class="bg-white border border-divider-subtle/30 rounded-xl overflow-hidden shadow-sm flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-background-light">
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">Staff Name
                            </th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">Faculty
                            </th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">Project
                                Title</th>
                            <th
                                class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-center">
                                Award</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">Level</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">Type</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">Event Name
                            </th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">National /
                                International</th>
                            <th class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle">Award Date
                            </th>
                            <th
                                class="px-6 py-4 text-sm font-bold text-text-main border-b border-divider-subtle text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-subtle/20">
                        @forelse($awards as $award)
                            @php
                                $awardKey = strtoupper($award->award_name ?? 'AWARD');
                                $style = $awardStyles[$awardKey] ?? ['text' => 'text-text-main', 'icon' => 'text-primary', 'badge' => 'bg-primary/10 text-primary'];
                                $staffName = $award->staff->staff_name ?? 'Unknown Staff';
                            @endphp
                            <tr class="hover:bg-background-light/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 min-w-[220px]">
                                        <div class="size-8 rounded-full bg-gray-200 bg-cover shrink-0"
                                            style="background-image: url('https://ui-avatars.com/api/?name={{ rawurlencode($staffName) }}&background=184290&color=fff')">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-text-main">{{ $staffName }}</p>
                                            <p class="text-xs text-divider-subtle">{{ $award->staff->staff_id ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded bg-primary/10 text-primary text-[10px] font-bold">
                                        {{ $award->staff->faculty->faculty_code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80 min-w-[240px]">
                                    {{ $award->project->project_title ?? 'Unknown Project' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined {{ $style['icon'] }} text-lg">emoji_events</span>
                                        <span class="text-sm font-bold {{ $style['text'] }}">{{ $award->award_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-[10px] font-bold">{{ $award->award_level }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80">{{ $award->award_type }}</td>
                                <td class="px-6 py-4 text-sm text-text-main/80 min-w-[260px]">
                                    {{ $award->event->event_name ?? 'Unknown Event' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded {{ strtoupper($award->event->exhibition_level ?? '') === 'INTERNATIONAL' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }} text-[10px] font-bold">
                                        {{ $award->event->exhibition_level ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-main/80 whitespace-nowrap">
                                    {{ optional($award->award_date)->format('M d, Y') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($award->evidence_url)
                                            <a href="{{ str_starts_with($award->evidence_url, 'http') ? $award->evidence_url : asset($award->evidence_url) }}"
                                                target="_blank"
                                                class="size-8 rounded flex items-center justify-center text-blue-500 hover:bg-blue-50 transition-colors"
                                                title="View Evidence">
                                                <span class="material-symbols-outlined text-xl">link</span>
                                            </a>
                                        @endif
                                        <button type="button"
                                            class="size-8 rounded flex items-center justify-center text-primary hover:bg-primary/10 transition-colors"
                                            title="Edit" data-id="{{ $award->id }}" data-staff-id="{{ $award->staff_id }}"
                                            data-staff-label="{{ e(trim(($award->staff->staff_id ?? '') . ' - ' . ($award->staff->staff_name ?? ''))) }}"
                                            data-project-id="{{ $award->project_id }}"
                                            data-project-label="{{ e(trim(($award->project->project_id ?? '') . ' - ' . ($award->project->project_title ?? ''))) }}"
                                            data-event-id="{{ $award->event_id }}"
                                            data-event-label="{{ e($award->event->event_name ?? '') }}"
                                            data-event-national-level="{{ e(strtoupper($award->event->national_level ?? '')) }}"
                                            data-award-name="{{ e($award->award_name) }}"
                                            data-award-level="{{ e($award->award_level) }}"
                                            data-award-type="{{ e($award->award_type) }}"
                                            data-award-date="{{ optional($award->award_date)->format('Y-m-d') }}"
                                            onclick="openEditAwardModal(this)">
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('admin.award.destroy', $award->id) }}"
                                            onsubmit="return confirm('Delete this award record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="size-8 rounded flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center text-sm text-divider-subtle">
                                    No award records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="px-6 py-4 bg-background-light/30 border-t border-divider-subtle/30 flex flex-wrap items-center justify-between gap-3 mt-auto">
                <!-- pagination and footer omitted for brevity -->
            </div>
        </div>
    </div>
@endsection