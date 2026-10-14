<?php

namespace App\Exports;

use App\Models\CommunityStemProgram;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CommunityStemMyRaExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection(): Collection
    {
        $rows = collect();
        $number = 0;

        CommunityStemProgram::with('beneficiaries')->orderBy('project_id')->each(
            function (CommunityStemProgram $program) use ($rows, &$number) {
                $shared = [
                    'project_id' => $program->project_id,
                    'staff_id' => $program->staff_id,
                    'faculty' => $program->faculty,
                    'past_research_id' => $program->past_research_id,
                    'kategori' => $program->kategori,
                    'organiser' => $program->organiser,
                    'sponsorship_value' => $program->sponsorship_value,
                    'program' => $program->program,
                    'brief_benefit' => $program->brief_benefit,
                    'star_rating' => $program->star_rating,
                    'start_date' => $program->start_date?->format('d-m-Y'),
                    'end_date' => $program->end_date?->format('d-m-Y'),
                    'evidence' => $program->combined_evidence_folder
                        ?: ($program->evidence_file
                            ? asset('storage/' . $program->evidence_file)
                            : $program->link_evidence),
                    'remarks' => $program->remarks,
                    'year' => $program->start_date?->format('Y'),
                ];

                $rows->push($this->row(++$number, $program->status, $program->community_industry_name, $shared));

                foreach ($program->beneficiaries as $beneficiary) {
                    $rows->push($this->row(++$number, 'Benefited', $beneficiary->name, $shared));
                }
            }
        );

        return $rows;
    }

    private function row(int $number, ?string $status, ?string $communityName, array $shared): array
    {
        return [
            $number,
            $shared['project_id'],
            $shared['staff_id'],
            $status,
            $shared['faculty'],
            $shared['past_research_id'],
            $shared['kategori'],
            $communityName,
            $shared['organiser'],
            $shared['sponsorship_value'],
            $shared['program'],
            $shared['brief_benefit'],
            $shared['star_rating'],
            $shared['start_date'],
            $shared['end_date'],
            $shared['evidence'],
            $shared['remarks'],
            $shared['year'],
        ];
    }

    public function headings(): array
    {
        return [
            'NO',
            'PROJECT ID',
            'STAFF ID',
            'STATUS',
            'FACULTY / CENTRE',
            'PAST RELATED RESEARCH PROJECT/ INNOVATION ID',
            'RELATED RESEARCH PROJECT / INNOVATION',
            'COMMUNITY / INDUSTRY NAME',
            'ORGANISER/SPONSOR/FUNDER / COLLABORATOR',
            'VALUE OF SPONSORSHIP (IF APPLICABLE) IN RM',
            'SOCIAL INNOVATION PROGRAM NAME',
            'BRIEF BENEFIT',
            'STAR RATING (for benefit)',
            'START DATE (dd-mm-yyyy)',
            'END DATE (dd-mm-yyyy)',
            'LINK TO EVIDENCE',
            'REMARKS',
            'TAHUN',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
