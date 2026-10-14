<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiStrategy;
use App\Models\KpiRecord;
use App\Models\KpiYear;
use App\Models\KpiPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KpiExport;

class KpiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            
            // Allow access only to users with KPI module access or superadmin
            if (!$user) {
                abort(403, 'Unauthorized access');
            }
            
            // Use the hasRole method from User model for consistent role checking
            if (!$user->hasRole(['superadmin', 'super_admin']) && !$user->modules()->where('module_name', 'KPI')->exists()) {
                abort(403, 'Unauthorized access');
            }
            
            return $next($request);
        });
    }

    public function index()
    {
        $strategies = KpiStrategy::ordered()->with(['kpiRecords.kpiYears.phases'])->get();
        $years = [2026, 2027, 2028, 2029, 2030];
        $currentYear = request('year', 2026);
        
        // Get strategies for filter
        $strategyOptions = KpiStrategy::orderBy('display_order')->get();
        
        // Filter by strategy if selected
        $selectedStrategy = request('strategy');
        if ($selectedStrategy) {
            $strategies = $strategies->where('strategy_code', $selectedStrategy);
        }

        return view('admin.kpi.index', compact('strategies', 'years', 'currentYear', 'strategyOptions'));
    }

    public function edit()
    {
        $strategies = KpiStrategy::ordered()->with(['kpiRecords.kpiYears.phases'])->get();
        $years = [2026, 2027, 2028, 2029, 2030];
        $currentYear = request('year', 2026);

        return view('admin.kpi.edit', compact('strategies', 'years', 'currentYear'));
    }

    public function getData(Request $request)
    {
        $year = $request->get('year', 2026);
        $strategies = KpiStrategy::ordered()->with([
            'kpiRecords.kpiYears' => function ($query) use ($year) {
                $query->where('target_year', $year)->with('phases');
            }
        ])->get();

        $data = [];

        foreach ($strategies as $strategy) {
            $strategyData = [
                'id' => $strategy->id,
                'strategy_code' => $strategy->strategy_code,
                'strategy_name' => $strategy->strategy_name,
                'display_order' => $strategy->display_order,
                'kpi_records' => []
            ];

            foreach ($strategy->kpiRecords as $kpi) {
                $kpiYear = $kpi->kpiYears->first();
                $phases = $kpiYear ? $kpiYear->phases : collect();

                $kpiData = [
                    'id' => $kpi->id,
                    'strategy_id' => $kpi->strategy_id,
                    'kpi_code' => $kpi->kpi_code,
                    'initiative' => $kpi->initiative,
                    'performance_indicator' => $kpi->performance_indicator,
                    'action_plan' => $kpi->action_plan,
                    'achievement_info' => $kpiYear ? $kpiYear->achievement_information : null,
                    'kpi_years' => []
                ];

                if ($kpiYear) {
                    $kpiData['kpi_years'][] = [
                        'id' => $kpiYear->id,
                        'kpi_id' => $kpiYear->kpi_id,
                        'target_year' => $kpiYear->target_year,
                        'target_value' => $kpiYear->target_value,
                        'achievement_percentage' => $kpiYear->achievement_percentage,
                        'achievement_information' => $kpiYear->achievement_information,
                        'phases' => $phases->map(function ($phase) {
                            return [
                                'id' => $phase->id,
                                'kpi_year_id' => $phase->kpi_year_id,
                                'phase' => $phase->phase,
                                'achievement' => $phase->achievement
                            ];
                        })->values()->toArray()
                    ];
                }

                $strategyData['kpi_records'][] = $kpiData;
            }

            $data[] = $strategyData;
        }

        return response()->json([
            'success' => true,
            'strategies' => $data,
            'totalKPIs' => collect($data)->sum(fn($s) => count($s['kpi_records'])),
            'lastUpdated' => now()->toISOString()
        ]);
    }

    public function updateKpi(Request $request)
    {
        try {
            // Check if bulk update (changes array) or single update
            if ($request->has('changes')) {
                return $this->bulkUpdateKpi($request);
            }
            
            // Single update (backward compatibility)
            $kpiCode = $request->input('kpi_code');
            $year = $request->input('year');
            $field = $request->input('field');
            $value = $request->input('value');

            $kpi = KpiRecord::where('kpi_code', $kpiCode)->first();
            if (!$kpi) {
                return response()->json(['success' => false, 'error' => 'KPI not found'], 404);
            }

            if ($field === 'initiative' || $field === 'performance_indicator' || $field === 'action_plan') {
                $kpi->update([$field => $value]);
            } elseif ($field === 'achievement_information') {
                $kpiYear = KpiYear::where('kpi_id', $kpi->id)->where('target_year', $year)->first();
                if (!$kpiYear) {
                    $kpiYear = KpiYear::create([
                        'kpi_id' => $kpi->id,
                        'target_year' => $year,
                        'achievement_information' => $value
                    ]);
                } else {
                    $kpiYear->update(['achievement_information' => $value]);
                }
            } else {
                $kpiYear = KpiYear::where('kpi_id', $kpi->id)->where('target_year', $year)->first();
                if (!$kpiYear) {
                    $kpiYear = KpiYear::create([
                        'kpi_id' => $kpi->id,
                        'target_year' => $year,
                        $field => $value
                    ]);
                } else {
                    $kpiYear->update([$field => $value]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function bulkUpdateKpi(Request $request)
    {
        try {
            $changes = $request->input('changes', []);
            
            DB::beginTransaction();
            
            foreach ($changes as $change) {
                $kpiCode = $change['kpi_code'];
                $year = $change['year'];
                $field = $change['field'];
                $value = $change['value'];

                $kpi = KpiRecord::where('kpi_code', $kpiCode)->first();
                if (!$kpi) {
                    throw new \Exception("KPI not found: {$kpiCode}");
                }

                if ($field === 'initiative' || $field === 'performance_indicator' || $field === 'action_plan') {
                    $kpi->update([$field => $value]);
                } elseif ($field === 'achievement_information') {
                    $kpiYear = KpiYear::where('kpi_id', $kpi->id)->where('target_year', $year)->first();
                    if (!$kpiYear) {
                        $kpiYear = KpiYear::create([
                            'kpi_id' => $kpi->id,
                            'target_year' => $year,
                            'achievement_information' => $value
                        ]);
                    } else {
                        $kpiYear->update(['achievement_information' => $value]);
                    }
                } elseif (str_starts_with($field, 'phase')) {
                    $kpiYear = KpiYear::where('kpi_id', $kpi->id)->where('target_year', $year)->first();
                    if (!$kpiYear) {
                        $kpiYear = KpiYear::create([
                            'kpi_id' => $kpi->id,
                            'target_year' => $year
                        ]);
                    }

                    $phaseMap = [
                        'phase1' => 'Phase 1',
                        'phase2' => 'Phase 2',
                        'phase3' => 'Phase 3',
                        'phase4' => 'Phase 4'
                    ];
                    
                    $phase = $phaseMap[$field] ?? $field;
                    
                    $kpiPhase = KpiPhase::where('kpi_year_id', $kpiYear->id)->where('phase', $phase)->first();
                    if (!$kpiPhase) {
                        KpiPhase::create([
                            'kpi_year_id' => $kpiYear->id,
                            'phase' => $phase,
                            'achievement' => $value
                        ]);
                    } else {
                        $kpiPhase->update(['achievement' => $value]);
                    }

                    // Recalculate achievement percentage
                    $this->recalculateAchievement($kpiYear);
                } else {
                    $kpiYear = KpiYear::where('kpi_id', $kpi->id)->where('target_year', $year)->first();
                    if (!$kpiYear) {
                        $kpiYear = KpiYear::create([
                            'kpi_id' => $kpi->id,
                            'target_year' => $year,
                            $field => $value
                        ]);
                    } else {
                        $kpiYear->update([$field => $value]);
                    }
                }
            }
            
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data berjaya disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updatePhase(Request $request)
    {
        try {
            $kpiCode = $request->input('kpi_code');
            $year = $request->input('year');
            $phase = $request->input('phase');
            $value = $request->input('value');

            $kpi = KpiRecord::where('kpi_code', $kpiCode)->first();
            if (!$kpi) {
                return response()->json(['success' => false, 'error' => 'KPI not found'], 404);
            }

            $kpiYear = KpiYear::where('kpi_id', $kpi->id)->where('target_year', $year)->first();
            if (!$kpiYear) {
                $kpiYear = KpiYear::create([
                    'kpi_id' => $kpi->id,
                    'target_year' => $year
                ]);
            }

            $kpiPhase = KpiPhase::where('kpi_year_id', $kpiYear->id)->where('phase', $phase)->first();
            if (!$kpiPhase) {
                $kpiPhase = KpiPhase::create([
                    'kpi_year_id' => $kpiYear->id,
                    'phase' => $phase,
                    'achievement' => $value
                ]);
            } else {
                $kpiPhase->update(['achievement' => $value]);
            }

            // Recalculate achievement percentage
            $this->recalculateAchievement($kpiYear);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function recalculateAchievement(KpiYear $kpiYear)
    {
        $phases = $kpiYear->phases;
        $totalAchievement = $phases->sum('achievement');
        $targetValue = floatval($kpiYear->target_value) ?: 1;

        $percentage = ($totalAchievement / $targetValue) * 100;
        $kpiYear->update(['achievement_percentage' => min(100, $percentage)]);
    }

    public function downloadTemplate($type)
    {
        $templates = [
            'kpi' => [
                'STRATEGY_CODE,STRATEGY_NAME,DISPLAY_ORDER,KPI_CODE,INITIATIVE,PERFORMANCE_INDICATOR,ACTION_PLAN,TARGET_YEAR,TARGET_VALUE,PHASE_1,PHASE_2,PHASE_3,PHASE_4,ACHIEVEMENT_PERCENTAGE,ACHIEVEMENT_INFORMATION',
                '2.1,MEMPERKUKUHKAN PENYELIDIKAN DAN PEMBANGUNAN BERIMPAK TINGGI,1,KPI-001,Memperkasa Penerbitan Buku Ilmiah,Buku ilmiah diterbitkan,Pelan tindakan penerbitan,2026,10,2,3,2,3,100,Pencapaian sasaran',
                '2.2,MEMPERKASAKAN INOVASI UNTUK MANFAAT KOMUNITI DAN INDUSTRI,2,KPI-002,Inovasi Komuniti,Produk inovasi dibangunkan,Pembangunan prototaip,2026,5,1,2,1,1,100,Inovasi berjaya',
            ],
        ];

        if (!isset($templates[$type])) {
            return back()->with('error', 'Invalid template type.');
        }

        $filename = $type . '_template.csv';
        $content = implode("\n", $templates[$type]);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    public function export(Request $request)
    {
        $year = $request->get('year', 2026);
        
        return Excel::download(new KpiExport($year), 'KPI_Data_Strategi_' . $year . '.xlsx');
    }
}
