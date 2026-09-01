<table>
    <thead>
        <tr>
            <th colspan="3">Comprehensive Report - {{ $year }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3"><strong>Executive Summary</strong></td>
        </tr>
        <tr>
            <td>Total Awards</td>
            <td>{{ $executiveSummary['total_awards'] }}</td>
            <td>Total number of awards in {{ $year }}</td>
        </tr>
        <tr>
            <td>Total Staff Participated</td>
            <td>{{ $executiveSummary['total_staff_participated'] }}</td>
            <td>Unique staff members who received awards</td>
        </tr>
        <tr>
            <td>Total Projects Featured</td>
            <td>{{ $executiveSummary['total_projects_featured'] }}</td>
            <td>Unique projects that received awards</td>
        </tr>
        <tr>
            <td>Total Events Held</td>
            <td>{{ $executiveSummary['total_events_held'] }}</td>
            <td>Total number of events in {{ $year }}</td>
        </tr>
        <tr>
            <td>Total Faculties Involved</td>
            <td>{{ $executiveSummary['total_faculties_involved'] }}</td>
            <td>Faculties with award recipients</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Faculty Performance</strong></td>
        </tr>
        <tr>
            <th>Faculty Name</th>
            <th>Total Awards</th>
            <th>Gold Awards</th>
        </tr>
        @foreach($facultyPerformance as $faculty)
            <tr>
                <td>{{ $faculty['faculty_name'] }}</td>
                <td>{{ $faculty['total_awards'] }}</td>
                <td>{{ $faculty['gold_awards'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Generated on: {{ $generated_at }}</td>
        </tr>
    </tfoot>
</table>