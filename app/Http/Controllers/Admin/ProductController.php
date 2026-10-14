<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrlProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        session(['workspace' => 'products']);

        $query = $this->filteredQuery($request);

        $statusCounts = (clone $query)
            ->selectRaw("COALESCE(NULLIF(status, ''), 'Not Set') as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        $trlCounts = (clone $query)
            ->selectRaw('trl_level as label, COUNT(*) as total')
            ->groupBy('trl_level')
            ->orderBy('trl_level')
            ->get();

        $facultyCounts = (clone $query)
            ->selectRaw("COALESCE(NULLIF(faculty, ''), 'Not Set') as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $allStatuses = TrlProduct::query()
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');
        $allFaculties = TrlProduct::query()
            ->whereNotNull('faculty')
            ->where('faculty', '!=', '')
            ->distinct()
            ->orderBy('faculty')
            ->pluck('faculty');
        $allYears = TrlProduct::query()
            ->whereNotNull('product_year')
            ->distinct()
            ->orderByDesc('product_year')
            ->pluck('product_year');

        $total = (clone $query)->count();

        return view('admin.products.index', [
            'totalProducts' => $total,
            'inDevelopment' => $this->countStatuses($query, ['in-development', 'in development', 'development']),
            'stagnant' => $this->countStatuses($query, ['stagnant']),
            'commercial' => $this->countStatuses($query, ['commercial', 'commercialized', 'commercialised']),
            'allStatuses' => $allStatuses,
            'allFaculties' => $allFaculties,
            'allYears' => $allYears,
            'statusChart' => $statusCounts,
            'trlChart' => $trlCounts,
            'facultyChart' => $facultyCounts,
        ]);
    }

    public function records(Request $request): View
    {
        session(['workspace' => 'products']);

        $products = $this->filteredQuery($request)
            ->orderBy('trl_level')
            ->orderBy('product')
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.records', [
            'products' => $products,
            'allStatuses' => TrlProduct::query()
                ->whereNotNull('status')
                ->where('status', '!=', '')
                ->distinct()
                ->orderBy('status')
                ->pluck('status'),
            'allFaculties' => TrlProduct::query()
                ->whereNotNull('faculty')
                ->where('faculty', '!=', '')
                ->distinct()
                ->orderBy('faculty')
                ->pluck('faculty'),
            'allYears' => TrlProduct::query()
                ->whereNotNull('product_year')
                ->distinct()
                ->orderByDesc('product_year')
                ->pluck('product_year'),
        ]);
    }

    public function create(): View
    {
        session(['workspace' => 'products']);

        return view('admin.products.create', ['product' => new TrlProduct]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['uuid'] = (string) Str::uuid();
        TrlProduct::create($data);

        return redirect()->route('admin.products.records')->with('success', 'TRL product added successfully.');
    }

    public function edit(TrlProduct $product): View
    {
        session(['workspace' => 'products']);

        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, TrlProduct $product): RedirectResponse
    {
        $product->update($this->validated($request));

        return redirect()->route('admin.products.records')->with('success', 'TRL product updated successfully.');
    }

    public function destroy(TrlProduct $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.records')->with('success', 'TRL product moved to the recycle bin.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        } catch (SpreadsheetReaderException $exception) {
            report($exception);

            return back()->withErrors(['file' => 'The uploaded file could not be read as a spreadsheet.']);
        }
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        $headers = array_map(fn ($header) => $this->normalizeHeader((string) $header), array_shift($rows) ?? []);
        $headerMap = $this->headerMap($headers);

        if (! isset($headerMap['product'], $headerMap['trl_level'])) {
            return back()->withErrors([
                'file' => 'The file must include Product and TRL (or trl_level) columns.',
            ]);
        }

        $records = [];
        $rowErrors = [];
        foreach ($rows as $index => $row) {
            if (collect($row)->every(fn ($value) => $value === null || trim((string) $value) === '')) {
                continue;
            }

            $record = [];
            foreach ($headerMap as $field => $columnIndex) {
                $record[$field] = $row[$columnIndex] ?? null;
            }
            $record['trl_level'] = preg_replace('/\D/', '', (string) ($record['trl_level'] ?? ''));
            $record['product_year'] = ($record['product_year'] ?? '') === '' ? null : $record['product_year'];
            $record['uuid'] = trim((string) ($record['uuid'] ?? '')) ?: (string) Str::uuid();

            $validator = Validator::make($record, [
                'uuid' => ['required', 'uuid'],
                'trl_level' => ['required', 'integer', 'between:1,9'],
                'product' => ['required', 'string', 'max:10000'],
                'researcher' => ['nullable', 'string', 'max:10000'],
                'faculty' => ['nullable', 'string', 'max:100'],
                'category' => ['nullable', 'string', 'max:10000'],
                'status' => ['nullable', 'string', 'max:100'],
                'action' => ['nullable', 'string', 'max:150'],
                'abstract' => ['nullable', 'string'],
                'product_year' => ['nullable', 'integer', 'between:1900,2100'],
            ]);

            if ($validator->fails()) {
                $rowErrors[] = 'Row '.($index + 2).': '.implode(' ', $validator->errors()->all());
                if (count($rowErrors) >= 10) {
                    break;
                }

                continue;
            }

            $records[] = $validator->validated();
        }

        if ($rowErrors !== []) {
            return back()->withErrors(['file' => implode(' ', $rowErrors)]);
        }

        $created = 0;
        $updated = 0;
        DB::transaction(function () use ($records, &$created, &$updated) {
            foreach ($records as $record) {
                $product = TrlProduct::withTrashed()->where('uuid', $record['uuid'])->first();
                if ($product) {
                    $product->update($record);
                    if ($product->trashed()) {
                        $product->restore();
                    }
                    $updated++;
                } else {
                    TrlProduct::create($record);
                    $created++;
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Import complete: {$created} created, {$updated} updated.");
    }

    public function export(Request $request): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('TRL Products');
        $sheet->fromArray([
            ['UUID', 'TRL', 'Product', 'Researcher', 'Faculty', 'Category', 'Status', 'Action', 'Abstract', 'Year'],
        ], null, 'A1');
        $sheet->freezePane('A2');

        $rowNumber = 2;
        $this->filteredQuery($request)
            ->orderBy('trl_level')
            ->orderBy('product')
            ->chunk(500, function ($products) use ($sheet, &$rowNumber) {
                foreach ($products as $product) {
                    $sheet->fromArray([
                        $product->uuid,
                        $product->trl_level,
                        $product->product,
                        $product->researcher,
                        $product->faculty,
                        $product->category,
                        $product->status,
                        $product->action,
                        $product->abstract,
                        $product->product_year,
                    ], null, 'A'.$rowNumber++);
                }
            });

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $path = tempnam(sys_get_temp_dir(), 'trl-products-');
        if ($path === false) {
            throw new \RuntimeException('Unable to create a temporary export file.');
        }
        $file = $path.'.xlsx';
        if (! rename($path, $file)) {
            unlink($path);
            throw new \RuntimeException('Unable to prepare the Excel export file.');
        }
        (new Xlsx($spreadsheet))->save($file);

        return response()->download($file, 'trl-products.xlsx')->deleteFileAfterSend(true);
    }

    private function filteredQuery(Request $request): Builder
    {
        $query = TrlProduct::query();

        if ($request->filled('trl')) {
            $query->where('trl_level', $request->integer('trl'));
        }
        if ($request->filled('status')) {
            if ($request->input('status') === '__not_set__') {
                $query->where(function (Builder $status) {
                    $status->whereNull('status')->orWhere('status', '');
                });
            } else {
                $query->where('status', $request->input('status'));
            }
        }
        if ($request->filled('faculty')) {
            if ($request->input('faculty') === '__not_set__') {
                $query->where(function (Builder $faculty) {
                    $faculty->whereNull('faculty')->orWhere('faculty', '');
                });
            } else {
                $query->where('faculty', $request->input('faculty'));
            }
        }
        if ($request->filled('year')) {
            $query->where('product_year', $request->integer('year'));
        }
        if ($request->filled('search')) {
            $search = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $request->string('search')->trim()->toString()).'%';
            $query->where(function (Builder $products) use ($search) {
                $products->where('product', 'like', $search)
                    ->orWhere('researcher', 'like', $search)
                    ->orWhere('category', 'like', $search)
                    ->orWhere('abstract', 'like', $search);
            });
        }

        return $query;
    }

    private function countStatuses(Builder $query, array $statuses): int
    {
        return (clone $query)
            ->where(function (Builder $statusQuery) use ($statuses) {
                foreach ($statuses as $status) {
                    $statusQuery->orWhereRaw('LOWER(TRIM(status)) = ?', [$status]);
                }
            })
            ->count();
    }

    private function normalizeHeader(string $header): string
    {
        return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '_', $header), '_'));
    }

    private function headerMap(array $headers): array
    {
        $aliases = [
            'uuid' => ['uuid'],
            'trl_level' => ['trl', 'trl_level'],
            'product' => ['product'],
            'researcher' => ['researcher'],
            'faculty' => ['faculty'],
            'category' => ['category', 'categories'],
            'status' => ['status'],
            'action' => ['action'],
            'abstract' => ['abstract'],
            'product_year' => ['year', 'product_year'],
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

    private function validated(Request $request): array
    {
        return $request->validate([
            'trl_level' => ['required', 'integer', 'between:1,9'],
            'product' => ['required', 'string', 'max:10000'],
            'researcher' => ['nullable', 'string', 'max:10000'],
            'faculty' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:10000'],
            'status' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:150'],
            'abstract' => ['nullable', 'string'],
            'product_year' => ['nullable', 'integer', 'between:1900,2100'],
        ]);
    }
}
