<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityStemProgram;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommunityStemDuplicateController extends Controller
{
    public function preview()
    {
        $groups = $this->duplicateGroups();

        return response()->json([
            'groups' => $groups->map(function (Collection $records) {
                $keep = $this->recordToKeep($records);

                return [
                    'keep' => [
                        'project_id' => $keep->project_id,
                        'program' => $keep->program,
                    ],
                    'remove' => $records
                        ->reject(fn (CommunityStemProgram $record) => $record->is($keep))
                        ->map(fn (CommunityStemProgram $record) => [
                            'id' => $record->id,
                            'project_id' => $record->project_id,
                            'program' => $record->program,
                        ])
                        ->values(),
                ];
            })->values(),
        ]);
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'duplicate_ids' => ['required', 'array', 'min:1'],
            'duplicate_ids.*' => ['required', 'integer'],
        ]);
        $selectedIds = collect($validated['duplicate_ids'])->map(fn ($id) => (int) $id)->all();

        [$deletedCount, $evidenceFiles] = DB::transaction(function () use ($selectedIds) {
            $deletedCount = 0;
            $evidenceFiles = [];

            foreach ($this->duplicateGroups() as $records) {
                $keep = $this->recordToKeep($records);
                $records
                    ->reject(fn (CommunityStemProgram $record) => $record->is($keep))
                    ->filter(fn (CommunityStemProgram $record) => in_array($record->id, $selectedIds, true))
                    ->each(function (CommunityStemProgram $record) use (&$deletedCount, &$evidenceFiles) {
                        if ($record->evidence_file) {
                            $evidenceFiles[] = $record->evidence_file;
                        }
                        $record->delete();
                        $deletedCount++;
                    });
            }

            return [$deletedCount, $evidenceFiles];
        });

        if ($evidenceFiles !== []) {
            Storage::disk('public')->delete($evidenceFiles);
        }

        return redirect()
            ->route('admin.stem.programs.index')
            ->with('success', "{$deletedCount} duplicate program record(s) removed.");
    }

    private function duplicateGroups(): Collection
    {
        return CommunityStemProgram::query()
            ->whereNotNull('submitted_at')
            ->whereNotNull('staff_id')
            ->where('staff_id', '!=', '')
            ->get()
            ->groupBy(fn (CommunityStemProgram $program) => strtolower(trim($program->staff_id))
                . '|' . $program->submitted_at->format('Y-m-d H:i:s'))
            ->filter(fn (Collection $records) => $records->count() > 1);
    }

    private function recordToKeep(Collection $records): CommunityStemProgram
    {
        return $records->sort(function (CommunityStemProgram $a, CommunityStemProgram $b) {
            $scoreDifference = $this->completenessScore($b) <=> $this->completenessScore($a);

            return $scoreDifference !== 0 ? $scoreDifference : $a->id <=> $b->id;
        })->first();
    }

    private function completenessScore(CommunityStemProgram $program): int
    {
        return collect([
            'organiser', 'sponsorship_value', 'brief_benefit', 'star_rating',
            'remarks', 'link_evidence', 'link_borang_a', 'link_borang_b',
            'link_borang_c', 'link_borang_d', 'link_kelulusan_kertas_kerja',
            'combined_evidence_folder', 'community_industry_name', 'kategori',
        ])->filter(fn (string $field) => filled($program->{$field}))->count();
    }
}
