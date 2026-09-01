<table>
    <thead>
        <tr>
            <th colspan="3">Award Statistics Report - {{ $year }}</th>
        </tr>
        <tr>
            <th>Award Level</th>
            <th>Count</th>
            <th>Percentage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($levelDistribution as $level)
            <tr>
                <td>{{ $level->award_level }}</td>
                <td>{{ $level->count }}</td>
                <td>{{ $stats['total_awards'] > 0 ? round(($level->count / $stats['total_awards']) * 100, 2) : 0 }}%</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Generated on: {{ $generated_at }}</td>
        </tr>
    </tfoot>
</table>
