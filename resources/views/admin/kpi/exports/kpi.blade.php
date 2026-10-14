<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .strategy-header {
            background-color: #e3f2fd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>KPI Data Strategi {{ $year }}</h1>
    <p>Dijana pada: {{ $generated_at }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Strategi Kod</th>
                <th>Strategi Nama</th>
                <th>KPI Kod</th>
                <th>Inisiatif</th>
                <th>Petunjuk Prestasi</th>
                <th>Pelan Tindakan</th>
                <th>Sasaran</th>
                <th>Fasa 1</th>
                <th>Fasa 2</th>
                <th>Fasa 3</th>
                <th>Fasa 4</th>
                <th>Pencapaian</th>
                <th>Peratus (%)</th>
                <th>Maklumat Pencapaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($strategies as $strategy)
                @foreach($strategy->kpiRecords as $kpi)
                    @php
                        $kpiYear = $kpi->kpiYears->first();
                        $phases = $kpiYear ? $kpiYear->phases->keyBy('phase') : collect();
                        
                        $parseValue = function($value) {
                            if ($value === null || $value === '') return 0;
                            $cleaned = str_replace(['RM', ',', ' '], '', (string) $value);
                            return is_numeric($cleaned) ? floatval($cleaned) : 0;
                        };
                        
                        $phase1 = $parseValue($phases->get('Phase 1')->achievement ?? 0);
                        $phase2 = $parseValue($phases->get('Phase 2')->achievement ?? 0);
                        $phase3 = $parseValue($phases->get('Phase 3')->achievement ?? 0);
                        $phase4 = $parseValue($phases->get('Phase 4')->achievement ?? 0);
                        $totalAchievement = $phase1 + $phase2 + $phase3 + $phase4;
                        $targetValue = $kpiYear ? $kpiYear->target_value : 0;
                        $percentage = $kpiYear ? $kpiYear->achievement_percentage : 0;
                        $achievementInfo = $kpiYear ? $kpiYear->achievement_information : '';
                    @endphp
                    <tr>
                        <td>{{ $strategy->strategy_code }}</td>
                        <td>{{ $strategy->strategy_name }}</td>
                        <td>{{ $kpi->kpi_code }}</td>
                        <td>{{ $kpi->initiative }}</td>
                        <td>{{ $kpi->performance_indicator }}</td>
                        <td>{{ $kpi->action_plan }}</td>
                        <td>{{ $targetValue }}</td>
                        <td>{{ $phase1 }}</td>
                        <td>{{ $phase2 }}</td>
                        <td>{{ $phase3 }}</td>
                        <td>{{ $phase4 }}</td>
                        <td>{{ $totalAchievement }}</td>
                        <td>{{ number_format($percentage, 1) }}%</td>
                        <td>{{ $achievementInfo }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
