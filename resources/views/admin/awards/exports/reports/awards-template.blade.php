@if(isset($template_type) && $template_type === 'Template_RD8_1_New')
{{-- Template_RD8_1_New - Tab-separated format --}}
<table>
    <thead>
        <tr>
            <th>Id_Recognation</th>
            <th>ProjectID</th>
            <th>Person_RefNo</th>
            <th>PersonTypeID</th>
            <th>FullName</th>
        </tr>
    </thead>
    <tbody>
        @foreach($awards as $index => $award)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $award->project->project_id ?? 'N/A' }}</td>
                <td>{{ $award->staff->staff_id ?? 'N/A' }}</td>
                <td>{{ $award->staff->PersonTypeID ?? '1' }}</td>
                <td>{{ $award->staff->staff_name ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@elseif(isset($template_type) && $template_type === 'Template_RD8_New')
{{-- Template_RD8_New --}}
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>ProjectID</th>
            <th>ProjectTitle</th>
            <th>ExhibitionName</th>
            <th>ExhibitionLevelID</th>
            <th>ExhibitionResultID</th>
            <th>ExhibitionPlace</th>
            <th>StartDate</th>
            <th>EndDate</th>
            <th>Amount</th>
            <th>Invention_Award</th>
        </tr>
    </thead>
    <tbody>
        @foreach($awards as $index => $award)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $award->project->project_id ?? 'N/A' }}</td>
                <td>{{ $award->project->project_title ?? 'N/A' }}</td>
                <td>{{ $award->event->event_name ?? 'N/A' }}</td>
                <td>{{ $award->event->exhibition_level_id ?? 'N/A' }}</td>
                <td>{{ $award->exhibition_result_id ?? 'N/A' }}</td>
                <td>{{ $award->event->exhibition_place ?? 'N/A' }}</td>
                <td>{{ $award->event->start_date ? \Carbon\Carbon::parse($award->event->start_date)->format('d-m-Y') : 'N/A' }}</td>
                <td>{{ $award->event->end_date ? \Carbon\Carbon::parse($award->event->end_date)->format('d-m-Y') : 'N/A' }}</td>
                <td>{{ $award->amount ? 'RM' . number_format($award->amount, 2) : 'RM0.00' }}</td>
                <td>1</td>
            </tr>
        @endforeach
    </tbody>
</table>
@elseif(isset($template_type) && $template_type === 'myMOHE')
{{-- myMOHE Template --}}
<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>PROJECT ID</th>
            <th>STAFF ID</th>
            <th>STAFF NAME</th>
            <th>FACULTY</th>
            <th>NAME OF AWARD</th>
            <th>LEVEL OF AWARD</th>
            <th>TYPE OF AWARD</th>
            <th>ORGANIZER</th>
            <th>EXIBITION LEVEL</th>
            <th>TITLE OF INVENTION</th>
            <th>EVENT</th>
            <th>DATE AWARDS (dd-mm-yyyy)</th>
            <th>LINK TO EVIDENCE</th>
        </tr>
    </thead>
    <tbody>
        @foreach($awards as $index => $award)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $award->project->project_id ?? 'N/A' }}</td>
                <td>{{ $award->staff->staff_id ?? 'N/A' }}</td>
                <td>{{ $award->staff->staff_name ?? 'N/A' }}</td>
                <td>{{ $award->staff->faculty->faculty_name ?? 'N/A' }}</td>
                <td>{{ $award->award_name ?? 'N/A' }}</td>
                <td>{{ $award->award_level ?? 'N/A' }}</td>
                <td>{{ $award->award_type ?? 'N/A' }}</td>
                <td>{{ $award->event->organizer ?? 'N/A' }}</td>
                <td>{{ $award->event->exhibition_level ?? 'N/A' }}</td>
                <td>{{ $award->project->project_title ?? 'N/A' }}</td>
                <td>{{ $award->event->event_name ?? 'N/A' }}</td>
                <td>{{ $award->award_date ? \Carbon\Carbon::parse($award->award_date)->format('d-m-Y') : 'N/A' }}</td>
                <td>{{ $award->evidence_document ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@else
{{-- myRA Template --}}
<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>PROJECT ID</th>
            <th>GRANT NO</th>
            <th>STAFF ID</th>
            <th>STAFF NAME</th>
            <th>FACULTY</th>
            <th>NAME OF AWARD</th>
            <th>ORGANIZER</th>
            <th>EXIBITION LEVEL</th>
            <th>TITLE OF INVENTION</th>
            <th>EVENT</th>
            <th>EXIBITION PLACE</th>
            <th>START DATE (dd-mm-yyyy)</th>
            <th>END DATE (dd-mm-yyyy)</th>
            <th>AMOUNT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($awards as $index => $award)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $award->project->project_id ?? 'N/A' }}</td>
                <td>{{ $award->project->grant_no ?? 'N/A' }}</td>
                <td>{{ $award->staff->staff_id ?? 'N/A' }}</td>
                <td>{{ $award->staff->staff_name ?? 'N/A' }}</td>
                <td>{{ $award->staff->faculty->faculty_name ?? 'N/A' }}</td>
                <td>{{ $award->award_name ?? 'N/A' }}</td>
                <td>{{ $award->event->organizer ?? 'N/A' }}</td>
                <td>{{ $award->event->exhibition_level ?? 'N/A' }}</td>
                <td>{{ $award->project->project_title ?? 'N/A' }}</td>
                <td>{{ $award->event->event_name ?? 'N/A' }}</td>
                <td>{{ $award->event->exhibition_place ?? 'N/A' }}</td>
                <td>{{ $award->event->start_date ? \Carbon\Carbon::parse($award->event->start_date)->format('d-m-Y') : 'N/A' }}</td>
                <td>{{ $award->event->end_date ? \Carbon\Carbon::parse($award->event->end_date)->format('d-m-Y') : 'N/A' }}</td>
                <td>{{ $award->amount ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
