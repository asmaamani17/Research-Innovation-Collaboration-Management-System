<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\IntellectualProperty;
use App\Models\Project;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class IpController extends Controller
{
    private const CATEGORIES = [
        'PERMOHONAN',
        'PATENT',
        'PATENT_GRANTED',
        'UTILITY_INNOVATION',
        'COPYRIGHT',
        'TRADEMARK',
        'INDUSTRIAL_DESIGN',
    ];

    private const TYPES = [
        'PATENT',
        'UTILITY_INNOVATION',
        'COPYRIGHT',
        'TRADEMARK',
        'INDUSTRIAL_DESIGN',
    ];

    public function index(Request $request): View
    {
        session(['workspace' => 'ip']);

        $ips = $this->filteredQuery($request)
            ->with(['project', 'staff.faculty'])
            ->orderByDesc('filing_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.ip.index', array_merge([
            'ips' => $ips,
        ], $this->filterOptions()));
    }

    public function create(): View
    {
        session(['workspace' => 'ip']);

        return view('admin.ip.create', [
            'ip' => new IntellectualProperty,
            'staff' => Staff::with('faculty')->orderBy('staff_name')->get(),
            'projects' => Project::orderBy('project_title')->get(),
            'categories' => self::CATEGORIES,
            'types' => self::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $staffIds = $data['staff_ids'] ?? [];
        unset($data['staff_ids']);
        $data['status_produk'] = $data['status_produk'] ?? $data['status'] ?? null;

        $ip = IntellectualProperty::create($data);
        $ip->staff()->sync($staffIds);

        return redirect()->route('admin.ip.index')->with('success', 'Intellectual property added successfully.');
    }

    public function show($id): View
    {
        session(['workspace' => 'ip']);
        $ip = IntellectualProperty::with(['project', 'staff.faculty'])->findOrFail($id);

        return view('admin.ip.show', compact('ip'));
    }

    public function edit($id): View
    {
        session(['workspace' => 'ip']);
        $ip = IntellectualProperty::with('staff')->findOrFail($id);

        return view('admin.ip.edit', [
            'ip' => $ip,
            'staff' => Staff::with('faculty')->orderBy('staff_name')->get(),
            'projects' => Project::orderBy('project_title')->get(),
            'categories' => self::CATEGORIES,
            'types' => self::TYPES,
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $ip = IntellectualProperty::findOrFail($id);
        $data = $this->validated($request);
        $staffIds = $data['staff_ids'] ?? [];
        unset($data['staff_ids']);
        $data['status_produk'] = $data['status_produk'] ?? $data['status'] ?? null;

        $ip->update($data);
        $ip->staff()->sync($staffIds);

        return redirect()->route('admin.ip.show', $ip->id)->with('success', 'Intellectual property updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $ip = IntellectualProperty::findOrFail($id);
        $ip->staff()->detach();
        $ip->delete();

        return redirect()->route('admin.ip.index')->with('success', 'Intellectual property deleted successfully.');
    }

    public function dashboard(Request $request): View
    {
        session(['workspace' => 'ip']);

        $filtered = $this->filteredQuery($request);
        $trendRecords = $this->filteredQuery($request, true)
            ->with('staff.faculty')
            ->get(['id', 'category', 'type', 'filing_date', 'tahun', 'fakulti']);
        $thisYear = (clone $this->filteredQuery($request, true))
            ->where(function (Builder $query) {
                $query->where('tahun', now()->year)
                    ->orWhereBetween('filing_date', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()]);
            })
            ->count();

        $categoryChart = (clone $filtered)
            ->selectRaw("COALESCE(NULLIF(category, ''), type) as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();
        $statusChart = (clone $filtered)
            ->selectRaw("COALESCE(NULLIF(status, ''), 'Not Set') as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        $yearCounts = [];
        foreach ($trendRecords as $record) {
            $year = $record->tahun ?: optional($record->filing_date)->year;
            if ($year) {
                $yearCounts[$year] = ($yearCounts[$year] ?? 0) + 1;
            }
        }
        ksort($yearCounts);

        return view('admin.ip.dashboard', array_merge([
            'stats' => [
                'total_ips' => (clone $filtered)->count(),
                'total_staff' => Staff::count(),
                'total_projects' => Project::count(),
                'this_year_ips' => $thisYear,
                'total_cost' => (float) (clone $filtered)->sum('kos'),
            ],
            'categoryChart' => $categoryChart,
            'statusChart' => $statusChart,
            'yearChart' => collect($yearCounts)->map(fn ($total, $year) => ['label' => $year, 'total' => $total])->values(),
            'facultyChart' => $this->facultyCounts($filtered->with('staff.faculty')->get()),
        ], $this->filterOptions()));
    }

    public function import(): View
    {
        session(['workspace' => 'ip']);

        return view('admin.ip.import.index', ['categories' => self::CATEGORIES]);
    }

    public function importStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:20480'],
            'category' => ['required', 'in:'.implode(',', self::CATEGORIES)],
            'mode' => ['required', 'in:append,replace'],
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['file' => 'The uploaded file could not be read. Please check that it is a valid Excel or CSV file.']);
        }

        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), array_shift($rows) ?? []);
        $headerMap = $this->importHeaderMap($headers);

        if (! isset($headerMap['title'])) {
            return back()->withErrors(['file' => 'The file must include a Product Name or Title column.']);
        }

        $records = [];
        $errors = [];
        foreach ($rows as $index => $row) {
            if (collect($row)->every(fn ($value) => $value === null || trim((string) $value) === '')) {
                continue;
            }

            $record = [
                'category' => $validated['category'],
                'type' => null,
            ];
            foreach ($headerMap as $field => $columnIndex) {
                $value = $row[$columnIndex] ?? null;
                if (is_string($value)) {
                    $value = trim($value);
                }
                if ($value === '' || $value === '-') {
                    $value = null;
                }
                if ($field === 'category' && $value === null) {
                    continue;
                }
                $record[$field] = $value;
            }

            $record['category'] = strtoupper(str_replace([' ', '-'], '_', (string) $record['category']));
            if (empty($record['type'])) {
                $record['type'] = in_array($record['category'], self::TYPES, true)
                    ? $record['category']
                    : 'PATENT';
            }
            if (! empty($record['type'])) {
                $record['type'] = strtoupper(str_replace([' ', '-'], '_', (string) $record['type']));
            }
            if (empty($record['tahun']) && ! empty($record['filing_date'])) {
                try {
                    $record['tahun'] = $this->importDate($record['filing_date'])->year;
                } catch (Throwable) {
                    $record['tahun'] = null;
                }
            }
            foreach (['filing_date', 'grant_date', 'expiry_date', 'tarikh_notis', 'tarikh_pewartaan', 'tarikh_renewal_terakhir', 'tarikh_renewal_seterusnya'] as $dateField) {
                if (! empty($record[$dateField])) {
                    try {
                        $record[$dateField] = $this->importDate($record[$dateField])->toDateString();
                    } catch (Throwable) {
                        $errors[] = 'Row '.($index + 2).': '.str_replace('_', ' ', ucfirst($dateField)).' is not a valid date.';
                    }
                }
            }
            foreach (['kos', 'kos_renewal'] as $amountField) {
                if (isset($record[$amountField])) {
                    $record[$amountField] = str_replace([',', 'RM', ' '], '', (string) $record[$amountField]);
                }
            }

            $validator = Validator::make($record, $this->rules());
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = 'Row '.($index + 2).': '.$message;
                }
            } else {
                $records[] = $this->withSourceFields($validator->validated());
            }

            if (count($errors) >= 10) {
                break;
            }
        }

        if ($errors !== []) {
            return back()->withErrors(['file' => implode(' ', array_slice($errors, 0, 10))]);
        }
        if ($records === []) {
            return back()->withErrors(['file' => 'The file does not contain any rows with a Product Name. No existing records were changed.']);
        }

        DB::transaction(function () use ($records, $validated) {
            if ($validated['mode'] === 'replace') {
                foreach (array_unique(array_column($records, 'category')) as $category) {
                    $existing = IntellectualProperty::query()->where('category', $category)->get();
                    foreach ($existing as $ip) {
                        $ip->staff()->detach();
                    }
                    IntellectualProperty::query()->where('category', $category)->delete();
                }
            }

            foreach (array_chunk($records, 250) as $batch) {
                foreach ($batch as $record) {
                    $record['status_produk'] = $record['status_produk'] ?? $record['status'] ?? null;
                    IntellectualProperty::create($record);
                }
            }
        });

        return redirect()->route('admin.ip.index')
            ->with('success', count($records).' intellectual property record(s) imported into the RICM database.');
    }

    public function reports(): View
    {
        session(['workspace' => 'ip']);
        $records = IntellectualProperty::query();

        return view('admin.ip.reports.index', [
            'summary' => [
                'total_ips' => (clone $records)->count(),
                'granted_ips' => (clone $records)->whereIn('status', ['GRANTED', 'REGISTERED'])->count(),
                'pending_ips' => (clone $records)->whereIn('status', ['FILED', 'PENDING'])->count(),
                'this_year_ips' => (clone $records)->where(function (Builder $query) {
                    $query->where('tahun', now()->year)
                        ->orWhereBetween('filing_date', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()]);
                })->count(),
                'total_cost' => (float) (clone $records)->sum('kos'),
            ],
            'byCategory' => (clone $records)
                ->selectRaw("COALESCE(NULLIF(category, ''), type) as label, COUNT(*) as total")
                ->groupBy('label')
                ->orderByDesc('total')
                ->get(),
            'byStatus' => (clone $records)
                ->selectRaw("COALESCE(NULLIF(status, ''), 'Not Set') as label, COUNT(*) as total")
                ->groupBy('label')
                ->orderByDesc('total')
                ->get(),
        ]);
    }

    public function renewals(Request $request): View
    {
        session(['workspace' => 'ip']);
        $records = IntellectualProperty::query()
            ->whereIn('category', ['PATENT', 'PATENT_GRANTED', 'TRADEMARK'])
            ->orderBy('expiry_date')
            ->get();

        $statuses = ['ACTIVE' => 0, 'DUE_SOON' => 0, 'EXPIRED' => 0, 'NOT_SET' => 0];
        $renewals = $records->map(function (IntellectualProperty $ip) use (&$statuses) {
            $nextRenewal = $ip->tarikh_renewal_seterusnya;
            $expiry = $ip->expiry_date;
            if ($expiry && $expiry->lt(today())) {
                $status = 'EXPIRED';
            } else {
                $actionDate = $nextRenewal ?: $expiry;
                if (! $actionDate) {
                    $status = 'NOT_SET';
                } elseif ($actionDate->lte(today()->addMonths(12)->endOfDay())) {
                    $status = 'DUE_SOON';
                } else {
                    $status = 'ACTIVE';
                }
            }
            $statuses[$status]++;
            $ip->setAttribute('renewal_status', $status);

            return $ip;
        });

        if ($request->filled('category')) {
            $renewals = $renewals->where('category', $request->input('category'));
        }
        if ($request->filled('renewal_status') && isset($statuses[$request->input('renewal_status')])) {
            $renewals = $renewals->where('renewal_status', $request->input('renewal_status'));
        }

        $renewals = $renewals->values();
        $page = Paginator::resolveCurrentPage();
        $perPage = 10;
        $paginated = new Paginator(
            $renewals->forPage($page, $perPage)->values(),
            $renewals->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.ip.renewals', [
            'renewals' => $paginated,
            'renewalCounts' => $statuses,
        ]);
    }

    public function export(Request $request)
    {
        $ips = $this->filteredQuery($request)
            ->with(['project', 'staff'])
            ->orderByDesc('filing_date')
            ->orderByDesc('id')
            ->get();

        $columns = [
            'category' => 'CATEGORY',
            'bil' => 'BIL.',
            'title' => 'PRODUCT NAME',
            'nama_produk_baru' => 'NEW PRODUCT NAME',
            'tahun' => 'YEAR',
            'tahun_aktiviti' => 'ACTIVITY YEAR',
            'filing_date' => 'FILING DATE',
            'grant_date' => 'GRANT DATE',
            'fakulti' => 'FACULTY',
            'coe' => 'CoE',
            'jawatan' => 'POSITION',
            'no_staf' => 'STAFF ID',
            'gelaran' => 'TITLE',
            'nama_penyelidik' => 'RESEARCHER',
            'penyelidik_bersama' => 'CO-RESEARCHERS',
            'geran' => 'GRANT',
            'ip_number' => 'FILING NUMBER',
            'status' => 'STATUS',
            'ejen' => 'AGENT',
            'type' => 'IP TYPE',
            'pewartaan' => 'GAZETTE',
            'kos' => 'COST (RM)',
            'abstrak' => 'ABSTRACT',
            'kategori' => 'SUBCATEGORY',
            'tarikh_notis' => 'NOTICE DATE',
            'tarikh_pewartaan' => 'GAZETTE DATE',
            'expiry_date' => 'EXPIRY DATE',
            'tarikh_renewal_terakhir' => 'LAST RENEWAL DATE',
            'tarikh_renewal_seterusnya' => 'NEXT RENEWAL DATE',
            'kos_renewal' => 'RENEWAL COST (RM)',
            'country' => 'COUNTRY',
            'link_to_evidence' => 'EVIDENCE URL',
            'remarks' => 'REMARKS',
            'staff_names' => 'RICM STAFF MEMBERS',
            'staff_ids' => 'RICM STAFF IDS',
            'project_title' => 'PROJECT TITLE',
            'project_code' => 'PROJECT CODE',
        ];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('IP Records');

        foreach (array_values($columns) as $index => $heading) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $heading);
        }
        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->getFont()->setBold(true);

        foreach ($ips as $rowIndex => $ip) {
            foreach (array_keys($columns) as $columnIndex => $field) {
                $value = match ($field) {
                    'title' => $ip->title,
                    'filing_date', 'expiry_date', 'grant_date', 'tarikh_notis', 'tarikh_pewartaan', 'tarikh_renewal_terakhir', 'tarikh_renewal_seterusnya' => optional($ip->{$field})->format('Y-m-d'),
                    'staff_names' => $ip->staff->pluck('staff_name')->join(', '),
                    'staff_ids' => $ip->staff->pluck('staff_id')->join(', '),
                    'project_title' => $ip->project?->project_title,
                    'project_code' => $ip->project?->project_id,
                    default => $ip->{$field},
                };
                $sheet->setCellValueByColumnAndRow($columnIndex + 1, $rowIndex + 2, $value);
            }
        }

        $filename = 'ricm-ip-records-'.now()->format('Y-m-d_His').'.xlsx';

        return response()->streamDownload(
            fn () => (new Xlsx($spreadsheet))->save('php://output'),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function chartData(Request $request)
    {
        $type = $request->input('type');
        $filters = $request->duplicate();
        if (in_array($type, ['types', 'statuses', 'monthly', 'faculties'], true)) {
            $filters->query->remove('type');
            $filters->request->remove('type');
        } else {
            $type = 'all';
        }
        $query = $this->filteredQuery($filters, $type === 'monthly');

        if ($type === 'types') {
            return response()->json((clone $query)
                ->selectRaw("COALESCE(NULLIF(category, ''), type) as label, COUNT(*) as count")
                ->groupBy('label')
                ->orderByDesc('count')
                ->get()
                ->map(fn ($row) => ['type' => $row->label, 'count' => (int) $row->count]));
        }
        if ($type === 'statuses') {
            return response()->json((clone $query)
                ->selectRaw("COALESCE(NULLIF(status, ''), 'Not Set') as label, COUNT(*) as count")
                ->groupBy('label')
                ->orderByDesc('count')
                ->get()
                ->map(fn ($row) => ['status' => $row->label, 'count' => (int) $row->count]));
        }
        if ($type === 'faculties') {
            return response()->json($this->facultyCounts((clone $query)->with('staff.faculty')->get()));
        }

        $years = [];
        foreach ((clone $query)->get(['tahun', 'filing_date']) as $record) {
            $year = $record->tahun ?: optional($record->filing_date)->year;
            if ($year) {
                $years[$year] = ($years[$year] ?? 0) + 1;
            }
        }
        ksort($years);

        return response()->json(collect($years)->map(fn ($count, $year) => ['year' => $year, 'count' => $count])->values());
    }

    private function filteredQuery(Request $request, bool $ignoreYear = false): Builder
    {
        $query = IntellectualProperty::query();

        if ($request->filled('category')) {
            $category = $request->string('category')->toString();
            $query->where(function (Builder $builder) use ($category) {
                $builder->where('category', $category)
                    ->orWhere(function (Builder $legacy) use ($category) {
                        $legacy->whereNull('category')->where('type', $category);
                    });
            });
        }

        $year = $request->input('year', $request->input('tahun'));
        if (! $ignoreYear && filled($year) && is_numeric($year)) {
            $query->where(function (Builder $builder) use ($year) {
                $builder->where('tahun', (int) $year)
                    ->orWhereBetween('filing_date', [$year.'-01-01', $year.'-12-31']);
            });
        }

        $faculty = $request->input('faculty', $request->input('faculty_id'));
        if (filled($faculty)) {
            $query->where(function (Builder $builder) use ($faculty) {
                $builder->where('fakulti', $faculty)
                    ->orWhereHas('staff.faculty', function (Builder $facultyQuery) use ($faculty) {
                        $facultyQuery->where('faculty_code', $faculty)
                            ->orWhere('faculty_name', $faculty);
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('search')) {
            $search = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $request->string('search')->trim()->toString()).'%';
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', $search)
                    ->orWhere('nama_produk_baru', 'like', $search)
                    ->orWhere('ip_number', 'like', $search)
                    ->orWhere('nama_penyelidik', 'like', $search)
                    ->orWhere('penyelidik_bersama', 'like', $search)
                    ->orWhereHas('staff', function (Builder $staffQuery) use ($search) {
                        $staffQuery->where('staff_name', 'like', $search)
                            ->orWhere('staff_id', 'like', $search);
                    })
                    ->orWhereHas('project', function (Builder $projectQuery) use ($search) {
                        $projectQuery->where('project_title', 'like', $search)
                            ->orWhere('project_id', 'like', $search);
                    });
            });
        }

        return $query;
    }

    private function filterOptions(): array
    {
        $categories = array_values(array_unique(array_merge(
            self::CATEGORIES,
            IntellectualProperty::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all()
        )));
        $years = IntellectualProperty::query()->get(['tahun', 'filing_date'])
            ->map(fn (IntellectualProperty $ip) => $ip->tahun ?: optional($ip->filing_date)->year)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();
        $faculties = Faculty::query()->orderBy('faculty_code')->get()
            ->map(fn (Faculty $faculty) => ['value' => $faculty->faculty_code, 'label' => $faculty->faculty_code.' - '.$faculty->faculty_name]);

        foreach (IntellectualProperty::query()->whereNotNull('fakulti')->where('fakulti', '!=', '')->distinct()->orderBy('fakulti')->pluck('fakulti') as $faculty) {
            if (! $faculties->contains(fn (array $item) => $item['value'] === $faculty)) {
                $faculties->push(['value' => $faculty, 'label' => $faculty]);
            }
        }

        return [
            'categories' => $categories,
            'years' => $years,
            'faculties' => $faculties,
            'types' => self::TYPES,
            'statuses' => IntellectualProperty::query()->whereNotNull('status')->where('status', '!=', '')->distinct()->orderBy('status')->pluck('status'),
        ];
    }

    private function facultyCounts($records): array
    {
        $counts = [];
        foreach ($records as $ip) {
            $labels = collect([$ip->fakulti])
                ->merge($ip->staff->map(fn (Staff $staff) => $staff->faculty?->faculty_code ?: $staff->faculty?->faculty_name))
                ->filter()
                ->unique();

            if ($labels->isEmpty()) {
                $labels->push('Not Set');
            }
            foreach ($labels as $label) {
                $counts[$label] = ($counts[$label] ?? 0) + 1;
            }
        }
        arsort($counts);

        return collect($counts)->take(10)->map(fn ($count, $label) => ['faculty_code' => $label, 'count' => $count])->values()->all();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate($this->rules() + [
            'staff_ids' => ['nullable', 'array'],
            'staff_ids.*' => ['integer', 'exists:staff,id'],
            'country' => ['nullable', 'string', 'max:100'],
            'link_to_evidence' => ['nullable', 'url', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);
        $data['tahun'] = $data['tahun'] ?? (! empty($data['filing_date']) ? Carbon::parse($data['filing_date'])->year : null);
        $data = $this->withSourceFields($data);

        return $data;
    }

    private function rules(): array
    {
        return [
            'category' => ['required', 'in:'.implode(',', self::CATEGORIES)],
            'bil' => ['nullable', 'string', 'max:100'],
            'nama_produk_baru' => ['nullable', 'string', 'max:255'],
            'tahun' => ['nullable', 'integer', 'between:1900,2100'],
            'tahun_aktiviti' => ['nullable', 'integer', 'between:1900,2100'],
            'type' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'ip_number' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:100'],
            'filing_date' => ['nullable', 'date'],
            'grant_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'fakulti' => ['nullable', 'string', 'max:100'],
            'coe' => ['nullable', 'string', 'max:255'],
            'jawatan' => ['nullable', 'string', 'max:255'],
            'no_staf' => ['nullable', 'string', 'max:100'],
            'gelaran' => ['nullable', 'string', 'max:100'],
            'nama_penyelidik' => ['nullable', 'string', 'max:255'],
            'penyelidik_bersama' => ['nullable', 'string'],
            'geran' => ['nullable', 'string', 'max:255'],
            'status_produk' => ['nullable', 'string', 'max:100'],
            'ejen' => ['nullable', 'string', 'max:255'],
            'pewartaan' => ['nullable', 'string', 'max:255'],
            'kos' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'abstrak' => ['nullable', 'string'],
            'kategori' => ['nullable', 'string', 'max:255'],
            'tarikh_notis' => ['nullable', 'date'],
            'tarikh_pewartaan' => ['nullable', 'date'],
            'tarikh_renewal_terakhir' => ['nullable', 'date'],
            'tarikh_renewal_seterusnya' => ['nullable', 'date'],
            'kos_renewal' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'country' => ['nullable', 'string', 'max:100'],
            'link_to_evidence' => ['nullable', 'url', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    private function importHeaderMap(array $headers): array
    {
        $aliases = [
            'category' => ['category'],
            'bil' => ['bil', 'bil_number'],
            'title' => ['product', 'product_name', 'nama_produk', 'title'],
            'nama_produk_baru' => ['new_product_name', 'nama_produk_baru'],
            'tahun' => ['year', 'tahun'],
            'tahun_aktiviti' => ['activity_year', 'tahun_aktiviti'],
            'filing_date' => ['filing_date', 'tarikh_difailkan'],
            'grant_date' => ['grant_date', 'tarikh_grant', 'tarikh_geran'],
            'fakulti' => ['faculty', 'fakulti'],
            'coe' => ['coe'],
            'jawatan' => ['position', 'jawatan'],
            'no_staf' => ['staff_id', 'no_staf'],
            'gelaran' => ['title_prefix', 'gelaran'],
            'nama_penyelidik' => ['researcher', 'nama_penyelidik'],
            'penyelidik_bersama' => ['co_researchers', 'co_researcher', 'penyelidik_bersama', 'penyelidik_bersama_contribution'],
            'geran' => ['grant', 'geran'],
            'ip_number' => ['filing_number', 'no_pemfailan', 'ip_number'],
            'status' => ['status', 'product_status', 'status_produk', 'panel_decision', 'keputusan_panel'],
            'ejen' => ['agent', 'ejen'],
            'type' => ['ip_type', 'jenis_ipr', 'jenis_pemfailan', 'type'],
            'pewartaan' => ['gazette', 'pewartaan'],
            'kos' => ['cost', 'kos'],
            'abstrak' => ['abstract', 'abstrak'],
            'kategori' => ['subcategory', 'kategori'],
            'tarikh_notis' => ['notice_date', 'tarikh_notis'],
            'tarikh_pewartaan' => ['gazette_date', 'tarikh_pewartaan'],
            'expiry_date' => ['expiry_date', 'tarikh_luput'],
            'tarikh_renewal_terakhir' => ['last_renewal_date', 'tarikh_renewal_terakhir'],
            'tarikh_renewal_seterusnya' => ['next_renewal_date', 'tarikh_renewal_seterusnya'],
            'kos_renewal' => ['renewal_cost', 'kos_renewal'],
            'country' => ['country'],
            'link_to_evidence' => ['evidence_url', 'link_to_evidence'],
            'remarks' => ['remarks'],
        ];
        $map = [];
        foreach ($headers as $index => $header) {
            foreach ($aliases as $field => $names) {
                if (in_array($header, $names, true)) {
                    $map[$field] = $index;
                    break;
                }
            }
        }

        return $map;
    }

    private function normalizeHeader(string $header): string
    {
        return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '_', $header), '_'));
    }

    private function withSourceFields(array $record): array
    {
        $record['nama_produk'] = $record['title'] ?? null;
        $record['tarikh_difailkan'] = $record['filing_date'] ?? null;
        $record['no_pemfailan'] = $record['ip_number'] ?? null;
        $record['status_produk'] = $record['status_produk'] ?? $record['status'] ?? null;
        $record['jenis_ipr'] = $record['type'] ?? null;
        $record['tarikh_luput'] = $record['expiry_date'] ?? null;

        return $record;
    }

    private function importDate(mixed $value): Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }
        if (is_numeric($value) && (float) $value > 20000) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
        }

        return Carbon::parse($value);
    }
}
