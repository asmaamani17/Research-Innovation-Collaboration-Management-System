<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Awards Report & Analytics</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            color: #0f172a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 12px;
            color: #0284c7;
            margin: 0 0 4px 0;
            font-weight: normal;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .meta-table td {
            font-size: 10px;
            padding: 4px 8px;
        }
        .summary-cards {
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-card {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        .summary-card .number {
            font-size: 18px;
            font-weight: bold;
            color: #0284c7;
        }
        .summary-card .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 7px 8px;
            border: 1px solid #0f172a;
        }
        table.data-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            word-wrap: break-word;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-gold { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-silver { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-bronze { background-color: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
        .badge-other { background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITI TEKNIKAL MALAYSIA MELAKA</h1>
        <h2>Anugerah Penyelidikan Management System — Award Statistics & Report</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Year Filter:</strong> {{ $filterInfo['year'] }}</td>
            <td><strong>Faculty Filter:</strong> {{ $filterInfo['faculty'] }}</td>
            <td><strong>Level Filter:</strong> {{ $filterInfo['level'] }}</td>
            <td><strong>Generated On:</strong> {{ $filterInfo['generated_at'] }}</td>
        </tr>
    </table>

    <table class="summary-cards" cellspacing="6">
        <tr>
            <td width="20%" class="summary-card">
                <div class="number">{{ $summary['total_awards'] }}</div>
                <div class="label">Total Awards</div>
            </td>
            <td width="20%" class="summary-card">
                <div class="number">{{ $summary['gold_count'] }}</div>
                <div class="label">Gold Awards</div>
            </td>
            <td width="20%" class="summary-card">
                <div class="number">{{ $summary['silver_count'] }}</div>
                <div class="label">Silver Awards</div>
            </td>
            <td width="20%" class="summary-card">
                <div class="number">{{ $summary['bronze_card'] ?? $summary['bronze_count'] }}</div>
                <div class="label">Bronze Awards</div>
            </td>
            <td width="20%" class="summary-card">
                <div class="number">{{ $summary['unique_staff'] }}</div>
                <div class="label">Staff Members</div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 12px; color: #0f172a; margin-top: 15px; margin-bottom: 6px;">Awards Detailed List ({{ $awards->count() }} records)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">#</th>
                <th width="26%">Award Name</th>
                <th width="10%">Level</th>
                <th width="18%">Staff Name</th>
                <th width="12%">Faculty</th>
                <th width="18%">Project</th>
                <th width="12%">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($awards as $index => $award)
                @php
                    $levelLower = strtolower($award->award_level ?? '');
                    $badgeClass = 'badge-other';
                    if (str_contains($levelLower, 'gold')) $badgeClass = 'badge-gold';
                    elseif (str_contains($levelLower, 'silver')) $badgeClass = 'badge-silver';
                    elseif (str_contains($levelLower, 'bronze')) $badgeClass = 'badge-bronze';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $award->award_name }}</strong></td>
                    <td><span class="badge {{ $badgeClass }}">{{ $award->award_level ?? 'N/A' }}</span></td>
                    <td>{{ $award->staff->staff_name ?? 'N/A' }}</td>
                    <td>{{ $award->staff->faculty->faculty_code ?? ($award->staff->faculty->faculty_name ?? 'N/A') }}</td>
                    <td>{{ $award->project->project_title ?? 'N/A' }}</td>
                    <td>{{ $award->award_date ? \Carbon\Carbon::parse($award->award_date)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b; padding: 15px;">No award records match the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated automatically by Anugerah Penyelidikan Management System
    </div>
</body>
</html>
