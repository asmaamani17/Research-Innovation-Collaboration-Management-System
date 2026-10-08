<?php

namespace App\Exports;

use App\Models\CommunityStemProgram;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CommunityStemOverallExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection(): Collection
    {
        return CommunityStemProgram::query()
            ->with('beneficiaries')
            ->orderByDesc('submitted_at')
            ->get()
            ->map(fn (CommunityStemProgram $program) => [
                optional($program->submitted_at)->format('d-m-Y H:i'),
                $program->project_id,
                $program->staff_id,
                $program->status,
                $program->faculty,
                $program->program,
                $program->kategori,
                $program->classification,
                $program->start_date?->format('d-m-Y'),
                $program->end_date?->format('d-m-Y'),
                $program->past_research_id,
                $program->community_industry_name,
                $program->organiser,
                $program->sponsorship_value,
                $program->brief_benefit,
                $program->star_rating,
                $program->remarks,
                $program->combined_evidence_folder
                    ?: ($program->evidence_file
                        ? asset('storage/' . $program->evidence_file)
                        : $program->link_evidence),
                $program->beneficiaries->pluck('name')->implode('; '),
            ]);
    }

    public function headings(): array
    {
        return [
            'Tarikh Dihantar',
            'ID Projek',
            'Staff ID',
            'Status',
            'Fakulti',
            'Nama Program',
            'Kategori',
            'Klasifikasi',
            'Tarikh Mula',
            'Tarikh Tamat',
            'ID Projek/Inovasi Lepas',
            'Nama Komuniti/Industri',
            'Penganjur',
            'Nilai Tajaan (RM)',
            'Ringkasan Manfaat',
            'Penilaian Bintang',
            'Catatan',
            'Link Bukti',
            'Penerima Manfaat',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
