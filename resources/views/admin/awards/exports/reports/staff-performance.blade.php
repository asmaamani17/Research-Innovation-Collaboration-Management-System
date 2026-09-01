<table>
    <thead>
        <tr>
            <th colspan="10">Staff Performance Report - {{ $year }}</th>
        </tr>
        <tr>
            <th>Staff ID</th>
            <th>Staff Name</th>
            <th>Faculty</th>
            <th>Total Awards</th>
            <th>Gold Awards</th>
            <th>Silver Awards</th>
            <th>Bronze Awards</th>
            <th>Unique Projects</th>
            <th>Latest Award</th>
            <th>Recent Award</th>
        </tr>
    </thead>
    <tbody>
        @foreach($staffStats as $stat)
            <tr>
                <td>{{ $stat['staff']->staff_id }}</td>
                <td>{{ $stat['staff']->staff_name }}</td>
                <td>{{ $stat['staff']->faculty->faculty_name ?? 'Unknown' }}</td>
                <td>{{ $stat['total_awards'] }}</td>
                <td>{{ $stat['gold_awards'] }}</td>
                <td>{{ $stat['silver_awards'] }}</td>
                <td>{{ $stat['bronze_awards'] }}</td>
                <td>{{ $stat['unique_projects'] }}</td>
                <td>{{ $stat['highest_award'] ?? 'None' }}</td>
                <td>{{ $stat['recent_award'] ?? 'Never' }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10">Generated on: {{ $generated_at }}</td>
        </tr>
    </tfoot>
</table>
