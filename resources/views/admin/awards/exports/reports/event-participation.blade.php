<table>
    <thead>
        <tr>
            <th colspan="14">Event Participation Report - {{ $year }}</th>
        </tr>
        <tr>
            <th>Event Name</th>
            <th>Location</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Duration (Days)</th>
            <th>Total Awards</th>
            <th>Unique Participants</th>
            <th>Unique Projects</th>
            <th>Gold Awards</th>
            <th>Silver Awards</th>
            <th>Bronze Awards</th>
            <th>Faculties Involved</th>
            <th>Awards per Day</th>
            <th>Participation Efficiency (%)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($eventStats as $stat)
            <tr>
                <td>{{ $stat['event']->event_name }}</td>
                <td>{{ $stat['event']->exhibition_place ?? 'N/A' }}</td>
                <td>{{ $stat['event']->start_date ? \Carbon\Carbon::parse($stat['event']->start_date)->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $stat['event']->end_date ? \Carbon\Carbon::parse($stat['event']->end_date)->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $stat['duration_days'] }}</td>
                <td>{{ $stat['total_awards'] }}</td>
                <td>{{ $stat['unique_participants'] }}</td>
                <td>{{ $stat['unique_projects'] }}</td>
                <td>{{ $stat['gold_awards'] }}</td>
                <td>{{ $stat['silver_awards'] }}</td>
                <td>{{ $stat['bronze_awards'] }}</td>
                <td>{{ $stat['faculties_involved'] }}</td>
                <td>{{ $stat['awards_per_day'] }}</td>
                <td>{{ $stat['participation_efficiency'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="14">Generated on: {{ $generated_at }}</td>
        </tr>
    </tfoot>
</table>
