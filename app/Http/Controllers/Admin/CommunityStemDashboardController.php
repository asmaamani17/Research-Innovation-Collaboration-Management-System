<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityStemProgram;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommunityStemDashboardController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:1900,2100'],
            'pusat' => ['nullable', 'string', 'max:255'],
        ]);

        $applyFilters = static function (Builder $query) use ($filters): Builder {
            if (!empty($filters['tahun'])) {
                $query->whereYear('start_date', $filters['tahun']);
            }

            if (!empty($filters['pusat'])) {
                $query->where('faculty', $filters['pusat']);
            }

            return $query;
        };

        $total = $applyFilters(CommunityStemProgram::query())->count();
        $countFor = static fn (string $classification): int => $applyFilters(
            CommunityStemProgram::where('classification', $classification)
        )->count();
        $percentageFor = static fn (int $count): int => $total > 0
            ? (int) round($count / $total * 100)
            : 0;

        $stemCount = $countFor('STEM');
        $komunitiCount = $countFor('Community');
        $industriCount = $countFor('Industri');

        $pusatCounts = $applyFilters(
            CommunityStemProgram::query()
                ->selectRaw("COALESCE(NULLIF(faculty, ''), 'Tidak Dinyatakan') as faculty, COUNT(*) as jumlah")
        )->groupBy('faculty')->orderByDesc('jumlah')->get();

        $senaraiPusat = $pusatCounts->pluck('faculty')->values();
        $pecahanPusat = $applyFilters(
            CommunityStemProgram::query()
                ->selectRaw("COALESCE(NULLIF(faculty, ''), 'Tidak Dinyatakan') as faculty, classification, COUNT(*) as jumlah")
        )->groupBy('faculty', 'classification')->get();

        $seriesFor = static function (string $classification) use ($senaraiPusat, $pecahanPusat) {
            return $senaraiPusat->map(static function (string $faculty) use ($classification, $pecahanPusat) {
                $row = $pecahanPusat->first(
                    static fn ($item) => $item->faculty === $faculty && $item->classification === $classification
                );

                return $row?->jumlah ?? 0;
            })->values();
        };

        $monthlySeriesFor = static function (string $classification) use ($applyFilters) {
            $counts = $applyFilters(
                CommunityStemProgram::query()
                    ->selectRaw('MONTH(start_date) as month_number, COUNT(*) as jumlah')
                    ->where('classification', $classification)
                    ->whereNotNull('start_date')
            )->groupBy('month_number')->pluck('jumlah', 'month_number');

            return collect(range(1, 12))
                ->map(static fn (int $month) => $counts[$month] ?? 0)
                ->values();
        };

        $kategoriCounts = $applyFilters(
            CommunityStemProgram::query()
                ->selectRaw("COALESCE(NULLIF(kategori, ''), 'Tidak Dinyatakan') as kategori, COUNT(*) as jumlah")
        )->groupBy('kategori')->get();

        $senaraiTahunFilter = CommunityStemProgram::query()
            ->whereNotNull('start_date')
            ->selectRaw('DISTINCT YEAR(start_date) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $senaraiPusatFilter = CommunityStemProgram::query()
            ->whereNotNull('faculty')
            ->where('faculty', '!=', '')
            ->distinct()
            ->orderBy('faculty')
            ->pluck('faculty');

        return view('admin.community,stem,industry.dashboard', [
            'total' => $total,
            'stemCount' => $stemCount,
            'stemPct' => $percentageFor($stemCount),
            'komunitiCount' => $komunitiCount,
            'komunitiPct' => $percentageFor($komunitiCount),
            'industriCount' => $industriCount,
            'industriPct' => $percentageFor($industriCount),
            'senaraiPusat' => $senaraiPusat,
            'stemSiri' => $seriesFor('STEM'),
            'komunitiSiri' => $seriesFor('Community'),
            'industriSiri' => $seriesFor('Industri'),
            'namaBulan' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogo', 'Sep', 'Okt', 'Nov', 'Dis'],
            'bulananKomuniti' => $monthlySeriesFor('Community'),
            'bulananStem' => $monthlySeriesFor('STEM'),
            'bulananIndustri' => $monthlySeriesFor('Industri'),
            'labelKategori' => $kategoriCounts->pluck('kategori')->values(),
            'dataKategori' => $kategoriCounts->pluck('jumlah')->values(),
            'senaraiTahunFilter' => $senaraiTahunFilter,
            'senaraiPusatFilter' => $senaraiPusatFilter,
        ]);
    }

    public function panduan()
    {
        return view('admin.community,stem,industry.panduan');
    }

    public function data(Request $request)
    {
        $filters = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:1900,2100'],
            'pusat' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:stats,pusat,nisbah,bulanan,kategori'],
        ]);

        $applyFilters = static function (Builder $query) use ($filters): Builder {
            if (!empty($filters['tahun'])) {
                $query->whereYear('start_date', $filters['tahun']);
            }

            if (!empty($filters['pusat'])) {
                $query->where('faculty', $filters['pusat']);
            }

            return $query;
        };

        $type = $filters['type'] ?? 'all';

        // Return specific chart data based on type
        if ($type === 'stats') {
            $total = $applyFilters(CommunityStemProgram::query())->count();
            $countFor = static fn (string $classification): int => $applyFilters(
                CommunityStemProgram::where('classification', $classification)
            )->count();
            $percentageFor = static fn (int $count): int => $total > 0
                ? (int) round($count / $total * 100)
                : 0;

            $stemCount = $countFor('STEM');
            $komunitiCount = $countFor('Community');
            $industriCount = $countFor('Industri');

            return response()->json([
                'total' => $total,
                'stemCount' => $stemCount,
                'stemPct' => $percentageFor($stemCount),
                'komunitiCount' => $komunitiCount,
                'komunitiPct' => $percentageFor($komunitiCount),
                'industriCount' => $industriCount,
                'industriPct' => $percentageFor($industriCount),
            ]);
        }

        if ($type === 'pusat') {
            $pusatCounts = $applyFilters(
                CommunityStemProgram::query()
                    ->selectRaw("COALESCE(NULLIF(faculty, ''), 'Tidak Dinyatakan') as faculty, COUNT(*) as jumlah")
            )->groupBy('faculty')->orderByDesc('jumlah')->get();

            $senaraiPusat = $pusatCounts->pluck('faculty')->values();
            $pecahanPusat = $applyFilters(
                CommunityStemProgram::query()
                    ->selectRaw("COALESCE(NULLIF(faculty, ''), 'Tidak Dinyatakan') as faculty, classification, COUNT(*) as jumlah")
            )->groupBy('faculty', 'classification')->get();

            $seriesFor = static function (string $classification) use ($senaraiPusat, $pecahanPusat) {
                return $senaraiPusat->map(static function (string $faculty) use ($classification, $pecahanPusat) {
                    $row = $pecahanPusat->first(
                        static fn ($item) => $item->faculty === $faculty && $item->classification === $classification
                    );

                    return $row?->jumlah ?? 0;
                })->values();
            };

            return response()->json([
                'senaraiPusat' => $senaraiPusat,
                'stemSiri' => $seriesFor('STEM'),
                'komunitiSiri' => $seriesFor('Community'),
                'industriSiri' => $seriesFor('Industri'),
            ]);
        }

        if ($type === 'nisbah') {
            $total = $applyFilters(CommunityStemProgram::query())->count();
            $countFor = static fn (string $classification): int => $applyFilters(
                CommunityStemProgram::where('classification', $classification)
            )->count();
            $percentageFor = static fn (int $count): int => $total > 0
                ? (int) round($count / $total * 100)
                : 0;

            return response()->json([
                'stemCount' => $countFor('STEM'),
                'komunitiCount' => $countFor('Community'),
                'industriCount' => $countFor('Industri'),
                'stemPct' => $percentageFor($countFor('STEM')),
                'komunitiPct' => $percentageFor($countFor('Community')),
                'industriPct' => $percentageFor($countFor('Industri')),
            ]);
        }

        if ($type === 'bulanan') {
            $monthlySeriesFor = static function (string $classification) use ($applyFilters) {
                $counts = $applyFilters(
                    CommunityStemProgram::query()
                        ->selectRaw('MONTH(start_date) as month_number, COUNT(*) as jumlah')
                        ->where('classification', $classification)
                        ->whereNotNull('start_date')
                )->groupBy('month_number')->pluck('jumlah', 'month_number');

                return collect(range(1, 12))
                    ->map(static fn (int $month) => $counts[$month] ?? 0)
                    ->values();
            };

            return response()->json([
                'namaBulan' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogo', 'Sep', 'Okt', 'Nov', 'Dis'],
                'bulananKomuniti' => $monthlySeriesFor('Community'),
                'bulananStem' => $monthlySeriesFor('STEM'),
                'bulananIndustri' => $monthlySeriesFor('Industri'),
            ]);
        }

        if ($type === 'kategori') {
            $kategoriCounts = $applyFilters(
                CommunityStemProgram::query()
                    ->selectRaw("COALESCE(NULLIF(kategori, ''), 'Tidak Dinyatakan') as kategori, COUNT(*) as jumlah")
            )->groupBy('kategori')->get();

            return response()->json([
                'labelKategori' => $kategoriCounts->pluck('kategori')->values(),
                'dataKategori' => $kategoriCounts->pluck('jumlah')->values(),
            ]);
        }

        // Return all data (original behavior)
        $total = $applyFilters(CommunityStemProgram::query())->count();
        $countFor = static fn (string $classification): int => $applyFilters(
            CommunityStemProgram::where('classification', $classification)
        )->count();
        $percentageFor = static fn (int $count): int => $total > 0
            ? (int) round($count / $total * 100)
            : 0;

        $stemCount = $countFor('STEM');
        $komunitiCount = $countFor('Community');
        $industriCount = $countFor('Industri');

        $pusatCounts = $applyFilters(
            CommunityStemProgram::query()
                ->selectRaw("COALESCE(NULLIF(faculty, ''), 'Tidak Dinyatakan') as faculty, COUNT(*) as jumlah")
        )->groupBy('faculty')->orderByDesc('jumlah')->get();

        $senaraiPusat = $pusatCounts->pluck('faculty')->values();
        $pecahanPusat = $applyFilters(
            CommunityStemProgram::query()
                ->selectRaw("COALESCE(NULLIF(faculty, ''), 'Tidak Dinyatakan') as faculty, classification, COUNT(*) as jumlah")
        )->groupBy('faculty', 'classification')->get();

        $seriesFor = static function (string $classification) use ($senaraiPusat, $pecahanPusat) {
            return $senaraiPusat->map(static function (string $faculty) use ($classification, $pecahanPusat) {
                $row = $pecahanPusat->first(
                    static fn ($item) => $item->faculty === $faculty && $item->classification === $classification
                );

                return $row?->jumlah ?? 0;
            })->values();
        };

        $monthlySeriesFor = static function (string $classification) use ($applyFilters) {
            $counts = $applyFilters(
                CommunityStemProgram::query()
                    ->selectRaw('MONTH(start_date) as month_number, COUNT(*) as jumlah')
                    ->where('classification', $classification)
                    ->whereNotNull('start_date')
            )->groupBy('month_number')->pluck('jumlah', 'month_number');

            return collect(range(1, 12))
                ->map(static fn (int $month) => $counts[$month] ?? 0)
                ->values();
        };

        $kategoriCounts = $applyFilters(
            CommunityStemProgram::query()
                ->selectRaw("COALESCE(NULLIF(kategori, ''), 'Tidak Dinyatakan') as kategori, COUNT(*) as jumlah")
        )->groupBy('kategori')->get();

        return response()->json([
            'total' => $total,
            'stemCount' => $stemCount,
            'stemPct' => $percentageFor($stemCount),
            'komunitiCount' => $komunitiCount,
            'komunitiPct' => $percentageFor($komunitiCount),
            'industriCount' => $industriCount,
            'industriPct' => $percentageFor($industriCount),
            'senaraiPusat' => $senaraiPusat,
            'stemSiri' => $seriesFor('STEM'),
            'komunitiSiri' => $seriesFor('Community'),
            'industriSiri' => $seriesFor('Industri'),
            'namaBulan' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogo', 'Sep', 'Okt', 'Nov', 'Dis'],
            'bulananKomuniti' => $monthlySeriesFor('Community'),
            'bulananStem' => $monthlySeriesFor('STEM'),
            'bulananIndustri' => $monthlySeriesFor('Industri'),
            'labelKategori' => $kategoriCounts->pluck('kategori')->values(),
            'dataKategori' => $kategoriCounts->pluck('jumlah')->values(),
        ]);
    }
}
