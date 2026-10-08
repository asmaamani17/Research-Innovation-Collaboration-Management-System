@extends('layouts.admin')

@section('title', 'Bulk OCR | Community, STEM & Industry')
@section('content')
  <div class="w-full pb-8 text-text-main">
    <header class="px-4 py-6 sm:px-8 sm:py-8">
      <div>
        <h1 class="text-2xl font-black leading-tight tracking-tight text-text-main sm:text-3xl">Bulk OCR</h1>
        <p class="mt-1 text-sm font-medium leading-5 text-divider-subtle">
          Program records with a Form A link and no benefit summary yet.
        </p>
      </div>
    </header>

    <div class="mx-4 mb-6 rounded-xl border border-divider-subtle/30 border-l-4 border-l-primary bg-white p-5 shadow-sm sm:mx-8 sm:p-6">
      <strong class="text-sm font-bold text-text-main">OCR processing is not configured in RICM.</strong>
      <p class="mt-2 text-sm leading-relaxed text-divider-subtle">
        This page is available in the sidebar, but processing remains disabled until the required OCR,
        PDF-rendering, and source-document integrations are configured.
      </p>
    </div>

    <div class="mx-4 mb-6 overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm sm:mx-8">
      <div class="border-b border-divider-subtle/30 px-5 py-4 sm:px-6">
        <strong class="text-sm font-bold text-text-main">{{ $programs->count() }} record(s) need a benefit summary</strong>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
          <thead class="bg-background-light">
            <tr>
              <th class="whitespace-nowrap border-b border-divider-subtle/30 px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main sm:px-6">Project ID</th>
              <th class="whitespace-nowrap border-b border-divider-subtle/30 px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main sm:px-6">Program</th>
              <th class="whitespace-nowrap border-b border-divider-subtle/30 px-5 py-3 text-xs font-bold uppercase tracking-wide text-text-main sm:px-6">Form A</th>
            </tr>
          </thead>
          <tbody>
            @forelse($programs as $program)
              <tr class="border-b border-divider-subtle/20 transition-colors last:border-0 hover:bg-background-light">
                <td class="px-5 py-4 text-text-main sm:px-6">{{ $program->project_id ?: '-' }}</td>
                <td class="px-5 py-4 text-text-main sm:px-6">{{ $program->program ?: '-' }}</td>
                <td class="px-5 py-4 sm:px-6">
                  <a class="font-semibold text-primary hover:underline" href="{{ $program->link_borang_a }}" target="_blank" rel="noopener noreferrer">
                    View Form A
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-5 py-6 text-center text-sm text-divider-subtle sm:px-6">
                  No records currently need a benefit summary.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
