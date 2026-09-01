<table>
    <thead>
        <tr>
            <th colspan="3">Summary Report - {{ $year }}</th>
        </tr>
        <tr>
            <th>Metric</th>
            <th>Value</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Awards</td>
            <td>{{ $summary['total_awards'] }}</td>
            <td>All time total number of awards</td>
        </tr>
        <tr>
            <td>Total Staff</td>
            <td>{{ $summary['total_staff'] }}</td>
            <td>Total number of staff members</td>
        </tr>
        <tr>
            <td>Total Projects</td>
            <td>{{ $summary['total_projects'] }}</td>
            <td>Total number of projects</td>
        </tr>
        <tr>
            <td>Total Events</td>
            <td>{{ $summary['total_events'] }}</td>
            <td>Total number of events</td>
        </tr>
        <tr>
            <td>Total Faculties</td>
            <td>{{ $summary['total_faculties'] }}</td>
            <td>Total number of faculties</td>
        </tr>
        <tr>
            <td>This Year Awards</td>
            <td>{{ $summary['this_year_awards'] }}</td>
            <td>Awards received in {{ date('Y') }}</td>
        </tr>
        <tr>
            <td>Last Year Awards</td>
            <td>{{ $summary['last_year_awards'] }}</td>
            <td>Awards received in {{ date('Y') - 1 }}</td>
        </tr>
        @php
            $growthRate = $summary['last_year_awards'] > 0 ?
                round((($summary['this_year_awards'] - $summary['last_year_awards']) / $summary['last_year_awards']) * 100, 1) : 0;
        @endphp
        <tr>
            <td>Growth Rate</td>
            <td>{{ $growthRate }}%</td>
            <td>Year-over-year growth rate</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Generated on: {{ $generated_at }}</td>
        </tr>
    </tfoot>
</table>
