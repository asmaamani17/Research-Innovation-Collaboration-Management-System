<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityStemProgram;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommunityStemProgramController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'submitted_at',
        'start_date',
        'project_id',
        'faculty',
        'program',
        'kategori',
        'classification',
    ];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:1900,2100'],
            'pusat' => ['nullable', 'string', 'max:255'],
            'klasifikasi' => ['nullable', 'in:Community,STEM,Industri'],
            'kategori' => ['nullable', 'string', 'max:255'],
            'cari' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'in:' . implode(',', self::SORTABLE_COLUMNS)],
            'dir' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'in:10,25,50,100'],
        ]);

        $query = CommunityStemProgram::query();
        if (!empty($filters['tahun'])) {
            $query->whereYear('start_date', $filters['tahun']);
        }
        if (!empty($filters['pusat'])) {
            $query->where('faculty', $filters['pusat']);
        }
        if (!empty($filters['klasifikasi'])) {
            $query->where('classification', $filters['klasifikasi']);
        }
        if (!empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }
        if (!empty($filters['cari'])) {
            $query->where('program', 'like', '%' . $filters['cari'] . '%');
        }

        $sortColumn = $filters['sort'] ?? 'submitted_at';
        $sortDirection = $filters['dir'] ?? 'desc';
        $query->orderBy($sortColumn, $sortDirection)->orderByDesc('id');

        $programs = $query->paginate($filters['per_page'] ?? 10)->withQueryString();
        $senaraiTahun = CommunityStemProgram::query()
            ->whereNotNull('start_date')
            ->selectRaw('DISTINCT YEAR(start_date) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');
        $senaraiPusat = CommunityStemProgram::query()
            ->whereNotNull('faculty')
            ->where('faculty', '!=', '')
            ->distinct()
            ->orderBy('faculty')
            ->pluck('faculty');
        $senaraiKategori = CommunityStemProgram::query()
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('admin.community,stem,industry.programs.index', compact(
            'programs',
            'senaraiTahun',
            'senaraiPusat',
            'senaraiKategori'
        ));
    }

    public function create()
    {
        return view('admin.community,stem,industry.programs.create', [
            'program' => new CommunityStemProgram(),
            'senaraiPusat' => $this->senaraiPusat(),
        ]);
    }

    public function bulkOcr()
    {
        $programs = CommunityStemProgram::query()
            ->whereNotNull('link_borang_a')
            ->where('link_borang_a', '!=', '')
            ->where(function (Builder $query) {
                $query->whereNull('brief_benefit')->orWhere('brief_benefit', '');
            })
            ->orderBy('project_id')
            ->get(['id', 'project_id', 'program', 'link_borang_a']);

        return view('admin.community,stem,industry.programs.ocr-pukal', compact('programs'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $beneficiaries = $data['beneficiaries'] ?? [];
        unset($data['beneficiaries']);

        $evidenceFile = null;
        if ($request->hasFile('evidence_file')) {
            $data['evidence_file'] = $request->file('evidence_file')->store('evidence', 'public');
            $evidenceFile = $data['evidence_file'];
        }
        $data['submitted_at'] = now();
        $data['status'] = 'New';

        try {
            DB::transaction(function () use ($data, $beneficiaries) {
                $program = CommunityStemProgram::create($data);
                $this->replaceBeneficiaries($program, $beneficiaries);
            });
        } catch (\Throwable $exception) {
            if ($evidenceFile) {
                Storage::disk('public')->delete($evidenceFile);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.stem.programs.index')
            ->with('success', 'Program record saved successfully.');
    }

    public function edit(CommunityStemProgram $program)
    {
        $program->load('beneficiaries');

        return view('admin.community,stem,industry.programs.edit', [
            'program' => $program,
            'senaraiPusat' => $this->senaraiPusat(),
        ]);
    }

    public function update(Request $request, CommunityStemProgram $program)
    {
        $data = $this->validatedData($request);
        $beneficiaries = $data['beneficiaries'] ?? [];
        unset($data['beneficiaries'], $data['evidence_file']);
        $previousEvidenceFile = $program->evidence_file;

        $newEvidenceFile = null;
        if ($request->hasFile('evidence_file')) {
            $newEvidenceFile = $request->file('evidence_file')->store('evidence', 'public');
            $data['evidence_file'] = $newEvidenceFile;
        }

        try {
            DB::transaction(function () use ($program, $data, $beneficiaries) {
                $program->update($data);
                $this->replaceBeneficiaries($program, $beneficiaries);
            });
        } catch (\Throwable $exception) {
            if ($newEvidenceFile) {
                Storage::disk('public')->delete($newEvidenceFile);
            }

            throw $exception;
        }

        if ($newEvidenceFile && $previousEvidenceFile) {
            Storage::disk('public')->delete($previousEvidenceFile);
        }

        return redirect()
            ->route('admin.stem.programs.index')
            ->with('success', 'Program record updated successfully.');
    }

    public function destroy(CommunityStemProgram $program)
    {
        $evidenceFile = $program->evidence_file;
        $program->delete();

        if ($evidenceFile) {
            Storage::disk('public')->delete($evidenceFile);
        }

        return redirect()
            ->route('admin.stem.programs.index')
            ->with('success', 'Program record deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'project_id' => ['nullable', 'string', 'max:20'],
            'staff_id' => ['required', 'string', 'max:50'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:5000'],
            'kategori' => ['nullable', 'string', 'max:255'],
            'classification' => ['required', 'in:Community,STEM,Industri'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'past_research_id' => ['nullable', 'string', 'max:255'],
            'community_industry_name' => ['nullable', 'string', 'max:255'],
            'organiser' => ['nullable', 'string', 'max:255'],
            'sponsorship_value' => ['nullable', 'string', 'max:255'],
            'brief_benefit' => ['nullable', 'string'],
            'star_rating' => ['nullable', 'integer', 'between:1,5'],
            'remarks' => ['nullable', 'string'],
            'link_evidence' => ['nullable', 'string', 'max:1000'],
            'link_borang_a' => ['nullable', 'string', 'max:1000'],
            'link_borang_b' => ['nullable', 'string', 'max:1000'],
            'link_borang_c' => ['nullable', 'string', 'max:1000'],
            'link_borang_d' => ['nullable', 'string', 'max:1000'],
            'link_kelulusan_kertas_kerja' => ['nullable', 'string', 'max:1000'],
            'combined_evidence_folder' => ['nullable', 'string', 'max:1000'],
            'evidence_file' => ['nullable', 'file', 'mimes:pdf', 'max:15360'],
            'beneficiaries' => ['nullable', 'array'],
            'beneficiaries.*' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function replaceBeneficiaries(CommunityStemProgram $program, array $beneficiaries): void
    {
        $program->beneficiaries()->delete();

        foreach (collect($beneficiaries)->map(fn ($name) => trim((string) $name))->filter() as $name) {
            $program->beneficiaries()->create(['name' => $name]);
        }
    }

    private function senaraiPusat()
    {
        return CommunityStemProgram::query()
            ->whereNotNull('faculty')
            ->where('faculty', '!=', '')
            ->distinct()
            ->orderBy('faculty')
            ->get(['faculty'])
            ->map(fn (CommunityStemProgram $program) => [
                'value' => $program->faculty,
                'label' => $program->faculty,
            ]);
    }
}
