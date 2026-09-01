<table>
    <thead>
        <tr>
            <th colspan="11">Faculty Performance Report - {{ $year }}</th>
        </tr>
        <tr>
            <th>Faculty Code</th>
            <th>Faculty Name</th>
            <th>Total Staff</th>
            <th>Total Awards</th>
            <th>Gold Awards</th>
            <th>Silver Awards</th>
            <th>Bronze Awards</th>
            <th>Unique Staff Participated</th>
            <th>Unique Projects</th>
            <th>Awards per Staff</th>
            <th>Participation Rate (%)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($facultyStats as $stat)
            <tr>
                <td>{{ $stat['faculty']->faculty_code }}</td>
                <td>{{ $stat['faculty']->faculty_name }}</td>
                <td>{{ $stat['faculty']->staff_count }}</td>
                <td>{{ $stat['total_awards'] }}</td>
                <td>{{ $stat['gold_awards'] }}</td>
                <td>{{ $stat['silver_awards'] }}</td>
                <td>{{ $stat['bronze_awards'] }}</td>
                <td>{{ $stat['unique_staff'] }}</td>
                <td>{{ $stat['unique_projects'] }}</td>
                <td>{{ $stat['awards_per_staff'] }}</td>
                <td>{{ $stat['participation_rate'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="11">Generated on: {{ $generated_at }}</td>
        </tr>
    </tfoot>
</table>
